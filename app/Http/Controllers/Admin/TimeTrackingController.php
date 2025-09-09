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

    // جلب جميع الموظفين والنشطاء فقط
    $employees = Employee::active()->get();

    // جلب جميع الـ admins النشطين
    $admins = \App\Models\Admin::where('status', 'active')->get();

    // دمج المجموعتين (يمكنك دمجهم في مجموعة واحدة)
    $users = $employees->map(function ($item) {
        $item->type = 'employee';
        return $item;
    })->merge($admins->map(function ($item) {
        $item->type = 'admin';
        return $item;
    }));

    $projects = Project::where('status', 'active')->get();

    // إحصائيات اليوم - مع التحقق من وجود البيانات
    $dailyStats = [
        'total_hours' => $timeEntries->sum('hours') ?: 0,
        'active_sessions' => $timeEntries->where('is_active', true)->count(),
        'employees_working' => $timeEntries->pluck('employee_id')->unique()->count(),
        'projects_active' => $timeEntries->pluck('project_id')->unique()->count(),
    ];

    return view('admin.time-tracking.index', compact(
        'timeEntries',
        'users',
        'projects',
        'date',
        'employeeId',
        'projectId',
        'dailyStats'
    ));
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
