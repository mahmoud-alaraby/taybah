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

        // المشاريع التي يعمل عليها الأدمن (له مهام فيها أو أنشأها)
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

        // حالة البصمة اليوم للأدمن
        $todayAttendance = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $today)
            ->first();

        // الـ Timer النشط للأدمن
        $activeTimer = TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->with(['project', 'task'])
            ->first();

        // إحصائيات اليوم للأدمن الشخصي
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

        // قائمة الموظفين للإدارة
        $employees = \App\Models\Employee::where('status', 'active')->get(['id', 'name']);

        return view('admin.project-tracking.index', compact(
            'activeProjects',
            'todayAttendance',
            'activeTimer',
            'todayStats',
            'employees'
        ));
    }

    public function checkIn(Request $request)
    {
        $adminId = auth('admin')->id();
        $today = Carbon::today();

        // التحقق من عدم وجود بصمة حضور لليوم
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
        $workStartTime = Carbon::today()->setHour(10)->setMinute(0); // 10:00 صباحاً

        // حساب التأخير بالدقائق فقط
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
            'late_time_formatted' => $attendance->late_time_formatted,
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

        // إذا كان انصراف مؤقت
        if ($checkoutType === 'temporary') {
            $result = $attendance->tempCheckOut();
            return response()->json($result);
        }

        // انصراف نهائي
        $attendance->checkOut('final');

        // إنشاء ملخص اليوم
        DailyWorkSummary::generateForEmployee($adminId, $today, 'admin');

        // تنسيق الساعات والدقائق للعرض
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

        // إيقاف أي timer نشط للأدمن
        TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->each(function ($timer) {
                $endTime = now();
                $totalSeconds = $timer->start_time->diffInSeconds($endTime);

                $timer->update([
                    'end_time' => $endTime,
                    'total_seconds' => $totalSeconds,
                    'is_active' => false,
                ]);

                // تحديث actual_hours في المهمة
                if ($timer->task) {
                    $timer->task->increment('actual_hours', $totalSeconds / 3600);
                }
            });

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
            'total_seconds' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم بدء العداد بنجاح',
            'timer' => $timer->load(['project', 'task']),
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

        // حساب الوقت المنقضي
        $endTime = now();
        $totalSeconds = $activeTimer->start_time->diffInSeconds($endTime);

        // تحديث البيانات
        $activeTimer->update([
            'end_time' => $endTime,
            'total_seconds' => $totalSeconds,
            'is_active' => false,
        ]);

        // تحديث actual_hours في المهمة
        if ($activeTimer->task) {
            $activeTimer->task->increment('actual_hours', $totalSeconds / 3600);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إيقاف العداد بنجاح',
            'timer' => $activeTimer,
            'duration' => $this->formatHoursMinutes($totalSeconds / 3600),
            'hours' => round($totalSeconds / 3600, 2),
        ]);
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

        // حساب الوقت المنقضي حتى الآن
        $pauseTime = now();
        $totalSeconds = $activeTimer->start_time->diffInSeconds($pauseTime);

        // إيقاف مؤقت - حفظ الوقت المنقضي
        $activeTimer->update([
            'end_time' => $pauseTime,
            'total_seconds' => $totalSeconds,
            'is_active' => false,
        ]);

        // تحديث actual_hours في المهمة
        if ($activeTimer->task) {
            $activeTimer->task->increment('actual_hours', $totalSeconds / 3600);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إيقاف العداد مؤقتاً',
            'timer' => $activeTimer,
            'duration' => $this->formatHoursMinutes($totalSeconds / 3600),
            'hours' => round($totalSeconds / 3600, 2),
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
            // حساب الوقت الحالي
            $currentSeconds = $activeTimer->start_time->diffInSeconds(now());

            return response()->json([
                'active' => true,
                'timer' => $activeTimer,
                'current_seconds' => $currentSeconds,
                'formatted_time' => $this->formatHoursMinutes($currentSeconds / 3600),
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
                    'formatted_duration' => $this->formatHoursMinutes($entry->total_seconds / 3600),
                    'hours' => round($entry->total_seconds / 3600, 2),
                    'is_active' => $entry->is_active,
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

        // التأكد من أن المشروع متاح للأدمن
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
            'assigned_to' => $adminId, // تلقائياً يخصص للأدمن الحالي
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

    /**
     * تنسيق الوقت بالساعات والدقائق فقط
     */
    private function formatHoursMinutes($hours)
    {
        if ($hours < 0) $hours = 0;

        $totalMinutes = round($hours * 60);
        $displayHours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $displayHours, $minutes);
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


}
