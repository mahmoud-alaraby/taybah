<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\TimeTracking;
use App\Models\EmployeeAttendance;
use App\Models\DailyWorkSummary;
use App\Models\Admin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminProjectTrackingController extends Controller
{
    public function index()
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        // المشاريع التي يعمل عليها الأدمن
        $activeProjects = Project::active()
            ->where(function ($query) use ($adminId) {
                $query->whereHas('tasks', function ($q) use ($adminId) {
                    $q->where('assigned_to', $adminId)->where('assigned_to_type', 'admin');
                })->orWhere(function ($q) use ($adminId) {
                    $q->where('created_by', $adminId)->where('created_by_type', 'admin');
                });
            })
            ->with(['tasks' => function ($q) use ($adminId) {
                $q->where(function ($query) use ($adminId) {
                    $query->where('assigned_to', $adminId)->where('assigned_to_type', 'admin')
                        ->orWhereNull('assigned_to');
                })
                    ->orderBy('status')
                    ->orderBy('created_at', 'desc');
            }])
            ->get();

        // إذا لم توجد مشاريع مخصصة للأدمن، اجعله يرى المشاريع التي أنشأها
        if ($activeProjects->isEmpty()) {
            $activeProjects = Project::active()
                ->where('created_by', $adminId)
                ->where('created_by_type', 'admin')
                ->with(['tasks' => function ($q) use ($adminId) {
                    $q->where(function ($query) use ($adminId) {
                        $query->where('assigned_to', $adminId)->where('assigned_to_type', 'admin')
                            ->orWhereNull('assigned_to');
                    })
                        ->orderBy('status')
                        ->orderBy('created_at', 'desc');
                }])
                ->get();
        }

        // حالة البصمة اليوم
        $todayAttendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        // الـ Timer النشط
        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->with(['project', 'task'])
            ->first();

        // إحصائيات اليوم
        $personalStats = [
            'total_hours' => TimeTracking::where('employee_id', $adminId)
                ->where('employee_type', 'admin')
                ->whereDate('date', $today)
                ->sum('total_seconds') / 3600,
            'target_percentage' => 0,
            'projects_worked' => TimeTracking::where('employee_id', $adminId)
                ->where('employee_type', 'admin')
                ->whereDate('date', $today)
                ->distinct('project_id')
                ->count(),
        ];

        if ($personalStats['total_hours'] > 0) {
            $personalStats['target_percentage'] = min(100, ($personalStats['total_hours'] / 7) * 100);
        }

        $todayStats = $personalStats;

        return view('admin.project-tracking.index', compact(
            'activeProjects',
            'todayAttendance',
            'activeTimer',
            'todayStats'
        ));
    }

    public function startTimer(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:project_tasks,id',
            'description' => 'nullable|string|max:500',
        ]);

        $adminId = auth('admin')->id();

        // التأكد من أن المهمة مخصصة للأدمن أو متاحة له
        $task = ProjectTask::where('id', $request->task_id)
            ->where(function ($q) use ($adminId) {
                $q->where(function ($query) use ($adminId) {
                    $query->where('assigned_to', $adminId)->where('assigned_to_type', 'admin');
                })->orWhereNull('assigned_to')
                    ->orWhereHas('project', function ($q2) use ($adminId) {
                        $q2->where('created_by', $adminId)->where('created_by_type', 'admin');
                    });
            })
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'هذه المهمة غير متاحة لك'
            ], 403);
        }

        // إيقاف أي timer نشط
        TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->each(function ($timer) {
                $timer->stopTimer();
            });

        // تحديد session number
        $sessionNumber = TimeTracking::where('task_id', $request->task_id)->max('session_number') + 1;

        // تحديث حالة المهمة إلى "قيد التنفيذ"
        $task->update(['status' => 'in_progress']);

        $timer = TimeTracking::create([
            'employee_id' => $adminId,
            'employee_type' => 'admin',
            'project_id' => $request->project_id,
            'task_id' => $request->task_id,
            'start_time' => now(),
            'description' => $request->description,
            'date' => Carbon::today(),
            'is_active' => true,
            'is_paused' => false,
            'pause_count' => 0,
            'resume_count' => 0,
            'session_number' => $sessionNumber,
            'pause_resume_log' => [],
            'total_seconds' => 0,
            'is_editable' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم بدء العداد بنجاح',
            'timer' => $timer->load(['project', 'task']),
        ]);
    }


    public function resumeTimer(Request $request)
    {
        $adminId = auth('admin')->id();

        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->first();

        if (!$activeTimer) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد عداد نشط'
            ], 400);
        }

        if (!$activeTimer->is_paused) {
            return response()->json([
                'success' => false,
                'message' => 'العداد غير متوقف'
            ], 400);
        }

        $resumeTime = now();

        // Log resume
        $pauseLog = $activeTimer->pause_resume_log ?? [];
        $pauseLog[] = [
            'action' => 'resume',
            'time' => $resumeTime->toISOString()
        ];

        // الحل: تحديث start_time بحيث يعكس الوقت المحفوظ
        // نحسب كم الوقت المفروض يكون منقضي لو بدأنا من الآن
        $savedSeconds = $activeTimer->total_seconds; // الوقت المحفوظ عند الإيقاف
        $newStartTime = $resumeTime->copy()->subSeconds($savedSeconds);

        $activeTimer->update([
            'start_time' => $newStartTime, // هنا الحل الأساسي
            'is_paused' => false,
            'resume_count' => $activeTimer->resume_count + 1,
            'pause_resume_log' => $pauseLog
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم استئناف العداد بنجاح',
            'timer' => $activeTimer->fresh(),
            'resume_count' => $activeTimer->resume_count,
            'is_paused' => $activeTimer->is_paused
        ]);
    }
    public function stopTimer(Request $request)
    {
        $adminId = auth('admin')->id();

        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->first();

        if (!$activeTimer) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد عداد نشط'
            ], 400);
        }

        $activeTimer->stopTimer();

        return response()->json([
            'success' => true,
            'message' => 'تم إنهاء العداد بنجاح',
            'timer' => $activeTimer->fresh(),
            'duration' => $activeTimer->formatted_duration,
            'hours' => round($activeTimer->total_seconds / 3600, 2),
            'session_summary' => $activeTimer->session_summary
        ]);
    }

    public function restartTimer(Request $request)
    {
        $request->validate([
            'timer_id' => 'required|exists:time_trackings,id',
            'description' => 'nullable|string|max:500'
        ]);

        $adminId = auth('admin')->id();

        $timer = TimeTracking::where('id', $request->timer_id)
            ->where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->first();

        if (!$timer) {
            return response()->json([
                'success' => false,
                'message' => 'Timer غير موجود'
            ], 404);
        }

        if ($timer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'العداد نشط بالفعل'
            ], 400);
        }

        // إيقاف أي timer نشط آخر
        TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->each(function ($activeTimer) {
                $activeTimer->stopTimer();
            });

        // حساب start_time الجديد بحيث يكمل من الوقت المحفوظ
        $now = now();
        $savedSeconds = $timer->total_seconds; // الوقت المحفوظ
        $newStartTime = $now->copy()->subSeconds($savedSeconds);

        // Log restart في pause_resume_log
        $pauseLog = $timer->pause_resume_log ?? [];
        $pauseLog[] = [
            'action' => 'restart_continue',
            'time' => $now->toISOString(),
            'continued_from_seconds' => $savedSeconds
        ];

        // تحديث نفس الـ timer بدلاً من إنشاء واحد جديد
        $timer->update([
            'start_time' => $newStartTime, // البداية محسوبة بحيث تعكس الوقت المحفوظ
            'end_time' => null,
            'is_active' => true,
            'is_paused' => false,
            'pause_resume_log' => $pauseLog,
            'description' => $request->description ?? $timer->description . ' - استكمال',
            // نحتفظ بـ session_number نفسه (مش +1)
            // نحتفظ بـ total_seconds المحفوظ
        ]);

        // تحديث حالة المهمة إلى "قيد التنفيذ"
        if ($timer->task) {
            $timer->task->update(['status' => 'in_progress']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم استكمال العداد من حيث توقف',
            'timer' => $timer->fresh()->load(['project', 'task']),
            'session_number' => $timer->session_number, // نفس الجلسة
            'continued_from' => $this->formatHoursMinutes($savedSeconds / 3600),
            'total_duration' => $timer->formatted_duration
        ]);
    }

    public function editTimer(Request $request)
    {
        $request->validate([
            'timer_id' => 'required|exists:time_trackings,id',
            'hours' => 'required|numeric|min:0|max:24',
            'minutes' => 'required|integer|min:0|max:59'
        ]);

        $adminId = auth('admin')->id();

        $timer = TimeTracking::where('id', $request->timer_id)
            ->where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->first();

        if (!$timer) {
            return response()->json([
                'success' => false,
                'message' => 'Timer غير موجود'
            ], 404);
        }

        $newSeconds = ($request->hours * 3600) + ($request->minutes * 60);
        $success = $timer->editTime($newSeconds, $adminId, 'admin');

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل هذا Timer'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الوقت بنجاح',
            'timer' => $timer->fresh(),
            'new_duration' => $timer->formatted_duration,
            'original_duration' => $this->formatHoursMinutes(($timer->original_seconds ?? 0) / 3600)
        ]);
    }

    public function getTimerDetails(Request $request)
    {
        $request->validate([
            'timer_id' => 'required|exists:time_trackings,id'
        ]);

        $adminId = auth('admin')->id();

        $timer = TimeTracking::where('id', $request->timer_id)
            ->where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->with(['project', 'task'])
            ->first();

        if (!$timer) {
            return response()->json([
                'success' => false,
                'message' => 'Timer غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'timer' => $timer,
            'session_summary' => $timer->session_summary,
            'pause_resume_log' => $timer->pause_resume_log,
            'total_pause_time' => $this->formatHoursMinutes($timer->total_pause_time / 3600),
            'is_edited' => $timer->is_edited,
            'original_duration' => $timer->original_seconds ?
                $this->formatHoursMinutes($timer->original_seconds / 3600) : null
        ]);
    }

    public function getActiveTimer()
    {
        $adminId = auth('admin')->id();

        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->with(['project', 'task'])
            ->first();

        if ($activeTimer) {
            $currentSeconds = 0;

            if ($activeTimer->is_paused) {
                $currentSeconds = $activeTimer->total_seconds;
            } else {
                $currentSeconds = $activeTimer->calculateTotalSeconds(now());
            }

            return response()->json([
                'active' => true,
                'timer' => $activeTimer,
                'current_seconds' => $currentSeconds,
                'formatted_time' => $this->formatHoursMinutes($currentSeconds / 3600),
                'is_paused' => $activeTimer->is_paused,
                'pause_count' => $activeTimer->pause_count,
                'resume_count' => $activeTimer->resume_count,
                'session_number' => $activeTimer->session_number
            ]);
        }

        return response()->json(['active' => false]);
    }

    public function todayTimeEntries()
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        $entries = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->with(['project', 'task'])
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'project' => [
                        'name' => $entry->project->name ?? 'مشروع محذوف',
                    ],
                    'task' => [
                        'name' => $entry->task->name ?? 'مهمة محذوفة',
                    ],
                    'start_time' => $entry->start_time->format('H:i'),
                    'end_time' => $entry->end_time ? $entry->end_time->format('H:i') : 'جاري',
                    'formatted_duration' => $entry->formatted_duration,
                    'hours' => round($entry->total_seconds / 3600, 2),
                    'is_active' => $entry->is_active,
                    'is_paused' => $entry->is_paused,
                    'pause_count' => $entry->pause_count,
                    'resume_count' => $entry->resume_count,
                    'session_number' => $entry->session_number,
                    'is_editable' => $entry->is_editable,
                    'is_edited' => $entry->is_edited,
                    'can_restart' => !$entry->is_active
                ];
            });

        $totalHours = $entries->sum('hours');
        $targetPercentage = $totalHours > 0 ? min(100, ($totalHours / 7) * 100) : 0;

        return response()->json([
            'entries' => $entries,
            'total_hours' => round($totalHours, 2),
            'target_percentage' => round($targetPercentage, 2),
            'formatted_total' => $this->formatHoursMinutes($totalHours),
        ]);
    }

    public function checkIn(Request $request)
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        $existingAttendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل حضورك مسبقاً اليوم'
            ], 400);
        }

        $checkInTime = now();
        $workStartTime = Carbon::today()->setHour(10)->setMinute(0);

        $isLate = $checkInTime->gt($workStartTime);
        $lateMinutes = $isLate ? $checkInTime->diffInMinutes($workStartTime) : 0;

        $attendance = EmployeeAttendance::create([
            'employee_id' => $adminId,
            'employee_type' => 'admin',
            'check_in_time' => $checkInTime,
            'date' => $today,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
        ]);

        $message = 'تم تسجيل الحضور بنجاح';
        if ($isLate) {
            $hours = floor($lateMinutes / 60);
            $minutes = $lateMinutes % 60;

            if ($hours > 0) {
                $lateText = "تأخير: {$hours} ساعة و {$minutes} دقيقة";
            } else {
                $lateText = "تأخير: {$minutes} دقيقة";
            }

            $message .= " - {$lateText}";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'attendance' => $attendance,
            'is_late' => $attendance->is_late,
            'late_minutes' => $attendance->late_minutes,
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:temporary,final'
        ]);

        $adminId = auth('admin')->id();
        $today = Carbon::today();
        $checkoutType = $request->input('type', 'final');

        $attendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على تسجيل حضور لهذا اليوم'
            ], 400);
        }

        if ($attendance->check_out_time && $attendance->checkout_type === 'final') {
            return response()->json([
                'success' => false,
                'message' => 'تم الانصراف النهائي مسبقاً'
            ], 400);
        }

        if ($checkoutType === 'temporary') {
            $result = $attendance->tempCheckOut();
            return response()->json($result);
        }

        $attendance->checkOut('final');
        DailyWorkSummary::generateForEmployee($adminId, $today, 'admin');

        $totalHoursFormatted = $this->formatHoursMinutes($attendance->total_hours);
        $overtimeFormatted = $this->formatHoursMinutes($attendance->overtime_hours);

        return response()->json([
            'success' => true,
            'message' => "تم الانصراف النهائي بنجاح - إجمالي العمل: {$totalHoursFormatted}",
            'attendance' => $attendance->fresh(),
            'total_hours' => $attendance->total_hours,
            'total_hours_formatted' => $totalHoursFormatted,
            'overtime_hours' => $attendance->overtime_hours,
            'overtime_formatted' => $overtimeFormatted,
        ]);
    }

    public function tempCheckOut(Request $request)
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على تسجيل حضور لهذا اليوم'
            ], 400);
        }

        $result = $attendance->tempCheckOut();
        return response()->json($result);
    }

    public function tempCheckIn(Request $request)
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على تسجيل حضور لهذا اليوم'
            ], 400);
        }

        $result = $attendance->tempCheckIn();
        return response()->json($result);
    }

    public function createProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $adminId = auth('admin')->id();

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'client_name' => $request->client_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => $adminId,
            'created_by_type' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المشروع بنجاح',
            'project' => $project,
        ]);
    }

    public function addTaskToProject(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'required|numeric|min:0',
        ]);

        $adminId = auth('admin')->id();

        $project = Project::where('id', $request->project_id)
            ->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                    ->where('created_by_type', 'admin')
                    ->orWhereHas('tasks', function ($q2) use ($adminId) {
                        $q2->where('assigned_to', $adminId)->where('assigned_to_type', 'admin');
                    });
            })
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للإضافة إلى هذا المشروع'
            ], 403);
        }

        $task = ProjectTask::create([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'estimated_hours' => $request->estimated_hours,
            'assigned_to' => $adminId,
            'assigned_to_type' => 'admin',
            'created_by' => $adminId,
            'created_by_type' => 'admin',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المهمة بنجاح',
            'task' => $task,
        ]);
    }

    private function formatHoursMinutes($hours)
    {
        if ($hours < 0) $hours = 0;

        $totalMinutes = round($hours * 60);
        $displayHours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $displayHours, $minutes);
    }

    public function pauseTimer(Request $request)
    {
        $adminId = auth('admin')->id();

        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->first();

        if (!$activeTimer) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد عداد نشط'
            ], 400);
        }

        // حساب الوقت المنقضي حتى الآن وحفظه
        $pauseTime = now();
        $totalSeconds = $activeTimer->start_time->diffInSeconds($pauseTime);

        // Log pause
        $pauseLog = $activeTimer->pause_resume_log ?? [];
        $pauseLog[] = [
            'action' => 'pause',
            'time' => $pauseTime->toISOString(),
            'total_seconds_at_pause' => $totalSeconds
        ];

        $activeTimer->update([
            'is_paused' => true,
            'pause_count' => $activeTimer->pause_count + 1,
            'pause_resume_log' => $pauseLog,
            'total_seconds' => $totalSeconds
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إيقاف العداد مؤقتاً',
            'timer' => $activeTimer->fresh(),
            'pause_count' => $activeTimer->pause_count,
            'is_paused' => $activeTimer->is_paused
        ]);
    }
}
