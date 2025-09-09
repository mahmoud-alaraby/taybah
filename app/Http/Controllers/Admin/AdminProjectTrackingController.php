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
            ->where(function($query) use ($adminId) {
                $query->whereHas('tasks', function($q) use ($adminId) {
                    $q->where('assigned_to', $adminId)->where('assigned_to_type', 'admin');
                })->orWhere(function($q) use ($adminId) {
                    $q->where('created_by', $adminId)->where('created_by_type', 'admin');
                });
            })
            ->with(['tasks' => function($q) use ($adminId) {
                $q->where(function($query) use ($adminId) {
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
                ->with(['tasks' => function($q) use ($adminId) {
                    $q->where(function($query) use ($adminId) {
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
                ->forDate($today)
                ->sum('total_seconds') / 3600,
            'target_percentage' => 0,
            'projects_worked' => TimeTracking::where('employee_id', $adminId)
                ->where('employee_type', 'admin')
                ->forDate($today)
                ->distinct('project_id')
                ->count(),
        ];

        if ($personalStats['total_hours'] > 0) {
            $personalStats['target_percentage'] = min(100, ($personalStats['total_hours'] / 7) * 100);
        }

        // إحصائيات عامة لجميع الموظفين (للمراقبة الإدارية)
        $todayStats = $this->getTodayStats();
        $todayStats['total_hours'] = $personalStats['total_hours']; // عرض الساعات الشخصية
        $todayStats['target_percentage'] = $personalStats['target_percentage']; // عرض النسبة الشخصية
        $todayStats['projects_worked'] = $personalStats['projects_worked']; // عرض المشاريع الشخصية

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
        
        // حساب التأخير
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

        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل انصرافك مسبقاً'
            ], 400);
        }

        // إيقاف أي timer نشط للأدمن
        TimeTracking::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        // تسجيل الانصراف
        $checkOutTime = now();
        $totalMinutes = $attendance->check_in_time->diffInMinutes($checkOutTime);
        $totalHours = round($totalMinutes / 60, 2);
        $standardWorkHours = 7;
        $overtimeHours = max(0, $totalHours - $standardWorkHours);

        $attendance->update([
            'check_out_time' => $checkOutTime,
            'total_hours' => $totalHours,
            'overtime_hours' => round($overtimeHours, 2),
        ]);

        // إنشاء ملخص اليوم
        DailyWorkSummary::generateForEmployee($adminId, $today, 'admin');

        // تنسيق الساعات والدقائق للعرض
        $totalHoursFormatted = $attendance->total_hours_formatted;
        $overtimeFormatted = sprintf('%d:%02d', 
            floor($overtimeHours), 
            round(($overtimeHours - floor($overtimeHours)) * 60)
        );

        return response()->json([
            'success' => true,
            'message' => "تم تسجيل الانصراف بنجاح - إجمالي العمل: {$totalHoursFormatted}",
            'attendance' => $attendance,
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
            ->where(function($q) use ($adminId) {
                $q->where(function($query) use ($adminId) {
                    $query->where('assigned_to', $adminId)->where('assigned_to_type', 'admin');
                })->orWhereNull('assigned_to')
                  ->orWhereHas('project', function($q2) use ($adminId) {
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
            ->each(function($timer) {
                $timer->stopTimer();
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
            'duration' => $this->formatSeconds($totalSeconds),
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
            'duration' => $this->formatSeconds($totalSeconds),
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
                'formatted_time' => $this->formatSeconds($currentSeconds),
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
            ->map(function($entry) {
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
                    'formatted_duration' => $this->formatSeconds($entry->total_seconds),
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
            'formatted_total' => $this->formatSeconds($entries->sum(function($entry) {
                return $entry['hours'] * 3600;
            })),
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
            ->where(function($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->where('created_by_type', 'admin')
                  ->orWhereHas('tasks', function($q2) use ($adminId) {
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

    public function reports(Request $request)
    {
        $adminId = auth('admin')->id();
        $type = $request->get('type', 'daily'); 
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        switch ($type) {
            case 'daily':
                return $this->getDailyReport($adminId, $date);
            case 'weekly':
                return $this->getWeeklyReport($adminId, $date);
            case 'monthly':
                return $this->getMonthlyReport($adminId, $date);
            default:
                return $this->getDailyReport($adminId, $date);
        }
    }

    private function getDailyReport($adminId, $date)
    {
        $summary = DailyWorkSummary::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->where('date', $date)
            ->first();

        if (!$summary) {
            // إنشاء ملخص جديد للتاريخ المحدد
            $timeEntries = TimeTracking::where('employee_id', $adminId)
                ->where('employee_type', 'admin')
                ->where('date', $date)
                ->with(['project', 'task'])
                ->get();

            $totalHours = $timeEntries->sum(function($entry) {
                return $entry->total_seconds / 3600;
            });
            
            $overtimeHours = max(0, $totalHours - 7);
            $targetPercentage = $totalHours > 0 ? min(100, ($totalHours / 7) * 100) : 0;

            $projectsWorked = $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
                $project = $entries->first()->project;
                return [
                    'project_id' => $projectId,
                    'project_name' => $project ? $project->name : 'مشروع محذوف',
                    'hours' => round($entries->sum(function($entry) {
                        return $entry->total_seconds / 3600;
                    }), 2),
                    'tasks' => $entries->map(function($entry) {
                        return [
                            'task_id' => $entry->task_id,
                            'task_name' => $entry->task ? $entry->task->name : 'مهمة محذوفة',
                            'hours' => round($entry->total_seconds / 3600, 2),
                        ];
                    })->toArray()
                ];
            })->values()->toArray();

            $summary = DailyWorkSummary::create([
                'employee_id' => $adminId,
                'employee_type' => 'admin',
                'date' => $date,
                'total_work_hours' => round($totalHours, 2),
                'overtime_hours' => round($overtimeHours, 2),
                'projects_worked' => $projectsWorked,
                'daily_target_percentage' => round($targetPercentage, 2),
            ]);
        }

        return response()->json([
            'type' => 'daily',
            'date' => $date,
            'summary' => $summary,
        ]);
    }

    private function getWeeklyReport($adminId, $date)
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        $summaries = DailyWorkSummary::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        $weeklyStats = [
            'total_hours' => $summaries->sum('total_work_hours'),
            'overtime_hours' => $summaries->sum('overtime_hours'),
            'average_daily_hours' => $summaries->avg('total_work_hours'),
            'days_worked' => $summaries->count(),
            'target_achievement' => $summaries->avg('daily_target_percentage'),
        ];

        return response()->json([
            'type' => 'weekly',
            'period' => $startOfWeek->format('Y-m-d') . ' إلى ' . $endOfWeek->format('Y-m-d'),
            'daily_summaries' => $summaries,
            'weekly_stats' => $weeklyStats,
        ]);
    }

    private function getMonthlyReport($adminId, $date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;

        $summaries = DailyWorkSummary::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->forMonth($year, $month)
            ->get();

        $attendances = EmployeeAttendance::where('employee_id', $adminId)
            ->where('employee_type', 'admin')
            ->forMonth($year, $month)
            ->get();

        $monthlyStats = [
            'total_hours' => $summaries->sum('total_work_hours'),
            'overtime_hours' => $summaries->sum('overtime_hours'),
            'days_worked' => $summaries->count(),
            'on_time_days' => $attendances->where('is_late', false)->count(),
            'late_days' => $attendances->where('is_late', true)->count(),
            'punctuality_percentage' => $attendances->count() > 0 
                ? round(($attendances->where('is_late', false)->count() / $attendances->count()) * 100, 2) 
                : 0,
            'average_daily_hours' => $summaries->avg('total_work_hours'),
            'target_achievement' => $summaries->avg('daily_target_percentage'),
        ];

        return response()->json([
            'type' => 'monthly',
            'period' => Carbon::create($year, $month)->format('Y-m'),
            'daily_summaries' => $summaries,
            'attendances' => $attendances,
            'monthly_stats' => $monthlyStats,
        ]);
    }

    private function formatSeconds($seconds)
    {
        if ($seconds < 0) $seconds = 0;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function employeeCheckIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $employeeId = $request->employee_id;
        $today = Carbon::today();

        // التحقق من عدم وجود بصمة حضور لليوم
        $existingAttendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('employee_type', 'employee')
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل حضور هذا الموظف مسبقاً اليوم'
            ], 400);
        }

        $checkInTime = now();
        $workStartTime = Carbon::today()->setHour(10)->setMinute(0); // 10:00 صباحاً
        
        // حساب التأخير
        $isLate = $checkInTime->gt($workStartTime);
        $lateMinutes = $isLate ? $checkInTime->diffInMinutes($workStartTime) : 0;

        $attendance = EmployeeAttendance::create([
            'employee_id' => $employeeId,
            'employee_type' => 'employee',
            'check_in_time' => $checkInTime,
            'date' => $today,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
        ]);

        $message = 'تم تسجيل حضور الموظف بنجاح';
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

    public function employeeCheckOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $employeeId = $request->employee_id;
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('employee_type', 'employee')
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على تسجيل حضور لهذا الموظف اليوم'
            ], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل انصراف هذا الموظف مسبقاً'
            ], 400);
        }

        // إيقاف أي timer نشط للموظف
        TimeTracking::where('employee_id', $employeeId)
            ->where('employee_type', 'employee')
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        // تسجيل الانصراف
        $checkOutTime = now();
        $totalMinutes = $attendance->check_in_time->diffInMinutes($checkOutTime);
        $totalHours = round($totalMinutes / 60, 2);
        $standardWorkHours = 7;
        $overtimeHours = max(0, $totalHours - $standardWorkHours);

        $attendance->update([
            'check_out_time' => $checkOutTime,
            'total_hours' => $totalHours,
            'overtime_hours' => round($overtimeHours, 2),
        ]);

        // إنشاء ملخص اليوم
        DailyWorkSummary::generateForEmployee($employeeId, $today, 'employee');

        // تنسيق الساعات والدقائق للعرض
        $totalHoursFormatted = $attendance->total_hours_formatted;
        $overtimeFormatted = sprintf('%d:%02d', 
            floor($overtimeHours), 
            round(($overtimeHours - floor($overtimeHours)) * 60)
        );

        return response()->json([
            'success' => true,
            'message' => "تم تسجيل انصراف الموظف بنجاح - إجمالي العمل: {$totalHoursFormatted}",
            'attendance' => $attendance,
            'total_hours' => $attendance->total_hours,
            'total_hours_formatted' => $totalHoursFormatted,
            'overtime_hours' => $attendance->overtime_hours,
            'overtime_formatted' => $overtimeFormatted,
        ]);
    }

    /**
     * إدارة العدادات للموظفين - من قبل الأدمن
     */
    public function startEmployeeTimer(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:project_tasks,id',
            'description' => 'nullable|string|max:500',
        ]);

        $employeeId = $request->employee_id;

        // إيقاف أي timer نشط للموظف
        TimeTracking::where('employee_id', $employeeId)
            ->where('employee_type', 'employee')
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        // تحديث حالة المهمة إلى "قيد التنفيذ"
        $task = ProjectTask::find($request->task_id);
        if ($task) {
            $task->update(['status' => 'in_progress']);
        }

        $timer = TimeTracking::create([
            'employee_id' => $employeeId,
            'employee_type' => 'employee',
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
            'message' => 'تم بدء العداد للموظف بنجاح',
            'timer' => $timer->load(['project', 'task', 'employee']),
        ]);
    }

    public function stopEmployeeTimer(Request $request)
    {
        $request->validate([
            'timer_id' => 'required|exists:time_trackings,id'
        ]);

        $timer = TimeTracking::find($request->timer_id);

        if (!$timer || !$timer->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'العداد غير نشط أو غير موجود'
            ], 400);
        }

        // حساب الوقت المنقضي
        $endTime = now();
        $totalSeconds = $timer->start_time->diffInSeconds($endTime);
        
        // تحديث البيانات
        $timer->update([
            'end_time' => $endTime,
            'total_seconds' => $totalSeconds,
            'is_active' => false,
        ]);

        // تحديث actual_hours في المهمة
        if ($timer->task) {
            $timer->task->increment('actual_hours', $totalSeconds / 3600);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إيقاف العداد بنجاح',
            'timer' => $timer->load(['employee']),
            'duration' => $this->formatSeconds($totalSeconds),
            'hours' => round($totalSeconds / 3600, 2),
        ]);
    }

    /**
     * الحصول على العدادات النشطة لجميع الموظفين
     */
    public function getActiveTimers()
    {
        $activeTimers = TimeTracking::where('is_active', true)
            ->with(['project', 'task', 'employee'])
            ->get()
            ->map(function($timer) {
                $currentSeconds = $timer->start_time->diffInSeconds(now());
                return [
                    'id' => $timer->id,
                    'employee' => [
                        'id' => $timer->employee_id,
                        'name' => $timer->employee ? $timer->employee->name : 'موظف محذوف',
                        'type' => $timer->employee_type
                    ],
                    'project' => [
                        'id' => $timer->project_id,
                        'name' => $timer->project ? $timer->project->name : 'مشروع محذوف'
                    ],
                    'task' => [
                        'id' => $timer->task_id,
                        'name' => $timer->task ? $timer->task->name : 'مهمة محذوفة'
                    ],
                    'start_time' => $timer->start_time->format('H:i'),
                    'current_seconds' => $currentSeconds,
                    'formatted_time' => $this->formatSeconds($currentSeconds)
                ];
            });

        return response()->json([
            'timers' => $activeTimers
        ]);
    }

    /**
     * الحصول على إحصائيات اليوم للأدمن
     */
    private function getTodayStats()
    {
        $today = Carbon::today();
        
        // عدد الموظفين الحاضرين
        $activeEmployees = EmployeeAttendance::where('date', $today)
            ->whereNull('check_out_time')
            ->count();
        
        // عدد العدادات النشطة
        $activeTimers = TimeTracking::where('is_active', true)
            ->count();
        
        // إجمالي ساعات العمل لليوم
        $totalHours = TimeTracking::forDate($today)
            ->sum('total_seconds') / 3600;
        
        // عدد المشاريع المُفعمل عليها اليوم
        $projectsWorked = TimeTracking::forDate($today)
            ->distinct('project_id')
            ->count();
        
        // نسبة تحقيق الهدف (بناءً على 7 ساعات عمل معيارية)
        $targetPercentage = $totalHours > 0 ? min(100, ($totalHours / ($activeEmployees * 7)) * 100) : 0;
        
        return [
            'active_employees' => $activeEmployees,
            'active_timers' => $activeTimers,
            'total_hours' => round($totalHours, 2),
            'projects_worked' => $projectsWorked,
            'target_percentage' => round($targetPercentage, 2),
        ];
    }

    public function getEmployeeTodayEntries(Request $request)
    {
        $today = Carbon::today();
        $employeeId = $request->get('employee_id');
        
        $query = TimeTracking::forDate($today)
            ->with(['project', 'task', 'employee']);
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId)
                  ->where('employee_type', 'employee');
        }
        
        $entries = $query->orderBy('start_time', 'desc')
            ->get()
            ->map(function($entry) {
                return [
                    'id' => $entry->id,
                    'employee' => [
                        'name' => $entry->employee ? $entry->employee->name : 'موظف محذوف'
                    ],
                    'project' => [
                        'name' => $entry->project ? $entry->project->name : 'مشروع محذوف',
                    ],
                    'task' => [
                        'name' => $entry->task ? $entry->task->name : 'مهمة محذوفة',
                    ],
                    'start_time' => $entry->start_time->format('H:i'),
                    'end_time' => $entry->end_time ? $entry->end_time->format('H:i') : null,
                    'formatted_duration' => $this->formatSeconds($entry->total_seconds),
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
            'formatted_total' => $this->formatSeconds($entries->sum(function($entry) {
                return $entry['hours'] * 3600;
            })),
        ]);
    }
}