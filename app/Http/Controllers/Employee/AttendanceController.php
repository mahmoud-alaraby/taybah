<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\DailyWorkSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = auth('employee')->id();
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->orderBy('date', 'desc')
            ->get();

        // إحصائيات الشهر
        $monthlyStats = $this->getMonthlyStats($employeeId, $year, $month);

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.attendance.index', compact(
            'attendances', 'monthlyStats', 'year', 'month', 'months'
        ));
    }

    public function todayStatus()
    {
        $employeeId = auth('employee')->id();
        $today = Carbon::today();

        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        $workSummary = DailyWorkSummary::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        return response()->json([
            'attendance' => $attendance,
            'work_summary' => $workSummary,
            'can_check_in' => !$attendance,
            'can_check_out' => $attendance && !$attendance->check_out_time,
        ]);
    }

    public function printReport(Request $request)
    {
        $employeeId = auth('employee')->id();
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $employee = auth('employee')->user();
        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->orderBy('date', 'asc')
            ->get();

        $workSummaries = DailyWorkSummary::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->orderBy('date', 'asc')
            ->get();

        $monthlyStats = $this->getMonthlyStats($employeeId, $year, $month);

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('employee.attendance.print', compact(
            'employee', 'attendances', 'workSummaries', 'monthlyStats', 'year', 'month', 'months'
        ));
    }

    private function getMonthlyStats($employeeId, $year, $month)
    {
        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->get();

        $workSummaries = DailyWorkSummary::where('employee_id', $employeeId)
            ->forMonth($year, $month)
            ->get();

        $workingDays = $this->getWorkingDaysInMonth($year, $month);

        return [
            'working_days' => $workingDays,
            'attended_days' => $attendances->count(),
            'on_time_days' => $attendances->where('is_late', false)->count(),
            'late_days' => $attendances->where('is_late', true)->count(),
            'total_work_hours' => $workSummaries->sum('total_work_hours'),
            'total_overtime_hours' => $workSummaries->sum('overtime_hours'),
            'average_daily_hours' => $workSummaries->avg('total_work_hours'),
            'attendance_percentage' => $workingDays > 0 
                ? round(($attendances->count() / $workingDays) * 100, 2) 
                : 0,
            'punctuality_percentage' => $attendances->count() > 0 
                ? round(($attendances->where('is_late', false)->count() / $attendances->count()) * 100, 2) 
                : 0,
            'target_achievement' => $workSummaries->avg('daily_target_percentage'),
        ];
    }

    private function getWorkingDaysInMonth($year, $month)
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        while ($start->lte($end)) {
            // استبعاد الجمعة والأعياد
            if ($start->dayOfWeek !== Carbon::FRIDAY && !$this->isHoliday($start)) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function isHoliday($date)
    {
        // قائمة الأعياد - يمكن جعلها ديناميكية
        $holidays = [
            // عيد الفطر وعيد الأضحى - 4 أيام فقط
        ];

        return in_array($date->format('Y-m-d'), $holidays);
    }
}