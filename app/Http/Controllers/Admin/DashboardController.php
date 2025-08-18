<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
   public function index(Request $request)
   {
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
       $years = range(date('Y') - 2, date('Y') + 2);

       // المقبوضات والمدفوعات لهذا الشهر
       $receipts_month = DB::table('receipts')->whereYear('date', $year)->whereMonth('date', $month)->sum('amount');
       $payments_month = DB::table('payments')->whereYear('date', $year)->whereMonth('date', $month)->sum('amount');

       // إعداد الكيرف لآخر 6 أشهر
       $curve_months = [];
       $curve_receipts = [];
       $curve_payments = [];
       for ($i = 5; $i >= 0; $i--) {
           $curveDate = Carbon::create($year, $month, 1)->subMonths($i);
           $cYear = $curveDate->year;
           $cMonth = $curveDate->month;
           $curve_months[] = $months[$cMonth - 1]['name'];
           $curve_receipts[] = DB::table('receipts')->whereYear('date', $cYear)->whereMonth('date', $cMonth)->sum('amount');
           $curve_payments[] = DB::table('payments')->whereYear('date', $cYear)->whereMonth('date', $cMonth)->sum('amount');
       }

       // المشاريع لهذا الشهر "النشطة" و"المكتملة"
       $projects_this_month = DB::table('projects')->whereYear('created_at', $year)->whereMonth('created_at', $month)->get();
       $projects_active = $projects_this_month->where('status', 'active')->count();
       $projects_completed = $projects_this_month->where('status', 'completed')->count();
       $projects_hold = $projects_this_month->where('status', 'on_hold')->count();
       $projects_cancelled = $projects_this_month->where('status', 'cancelled')->count();
       $projects_total = max($projects_active + $projects_completed + $projects_hold + $projects_cancelled, 1);

       // نسب إكمال المشاريع
       $project_pie = [
           'جارية'   => $projects_active,
           'مكتملة'  => $projects_completed,
           'متوقفة'  => $projects_hold,
           'ملغاة'   => $projects_cancelled,
       ];

       // العملاء المحتملين
       $potential_customers = DB::table('potential_customers')
           ->whereYear('created_at', $year)
           ->whereMonth('created_at', $month)->get();
       $total_potentials = $potential_customers->count();
       $requested_call_count = $potential_customers->filter(function ($c) {
           $cl = json_decode($c->customer_classifications, true);
           return is_array($cl) && in_array('requested_call', $cl);
       })->count();
       $requested_visit_count = $potential_customers->filter(function ($c) {
           $cl = json_decode($c->customer_classifications, true);
           return is_array($cl) && in_array('requested_visit', $cl);
       })->count();

       // عدد المشاريع الفعالة
       $projects_count = DB::table('projects')->where('status', 'active')->count();

       // عدد الموظفين الفعالين
       $employees_count = DB::table('employees')->where('status', 'active')->count();

       // متوسط ساعات العمل للموظفين هذا الشهر
       $avg_work_hours = DB::table('daily_work_summaries')
           ->whereYear('date', $year)->whereMonth('date', $month)->avg('total_work_hours');

       // أفضل موظف في المقبوضات
       $top_employee_row = DB::table('receipts')
           ->select('employee_id', DB::raw('SUM(amount) as total_amount'))
           ->whereYear('date', $year)->whereMonth('date', $month)
           ->groupBy('employee_id')->orderByDesc('total_amount')->first();
       $top_employee_name = $top_employee_row
           ? DB::table('employees')->where('id', $top_employee_row->employee_id)->value('name') : '-';
       $top_employee_amount = $top_employee_row ? $top_employee_row->total_amount : 0;

       // أكثر موظف حضور في الوقت
       $most_on_time_row = DB::table('employee_attendances')
           ->whereYear('date', $year)->whereMonth('date', $month)->where('is_late', 0)
           ->select('employee_id', DB::raw('COUNT(*) as cnt'))
           ->groupBy('employee_id')->orderByDesc('cnt')->first();
       $most_on_time_name = $most_on_time_row
           ? DB::table('employees')->where('id', $most_on_time_row->employee_id)->value('name') : '-';

       // أكثر موظف تأخير
       $most_late_row = DB::table('employee_attendances')
           ->whereYear('date', $year)->whereMonth('date', $month)->where('is_late', 1)
           ->select('employee_id', DB::raw('COUNT(*) as cnt'))
           ->groupBy('employee_id')->orderByDesc('cnt')->first();
       $most_late_name = $most_late_row
           ? DB::table('employees')->where('id', $most_late_row->employee_id)->value('name') : '-';

       // أكثر موظف ينفذ مهام في المشاريع
       $most_project_tasks_row = DB::table('project_tasks')
           ->whereYear('created_at', $year)->whereMonth('created_at', $month)
           ->select('assigned_to', DB::raw('COUNT(*) as cnt'))->whereNotNull('assigned_to')
           ->groupBy('assigned_to')->orderByDesc('cnt')->first();
       $most_project_tasks_name = $most_project_tasks_row
           ? DB::table('employees')->where('id', $most_project_tasks_row->assigned_to)->value('name') : '-';
       $most_project_tasks_count = $most_project_tasks_row ? $most_project_tasks_row->cnt : 0;

       // أنشطة السيستم/modules
       $systems_data = DB::table('permissions')
           ->select('system_category', DB::raw('COUNT(*) as modules_count'))
           ->groupBy('system_category')->get();
       $systems_details = DB::table('permissions')
           ->select('system_category', 'display_name')->get()
           ->groupBy('system_category')
           ->map(fn($items) => $items->pluck('display_name')->toArray());
       $total_modules = $systems_data->sum('modules_count');
       foreach ($systems_data as $sys) {
           $sys->percentage = $total_modules > 0 ? round(($sys->modules_count / $total_modules) * 100) : 0;
       }

       // بيانات الرسم للكيرف والدائرة
       $curveData = [
           'months'    => $curve_months,
           'receipts'  => $curve_receipts,
           'payments'  => $curve_payments,
       ];
       $pieData = $project_pie;
       $projects_total = $projects_total; // لتحسين العرض

       return view('admin.dashboard', [
           'receipts_month'           => $receipts_month,
           'payments_month'           => $payments_month,
           'curveData'                => $curveData,
           'pieData'                  => $pieData,
           'projects_total'           => $projects_total,
           'projects_count'           => $projects_count,
           'employees_count'          => $employees_count,
           'month'                    => $month,
           'year'                     => $year,
           'months'                   => $months,
           'years'                    => $years,
           'total_potentials'         => $total_potentials,
           'requested_call_count'     => $requested_call_count,
           'requested_visit_count'    => $requested_visit_count,
           'avg_work_hours'           => $avg_work_hours,
           'top_employee_name'        => $top_employee_name,
           'top_employee_amount'      => $top_employee_amount,
           'most_on_time_name'        => $most_on_time_name,
           'most_late_name'           => $most_late_name,
           'most_project_tasks_name'  => $most_project_tasks_name,
           'most_project_tasks_count' => $most_project_tasks_count,
           'systems_data'             => $systems_data,
           'systems_details'          => $systems_details,
       ]);
   }
}
