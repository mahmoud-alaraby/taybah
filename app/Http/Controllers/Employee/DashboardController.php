<?php
namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth('employee')->user();

        // صلاحيات الموظف
        $permission_ids = DB::table('employee_roles')
            ->where('employee_id', $employee->id)
            ->pluck('role_id');

        $permissions = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_permissions.role_id', $permission_ids)
            ->select('permissions.*')->get();

        $month = intval($request->input('month', date('m')));
        $year = intval($request->input('year', date('Y')));

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

        // إجمالي المشاريع الفعالة للموظف
        $project_ids = DB::table('project_employees')->where('employee_id', $employee->id)->pluck('project_id');
        $active_projects_count = DB::table('projects')->whereIn('id', $project_ids)->where('status', 'active')->count();

        // إجمالي المهام المنفذة هذا الشهر
        $tasks_count_month = DB::table('project_tasks')
            ->where('assigned_to', $employee->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // إجمالي مقبوضات الموظف هذا الشهر
        $receipts_month = DB::table('receipts')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        // إجمالي مدفوعات الموظف هذا الشهر
        $payments_month = DB::table('payments')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        // إجمالي العملاء المحتملين للموظف هذا الشهر
        $potentials_month = DB::table('potential_customers')
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // عدد طلبات المكالمة وزيارة المكتب
        $potentials_data = DB::table('potential_customers')
            ->where('employee_id', $employee->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $requested_call_count = $potentials_data->filter(function($c){
            $cl = json_decode($c->customer_classifications, true);
            return is_array($cl) && in_array('requested_call', $cl);
        })->count();

        $requested_visit_count = $potentials_data->filter(function($c){
            $cl = json_decode($c->customer_classifications, true);
            return is_array($cl) && in_array('requested_visit', $cl);
        })->count();

        // المتوسط الشهري لساعات عمل الموظف لهذا الشهر
        $avg_work_hours = DB::table('daily_work_summaries')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->avg('total_work_hours');

        // كيرف المقبوضات والمدفوعات آخر 6 أشهر للموظف
        $curve_months = [];
        $curve_receipts = [];
        $curve_payments = [];
        for ($i = 5; $i >= 0; $i--) {
            $curveDate = Carbon::create($year, $month, 1)->subMonths($i);
            $cYear = $curveDate->year;
            $cMonth = $curveDate->month;
            $curve_months[] = $months[$cMonth - 1]['name'];
            $curve_receipts[] = DB::table('receipts')->where('employee_id', $employee->id)->whereYear('date', $cYear)->whereMonth('date', $cMonth)->sum('amount');
            $curve_payments[] = DB::table('payments')->where('employee_id', $employee->id)->whereYear('date', $cYear)->whereMonth('date', $cMonth)->sum('amount');
        }

        // Pie projects status للموظف لهذا الشهر
        $employee_projects = DB::table('projects')->whereIn('id', $project_ids)
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
        $pie_active = $employee_projects->where('status', 'active')->count();
        $pie_completed = $employee_projects->where('status', 'completed')->count();
        $pie_hold = $employee_projects->where('status', 'on_hold')->count();
        $pie_cancelled = $employee_projects->where('status', 'cancelled')->count();

        $pieData = [
            'جارية' => $pie_active,
            'مكتملة' => $pie_completed,
            'متوقفة' => $pie_hold,
            'ملغاة' => $pie_cancelled,
        ];

        // عدد حالات الحضور والانصراف (أنهى حضور في الوقت/متأخر)
        $attendance_ontime = DB::table('employee_attendances')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('is_late', 0)
            ->count();
        $attendance_late = DB::table('employee_attendances')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('is_late', 1)
            ->count();

        // Data for charts
        $curveData = [
            'months' => $curve_months,
            'receipts' => $curve_receipts,
            'payments' => $curve_payments,
        ];

        return view('employee.dashboard', [
            'employee' => $employee,
            'permissions'=>  $permissions ,
            'months' => $months,
            'years' => $years,
            'month' => $month,
            'year' => $year,
            'active_projects_count' => $active_projects_count,
            'tasks_count_month' => $tasks_count_month,
            'receipts_month' => $receipts_month,
            'payments_month' => $payments_month,
            'potentials_month' => $potentials_month,
            'requested_call_count' => $requested_call_count,
            'requested_visit_count' => $requested_visit_count,
            'avg_work_hours' => $avg_work_hours,
            'curveData' => $curveData,
            'pieData' => $pieData,
            'attendance_ontime' => $attendance_ontime,
            'attendance_late' => $attendance_late,
        ]);
    }
}
