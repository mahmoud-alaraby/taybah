<?php
namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth('employee')->user();
        // الفلاتر
        $month = intval($request->input('month', date('m')));
        $year = intval($request->input('year', date('Y')));
        $project_id = intval($request->input('project_id'));

        $months = [
            ['num' => 1, 'name' => 'يناير'],
            ['num' => 2, 'name' => 'فبراير'],
            ['num' => 3, 'name' => 'مارس'],
            ['num' => 4, 'name' => 'إبريل'],
            ['num' => 5, 'name' => 'مايو'],
            ['num' => 6, 'name' => 'يونيو'],
            ['num' => 7, 'name' => 'يوليو'],
            ['num' => 8, 'name' => 'أغسطس'],
            ['num' => 9, 'name' => 'سبتمبر'],
            ['num' => 10, 'name' => 'أكتوبر'],
            ['num' => 11, 'name' => 'نوفمبر'],
            ['num' => 12, 'name' => 'ديسمبر'],
        ];
        $years = range(date('Y')-2, date('Y')+2);

        // صلاحيات الموظف
        $permission_ids = DB::table('employee_roles')
            ->where('employee_id', $employee->id)
            ->pluck('role_id');

        $permissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_permissions.role_id', $permission_ids)
            ->select('permissions.*')->get();

        // المشاريع التي يعمل فيها الموظف
        $project_ids = DB::table('project_employees')
            ->where('employee_id', $employee->id)->pluck('project_id');

        // احصائيات الحالات للمشاريع Pie
        $projects_stats = DB::table('projects')
            ->whereIn('id', $project_ids)
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')->pluck('cnt', 'status');

        $status_labels = [
            'active' => 'جارية',
            'completed' => 'مكتملة',
            'on_hold' => 'متوقفة',
            'cancelled' => 'ملغاة',
        ];
        $pie_stats = [];
        foreach ($status_labels as $key => $label) {
            $pie_stats[$label] = $projects_stats[$key] ?? 0;
        }

        // حضوره في الموعد وتأخيره لكل يوم في الشهر
        $attendance_curve_dates = [];
        $attendance_curve_ontime = [];
        $attendance_curve_late = [];

        $attendances = DB::table('employee_attendances')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // generate days for selected month
        $days_in_month = Carbon::create($year, $month, 1)->daysInMonth;
        for ($i = 1; $i <= $days_in_month; $i++) {
            $date = Carbon::create($year, $month, $i)->toDateString();
            $attendance_curve_dates[] = $date;
            $ontime = $attendances->where('date', $date)->where('is_late', 0)->count();
            $late = $attendances->where('date', $date)->where('is_late', 1)->count();
            $attendance_curve_ontime[] = $ontime;
            $attendance_curve_late[] = $late;
        }

        // مشاريع الموظف لفلتر المهام completion curve
        $employee_projects = DB::table('projects')
            ->whereIn('id', $project_ids)
            ->select('id', 'name')->get();

        $selected_project = $project_id ?: ($employee_projects[0]->id ?? null);

        $tasks = DB::table('project_tasks')
            ->where('assigned_to', $employee->id)
            ->where('project_id', $selected_project)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $completed_ontime = $tasks->where('completed_on_time', 1)->count();
        $completed_late = $tasks->where('completed_on_time', 0)->count();

        return view('employee.dashboard', [
            'employee' => $employee,
            'permissions' => $permissions,
            'months' => $months,
            'years' => $years,
            'month' => $month,
            'year' => $year,
            'pie_stats' => $pie_stats,
            'attendance_curve' => [
                'dates' => $attendance_curve_dates,
                'ontime' => $attendance_curve_ontime,
                'late' => $attendance_curve_late,
            ],
            'employee_projects' => $employee_projects,
            'selected_project' => $selected_project,
            'tasks_curve' => [
                'completed_ontime' => $completed_ontime,
                'completed_late' => $completed_late,
            ]
        ]);
    }
}
