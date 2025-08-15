<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyWorkSummary;
use App\Models\TimeTracking;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkReportsController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = auth('employee')->id();
        $type = $request->get('type', 'daily');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        switch ($type) {
            case 'weekly':
                return $this->weeklyReport($employeeId, $date);
            case 'monthly':
                return $this->monthlyReport($employeeId, $date);
            case 'project':
                return $this->projectReport($employeeId, $request);
            default:
                return $this->dailyReport($employeeId, $date);
        }
    }

    private function dailyReport($employeeId, $date)
    {
        $summary = DailyWorkSummary::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();

        if (!$summary) {
            $summary = DailyWorkSummary::generateForEmployee($employeeId, $date);
        }

        $timeEntries = TimeTracking::forEmployee($employeeId)
            ->forDate($date)
            ->with(['project', 'task'])
            ->orderBy('start_time')
            ->get();

        return view('employee.work-reports.index', compact('summary', 'timeEntries', 'date'));
    }

    private function weeklyReport($employeeId, $date)
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        $summaries = DailyWorkSummary::where('employee_id', $employeeId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date')
            ->get();

        $weeklyStats = [
            'total_hours' => $summaries->sum('total_work_hours'),
            'overtime_hours' => $summaries->sum('overtime_hours'),
            'average_daily_hours' => $summaries->avg('total_work_hours'),
            'days_worked' => $summaries->count(),
            'target_achievement' => $summaries->avg('daily_target_percentage'),
        ];

        // إحصائيات المشاريع للأسبوع
        $projectStats = $this->getProjectStatsForPeriod($employeeId, $startOfWeek, $endOfWeek);

        return view('employee.reports.weekly', compact(
            'summaries', 'weeklyStats', 'projectStats', 'startOfWeek', 'endOfWeek'
        ));
    }

    private function monthlyReport($employeeId, $date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $summaries = DailyWorkSummary::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->orderBy('date')
            ->get();

        $monthlyStats = [
            'total_hours' => $summaries->sum('total_work_hours'),
            'overtime_hours' => $summaries->sum('overtime_hours'),
            'days_worked' => $summaries->count(),
            'average_daily_hours' => $summaries->avg('total_work_hours'),
            'target_achievement' => $summaries->avg('daily_target_percentage'),
            'expected_working_days' => $this->getWorkingDaysInMonth($year, $month),
        ];

        // إحصائيات المشاريع للشهر
        $projectStats = $this->getProjectStatsForPeriod($employeeId, $startOfMonth, $endOfMonth);

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.reports.monthly', compact(
            'summaries', 'monthlyStats', 'projectStats', 'year', 'month', 'months'
        ));
    }

    private function projectReport($employeeId, $request)
    {
        $projectId = $request->get('project_id');
        $startDate = $request->get('start_date', Carbon::today()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

        // مشاريع الموظف
        $projects = Project::whereHas('tasks', function($q) use ($employeeId) {
            $q->where('assigned_to', $employeeId);
        })->get();

        $query = TimeTracking::forEmployee($employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['project', 'task']);

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $timeEntries = $query->get();

        $projectBreakdown = $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
            $project = $entries->first()->project;
            return [
                'project' => $project,
                'total_hours' => $entries->sum('hours'),
                'sessions_count' => $entries->count(),
                'tasks' => $entries->groupBy('task_id')->map(function($taskEntries, $taskId) {
                    $task = $taskEntries->first()->task;
                    return [
                        'task' => $task,
                        'hours' => $taskEntries->sum('hours'),
                        'sessions' => $taskEntries->count(),
                    ];
                })->values(),
            ];
        })->values();

        return view('employee.reports.project', compact(
            'projects', 'projectBreakdown', 'timeEntries', 'startDate', 'endDate', 'projectId'
        ));
    }

    private function getProjectStatsForPeriod($employeeId, $startDate, $endDate)
    {
        $timeEntries = TimeTracking::forEmployee($employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['project'])
            ->get();

        return $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
            $project = $entries->first()->project;
            return [
                'project' => $project,
                'total_hours' => $entries->sum('hours'),
                'sessions_count' => $entries->count(),
                'days_worked' => $entries->pluck('date')->unique()->count(),
            ];
        })->values();
    }

    private function getWorkingDaysInMonth($year, $month)
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($start->dayOfWeek !== Carbon::FRIDAY) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    public function printReport(Request $request)
    {
        $employeeId = auth('employee')->id();
        $employee = auth('employee')->user();
        $type = $request->get('type', 'monthly');
        
        switch ($type) {
            case 'weekly':
                return $this->printWeeklyReport($employee, $request);
            case 'project':
                return $this->printProjectReport($employee, $request);
            default:
                return $this->printMonthlyReport($employee, $request);
        }
    }

private function printMonthlyReport($employee, $request)
   {
       $year = $request->get('year', date('Y'));
       $month = $request->get('month', date('n'));

       $summaries = DailyWorkSummary::where('employee_id', $employee->id)
           ->forMonth($year, $month)
           ->orderBy('date')
           ->get();

       $monthlyStats = [
           'total_hours' => $summaries->sum('total_work_hours'),
           'overtime_hours' => $summaries->sum('overtime_hours'),
           'days_worked' => $summaries->count(),
           'average_daily_hours' => $summaries->avg('total_work_hours'),
           'target_achievement' => $summaries->avg('daily_target_percentage'),
       ];

       $months = [
           1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
           5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
           9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
       ];

       return view('employee.reports.print-monthly', compact(
           'employee', 'summaries', 'monthlyStats', 'year', 'month', 'months'
       ));
   }

   private function printWeeklyReport($employee, $request)
   {
       $date = $request->get('date', Carbon::today()->format('Y-m-d'));
       $startOfWeek = Carbon::parse($date)->startOfWeek();
       $endOfWeek = Carbon::parse($date)->endOfWeek();

       $summaries = DailyWorkSummary::where('employee_id', $employee->id)
           ->whereBetween('date', [$startOfWeek, $endOfWeek])
           ->orderBy('date')
           ->get();

       $weeklyStats = [
           'total_hours' => $summaries->sum('total_work_hours'),
           'overtime_hours' => $summaries->sum('overtime_hours'),
           'average_daily_hours' => $summaries->avg('total_work_hours'),
           'days_worked' => $summaries->count(),
           'target_achievement' => $summaries->avg('daily_target_percentage'),
       ];

       return view('employee.reports.print-weekly', compact(
           'employee', 'summaries', 'weeklyStats', 'startOfWeek', 'endOfWeek'
       ));
   }

   private function printProjectReport($employee, $request)
   {
       $projectId = $request->get('project_id');
       $startDate = $request->get('start_date', Carbon::today()->subMonth()->format('Y-m-d'));
       $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

       $query = TimeTracking::forEmployee($employee->id)
           ->whereBetween('date', [$startDate, $endDate])
           ->with(['project', 'task']);

       if ($projectId) {
           $query->where('project_id', $projectId);
       }

       $timeEntries = $query->get();

       $projectBreakdown = $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
           $project = $entries->first()->project;
           return [
               'project' => $project,
               'total_hours' => $entries->sum('hours'),
               'sessions_count' => $entries->count(),
               'tasks' => $entries->groupBy('task_id')->map(function($taskEntries, $taskId) {
                   $task = $taskEntries->first()->task;
                   return [
                       'task' => $task,
                       'hours' => $taskEntries->sum('hours'),
                       'sessions' => $taskEntries->count(),
                   ];
               })->values(),
           ];
       })->values();

       return view('employee.reports.print-project', compact(
           'employee', 'projectBreakdown', 'timeEntries', 'startDate', 'endDate'
       ));
   }
}