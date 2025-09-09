<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TimeTracking;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Admin;

class AdminWorkReportsController extends Controller
{
    public function index(Request $request)
    {
        $type       = $request->get('type', 'daily');
        $date       = $request->get('date', Carbon::today()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $projectId  = $request->get('project_id');

        switch ($type) {
            case 'weekly':
                return $this->weeklyReport($employeeId, $projectId, $date);
            case 'monthly':
                return $this->monthlyReport($employeeId, $projectId, $date);
            default:
                return $this->dailyReport($employeeId, $projectId, $date);
        }
    }

    private function dailyReport($employeeId, $projectId, $date)
    {
        $query = TimeTracking::with(['project','task'])
            ->whereDate('date', $date);

        if ($employeeId) $query->where('employee_id', $employeeId);
        if ($projectId)  $query->where('project_id', $projectId);

        $entries = $query->get();

        // استعلام لتحميل بيانات الأدمن أو الموظف بناء على employee_type
        $employeeIds = $entries->pluck('employee_id')->unique();
        $employeeTypes = $entries->pluck('employee_type');

        // تحميل جميع الموظفين والأدمن المرتبطين في الوقت نفسه
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $admins = Admin::whereIn('id', $employeeIds)->get()->keyBy('id');

        $grouped = $entries->groupBy('employee_id')->map(function($rows) use ($employees, $admins) {
            $first = $rows->first();
            $person = null;

            if ($first->employee_type == 'admin') {
                $person = $admins->get($first->employee_id);
            } else {
                $person = $employees->get($first->employee_id);
            }

            return [
                'employee' => $person,
                'projects' => $rows->groupBy('project_id')->map(function($pRows) {
                    return [
                        'project' => $pRows->first()->project,
                        'tasks'   => $pRows->groupBy('task_id')->map(function($tRows) {
                            $hours = $tRows->sum('hours');

                            return [
                                'task' => $tRows->first()->task,
                                'hours' => $hours,
                                'target' => 7,
                                'achievement' => round(($hours / 7) * 100, 1),
                                'overtime' => max(0, $hours - 7),
                            ];
                        })->values()
                    ];
                })->values()
            ];
        });

        return view('admin.work-reports.index', [
            'type' => 'daily',
            'date' => $date,
            'data' => $grouped,
            'employees' => Employee::all(),
            'projects' => Project::where('status','active')->get()
        ]);
    }

    private function weeklyReport($employeeId, $projectId, $date)
    {
        $start = Carbon::parse($date)->startOfWeek();
        $end   = Carbon::parse($date)->endOfWeek();

        $query = TimeTracking::with(['project','task'])
            ->whereBetween('date', [$start, $end]);

        if ($employeeId) $query->where('employee_id', $employeeId);
        if ($projectId)  $query->where('project_id', $projectId);

        $entries = $query->get();

        $employeeIds = $entries->pluck('employee_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $admins = Admin::whereIn('id', $employeeIds)->get()->keyBy('id');

        $grouped = $entries->groupBy('employee_id')->map(function($rows) use ($employees, $admins) {
            $totalHours = $rows->sum('hours');
            $targetHours = 42; // 6 أيام × 7 ساعات
            $first = $rows->first();
            $person = null;

            if ($first->employee_type == 'admin') {
                $person = $admins->get($first->employee_id);
            } else {
                $person = $employees->get($first->employee_id);
            }

            return [
                'employee' => $person,
                'total_hours' => $totalHours,
                'target_hours' => $targetHours,
                'achievement' => round(($totalHours/$targetHours)*100,1),
                'overtime' => max(0, $totalHours - $targetHours),
                'tasks' => $rows->groupBy('task_id')->map(function($tRows){
                    return [
                        'task' => $tRows->first()->task,
                        'hours' => $tRows->sum('hours'),
                    ];
                })->values(),
            ];
        });

        return view('admin.work-reports.index', [
            'type' => 'weekly',
            'start' => $start,
            'end' => $end,
            'data' => $grouped,
            'employees' => Employee::all(),
            'projects' => Project::where('status','active')->get()
        ]);
    }

    private function monthlyReport($employeeId, $projectId, $date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        $start = Carbon::create($year,$month,1);
        $end   = $start->copy()->endOfMonth();

        $query = TimeTracking::with(['project','task'])
            ->whereBetween('date', [$start, $end]);

        if ($employeeId) $query->where('employee_id', $employeeId);
        if ($projectId)  $query->where('project_id', $projectId);

        $entries = $query->get();

        $employeeIds = $entries->pluck('employee_id')->unique();
        $employees = Employee::whereIn('id', $employeeIds)->get()->keyBy('id');
        $admins = Admin::whereIn('id', $employeeIds)->get()->keyBy('id');

        $grouped = $entries->groupBy('employee_id')->map(function($rows) use ($employees, $admins) {
            $totalHours = $rows->sum('hours');
            $targetHours = 182; // 26 يوم × 7 ساعات
            $first = $rows->first();
            $person = null;

            if ($first->employee_type == 'admin') {
                $person = $admins->get($first->employee_id);
            } else {
                $person = $employees->get($first->employee_id);
            }

            return [
                'employee' => $person,
                'total_hours' => $totalHours,
                'target_hours' => $targetHours,
                'achievement' => round(($totalHours/$targetHours)*100,1),
                'overtime' => max(0, $totalHours - $targetHours),
                'tasks' => $rows->groupBy('task_id')->map(function($tRows){
                    return [
                        'task' => $tRows->first()->task,
                        'hours' => $tRows->sum('hours'),
                    ];
                })->values(),
            ];
        });

        return view('admin.work-reports.index', [
            'type' => 'monthly',
            'start' => $start,
            'end' => $end,
            'data' => $grouped,
            'employees' => Employee::all(),
            'projects' => Project::where('status','active')->get()
        ]);
    }
}
