<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeTracking;
use App\Models\Employee;
use App\Models\Project;
use App\Models\DailyWorkSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $projectId = $request->get('project_id');

        $query = TimeTracking::with(['employee', 'admin', 'project', 'task'])
            ->where('date', $date);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $timeEntries = $query->orderBy('start_time', 'desc')->get();

        // تجميع البيانات حسب الموظف والمشروع والمهمة
        $groupedEntries = $timeEntries->groupBy(function ($item) {
            $userId = $item->employee_id;
            $userType = $item->employee_type;
            $projectId = $item->project_id;
            $taskId = $item->task_id;
            
            return "{$userId}_{$userType}_{$projectId}_{$taskId}";
        })->map(function ($entries) {
            $firstEntry = $entries->first();
            $sessions = $entries->sortBy('start_time');
            
            // حساب الإحصائيات
            $totalHours = $entries->sum('hours');
            $totalSeconds = $entries->sum('total_seconds');
            $activeSessions = $entries->where('is_active', true)->count();
            
            return [
                'user' => $firstEntry->user,
                'project' => $firstEntry->project,
                'task' => $firstEntry->task,
                'sessions' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'start_time' => $session->start_time,
                        'end_time' => $session->end_time,
                        'formatted_duration' => $session->formatted_duration,
                        'hours' => $session->hours,
                        'is_active' => $session->is_active,
                        'is_paused' => $session->is_paused,
                        'session_number' => $session->session_number,
                        'pause_count' => $session->pause_count,
                        'resume_count' => $session->resume_count,
                        'description' => $session->description,
                        'pause_resume_log' => $session->pause_resume_log ?? [],
                        // معلومات إضافية
                        'total_seconds' => $session->total_seconds,
                        'session_summary' => $this->getSessionSummary($session),
                    ];
                }),
                'summary' => [
                    'total_hours' => $totalHours,
                    'total_seconds' => $totalSeconds,
                    'formatted_total_duration' => $this->formatDuration($totalSeconds),
                    'sessions_count' => $entries->count(),
                    'active_sessions' => $activeSessions,
                    'completed_sessions' => $entries->count() - $activeSessions,
                    'total_pauses' => $entries->sum('pause_count'),
                    'total_resumes' => $entries->sum('resume_count'),
                ]
            ];
        })->values();

        // جلب جميع المستخدمين (موظفين + أدمن)
        $employees = Employee::active()->get();
        $admins = \App\Models\Admin::where('status', 'active')->get();
        $users = $employees->map(function ($item) {
            $item->type = 'employee';
            return $item;
        })->merge($admins->map(function ($item) {
            $item->type = 'admin';
            return $item;
        }));

        $projects = Project::where('status', 'active')->get();

        // إحصائيات اليوم
        $dailyStats = [
            'total_hours' => $timeEntries->sum('hours') ?: 0,
            'active_sessions' => $timeEntries->where('is_active', true)->count(),
            'employees_working' => $timeEntries->pluck('employee_id')->unique()->count(),
            'projects_active' => $timeEntries->pluck('project_id')->unique()->count(),
        ];

        return view('admin.time-tracking.index', compact(
            'groupedEntries',
            'users',
            'projects',
            'date',
            'employeeId',
            'projectId',
            'dailyStats'
        ));
    }

    private function getSessionSummary($session)
    {
        $summary = [];
        
        if ($session->pause_resume_log && is_array($session->pause_resume_log)) {
            foreach ($session->pause_resume_log as $log) {
                $time = Carbon::parse($log['time'])->format('H:i:s');
                $action = $log['action'] === 'pause' ? 'توقف' : 
                         ($log['action'] === 'resume' ? 'استئناف' : 
                         ($log['action'] === 'restart_continue' ? 'إعادة تشغيل' : $log['action']));
                
                $summary[] = "{$action} في {$time}";
            }
        }
        
        return $summary;
    }

    private function formatDuration($totalSeconds)
    {
        if ($totalSeconds <= 0) return '00:00:00';
        
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->subWeek()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');

        // تقرير الموظفين
        $employeeReports = Employee::active()
            ->when($employeeId, fn($q) => $q->where('id', $employeeId))
            ->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $timeEntries = TimeTracking::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->with(['project', 'task'])
                    ->get();

                return [
                    'employee' => $employee,
                    'total_hours' => $timeEntries->sum('hours'),
                    'total_days' => $timeEntries->pluck('date')->unique()->count(),
                    'projects_worked' => $timeEntries->pluck('project')->unique()->count(),
                    'average_daily_hours' => $timeEntries->groupBy('date')->avg(fn($day) => $day->sum('hours')),
                    'projects_breakdown' => $timeEntries->groupBy('project_id')->map(function ($entries, $projectId) {
                        return [
                            'project' => $entries->first()->project,
                            'hours' => $entries->sum('hours'),
                            'sessions' => $entries->count(),
                        ];
                    })->values(),
                ];
            });

        // تقرير المشاريع
        $projectReports = Project::whereHas('timeTracking', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })
            ->with(['timeTracking' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($project) {
                return [
                    'project' => $project,
                    'total_hours' => $project->timeTracking->sum('hours'),
                    'employees_count' => $project->timeTracking->pluck('employee_id')->unique()->count(),
                    'sessions_count' => $project->timeTracking->count(),
                ];
            });

        $employees = Employee::active()->get();

        return view('admin.time-tracking.reports', compact(
            'employeeReports',
            'projectReports',
            'employees',
            'startDate',
            'endDate',
            'employeeId'
        ));
    }

    public function dailySummary(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $summaries = DailyWorkSummary::with('employee')
            ->where('date', $date)
            ->get();

        // إنشاء ملخصات للموظفين اللي مش موجودين
        $existingEmployeeIds = $summaries->pluck('employee_id');
        $allEmployeeIds = Employee::active()->pluck('id');
        $missingEmployeeIds = $allEmployeeIds->diff($existingEmployeeIds);

        foreach ($missingEmployeeIds as $employeeId) {
            DailyWorkSummary::generateForEmployee($employeeId, $date);
        }

        // إعادة جلب البيانات
        $summaries = DailyWorkSummary::with('employee')
            ->where('date', $date)
            ->get();

        return view('admin.time-tracking.daily-summary', compact('summaries', 'date'));
    }
}