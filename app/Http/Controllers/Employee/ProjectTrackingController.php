<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\TimeTracking;
use App\Models\EmployeeAttendance;
use App\Models\DailyWorkSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectTrackingController extends Controller
{
    public function index()
    {
        $employeeId = auth('employee')->id();
        $today = Carbon::today();

        // المشاريع التي يعمل عليها الموظف (له مهام فيها)
        $activeProjects = Project::active()
            ->whereHas('tasks', function($q) use ($employeeId) {
                $q->where('assigned_to', $employeeId);
            })
            ->with(['tasks' => function($q) use ($employeeId) {
                $q->where('assigned_to', $employeeId)
                  ->orderBy('status')
                  ->orderBy('created_at', 'desc');
            }])
            ->get();

        // إذا لم توجد مشاريع مخصصة للموظف، اجعله يرى المشاريع التي أنشأها
        if ($activeProjects->isEmpty()) {
            $activeProjects = Project::active()
                ->where('created_by', $employeeId)
                ->where('created_by_type', 'employee')
                ->with(['tasks' => function($q) use ($employeeId) {
                    $q->where('assigned_to', $employeeId)
                      ->orWhereNull('assigned_to')
                      ->orderBy('status')
                      ->orderBy('created_at', 'desc');
                }])
                ->get();
        }

        // حالة البصمة اليوم
        $todayAttendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        // الـ Timer النشط
        $activeTimer = TimeTracking::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->with(['project', 'task'])
            ->first();

        // إحصائيات اليوم
        $todayStats = [
            'total_hours' => TimeTracking::forEmployee($employeeId)
                ->forDate($today)
                ->sum('total_seconds') / 3600,
            'target_percentage' => 0,
            'projects_worked' => TimeTracking::forEmployee($employeeId)
                ->forDate($today)
                ->distinct('project_id')
                ->count(),
        ];

        if ($todayStats['total_hours'] > 0) {
            $todayStats['target_percentage'] = min(100, ($todayStats['total_hours'] / 7) * 100);
        }

        return view('employee.project-tracking.index', compact(
            'activeProjects', 'todayAttendance', 'activeTimer', 'todayStats'
        ));
    }

    public function checkIn(Request $request)
    {
        $employeeId = auth('employee')->id();
        $attendance = EmployeeAttendance::checkIn($employeeId);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الحضور بنجاح',
            'attendance' => $attendance,
            'is_late' => $attendance->is_late,
            'late_minutes' => $attendance->late_minutes,
        ]);
    }

    public function checkOut(Request $request)
    {
        $employeeId = auth('employee')->id();
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على تسجيل حضور لهذا اليوم'
            ], 400);
        }

        // إيقاف أي timer نشط
        TimeTracking::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        $attendance->checkOut();

        // إنشاء ملخص اليوم
        DailyWorkSummary::generateForEmployee($employeeId, $today);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الانصراف بنجاح',
            'attendance' => $attendance,
            'total_hours' => $attendance->total_hours,
            'overtime_hours' => $attendance->overtime_hours,
        ]);
    }

    public function startTimer(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:project_tasks,id',
            'description' => 'nullable|string|max:500',
        ]);

        $employeeId = auth('employee')->id();

        // التأكد من أن المهمة مخصصة للموظف أو متاحة له
        $task = ProjectTask::where('id', $request->task_id)
            ->where(function($q) use ($employeeId) {
                $q->where('assigned_to', $employeeId)
                  ->orWhereNull('assigned_to')
                  ->orWhereHas('project', function($q2) use ($employeeId) {
                      $q2->where('created_by', $employeeId)
                        ->where('created_by_type', 'employee');
                  });
            })
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'هذه المهمة غير متاحة لك'
            ], 403);
        }

        // إيقاف أي timer نشط للموظف
        TimeTracking::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        // تحديث حالة المهمة إلى "قيد التنفيذ"
        $task->update(['status' => 'in_progress']);

        $timer = TimeTracking::create([
            'employee_id' => $employeeId,
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
        $employeeId = auth('employee')->id();

        $activeTimer = TimeTracking::where('employee_id', $employeeId)
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
        $employeeId = auth('employee')->id();

        $activeTimer = TimeTracking::where('employee_id', $employeeId)
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
        $employeeId = auth('employee')->id();

        $activeTimer = TimeTracking::where('employee_id', $employeeId)
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
        $employeeId = auth('employee')->id();
        $today = Carbon::today();

        $entries = TimeTracking::where('employee_id', $employeeId)
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

        $employeeId = auth('employee')->id();

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'client_name' => $request->client_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'created_by' => $employeeId,
            'created_by_type' => 'employee',
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

        $employeeId = auth('employee')->id();
        
        // التأكد من أن المشروع متاح للموظف
        $project = Project::where('id', $request->project_id)
            ->where(function($q) use ($employeeId) {
                $q->where('created_by', $employeeId)
                  ->where('created_by_type', 'employee')
                  ->orWhereHas('tasks', function($q2) use ($employeeId) {
                      $q2->where('assigned_to', $employeeId);
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
            'assigned_to' => $employeeId, // تلقائياً يخصص للموظف الحالي
            'created_by' => $employeeId,
            'created_by_type' => 'employee',
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
        $employeeId = auth('employee')->id();
        $type = $request->get('type', 'daily'); 
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        switch ($type) {
            case 'daily':
                return $this->getDailyReport($employeeId, $date);
            case 'weekly':
                return $this->getWeeklyReport($employeeId, $date);
            case 'monthly':
                return $this->getMonthlyReport($employeeId, $date);
            default:
                return $this->getDailyReport($employeeId, $date);
        }
    }

    private function getDailyReport($employeeId, $date)
    {
        $summary = DailyWorkSummary::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();

        if (!$summary) {
            // إنشاء ملخص جديد للتاريخ المحدد
            $timeEntries = TimeTracking::where('employee_id', $employeeId)
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
                'employee_id' => $employeeId,
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

    private function getWeeklyReport($employeeId, $date)
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        $summaries = DailyWorkSummary::where('employee_id', $employeeId)
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

    private function getMonthlyReport($employeeId, $date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;

        $summaries = DailyWorkSummary::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->get();

        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
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


}