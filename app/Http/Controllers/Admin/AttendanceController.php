<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        $query = EmployeeAttendance::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);
        $employees = Employee::active()->get();

        // إحصائيات الشهر - مع التحقق من وجود البيانات
        $monthlyStats = [
            'total_days' => $this->getWorkingDaysInMonth($year, $month),
            'on_time_percentage' => $this->getOnTimePercentage($year, $month, $employeeId),
            'average_hours' => $this->getAverageWorkingHours($year, $month, $employeeId),
            'total_overtime' => $this->getTotalOvertime($year, $month, $employeeId),
        ];

        return view('admin.attendance.index', compact(
            'attendances',
            'employees',
            'year',
            'month',
            'employeeId',
            'monthlyStats'
        ));
    }

    public function reports(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $employees = Employee::active()->get()->map(function ($employee) use ($year, $month) {
            $attendances = EmployeeAttendance::where('employee_id', $employee->id)
                ->forMonth($year, $month)
                ->get();

            $workingDays = $this->getWorkingDaysInMonth($year, $month);
            $attendedDays = $attendances->count();
            $onTimeDays = $attendances->where('is_late', false)->count();
            $totalHours = $attendances->sum('total_hours');
            $overtimeHours = $attendances->sum('overtime_hours');

            return [
                'employee' => $employee,
                'working_days' => $workingDays,
                'attended_days' => $attendedDays,
                'on_time_days' => $onTimeDays,
                'attendance_percentage' => $workingDays > 0 ? round(($attendedDays / $workingDays) * 100, 2) : 0,
                'punctuality_percentage' => $attendedDays > 0 ? round(($onTimeDays / $attendedDays) * 100, 2) : 0,
                'total_hours' => $totalHours,
                'overtime_hours' => $overtimeHours,
                'average_daily_hours' => $attendedDays > 0 ? round($totalHours / $attendedDays, 2) : 0,
            ];
        });

        return view('admin.attendance.reports', compact('employees', 'year', 'month'));
    }

    private function getWorkingDaysInMonth($year, $month)
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $workingDays = 0;

        while ($start->lte($end)) {
            // استبعاد الجمعة (6) والأعياد
            if ($start->dayOfWeek !== Carbon::FRIDAY && !$this->isHoliday($start)) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function isHoliday($date)
    {
        // قائمة الأعياد (عيد الفطر وعيد الأضحى - 4 أيام فقط)
        $holidays = [
            // يمكن إضافة تواريخ الأعياد هنا
            // أو جعلها قابلة للتخصيص من الإعدادات
        ];

        return in_array($date->format('Y-m-d'), $holidays);
    }

    private function getOnTimePercentage($year, $month, $employeeId = null)
    {
        $query = EmployeeAttendance::whereYear('date', $year)->whereMonth('date', $month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $totalDays = $query->count();

        if ($totalDays == 0) {
            return 0; // إرجاع 0 إذا مفيش بيانات
        }

        $onTimeDays = $query->where('is_late', false)->count();

        return round(($onTimeDays / $totalDays) * 100, 2);
    }


    private function getAverageWorkingHours($year, $month, $employeeId = null)
    {
        $query = EmployeeAttendance::whereYear('date', $year)->whereMonth('date', $month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $avgHours = $query->avg('total_hours');

        return $avgHours ? round($avgHours, 2) : 0;
    }


    private function getTotalOvertime($year, $month, $employeeId = null)
    {
        $query = EmployeeAttendance::whereYear('date', $year)->whereMonth('date', $month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $totalOvertime = $query->sum('overtime_hours');

        return $totalOvertime ? round($totalOvertime, 2) : 0;
    }
    
}