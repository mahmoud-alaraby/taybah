<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\Employee;
use App\Models\Admin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $employeeId = $request->get('employee_id');

        // بناء الاستعلام الأساسي
        $query = EmployeeAttendance::with(['employee', 'admin'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        // فلترة حسب الموظف/الأدمن المحدد
        if ($employeeId) {
            // التعامل مع النوع المدمج في المعرف
            if (strpos($employeeId, 'employee_') === 0) {
                $realEmployeeId = str_replace('employee_', '', $employeeId);
                $query->where('employee_id', $realEmployeeId)->where('employee_type', 'employee');
            } elseif (strpos($employeeId, 'admin_') === 0) {
                $realAdminId = str_replace('admin_', '', $employeeId);
                $query->where('employee_id', $realAdminId)->where('employee_type', 'admin');
            } else {
                // للتوافق مع النظام القديم - تجربة كموظف أولاً
                $query->where(function($q) use ($employeeId) {
                    $q->where(function($q2) use ($employeeId) {
                        $q2->where('employee_id', $employeeId)->where('employee_type', 'employee');
                    })->orWhere(function($q2) use ($employeeId) {
                        $q2->where('employee_id', $employeeId)->where('employee_type', 'admin');
                    });
                });
            }
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);

        // جلب الموظفين والأدمن النشطين
        $employees = Employee::where('status', 'active')->get();
        $admins = Admin::where('status', 'active')->get();

        // دمج القوائم مع تمييز النوع
        $users = collect();
        
        foreach ($employees as $employee) {
            $users->push((object)[
                'id' => 'employee_' . $employee->id,
                'name' => $employee->name,
                'employee_id' => $employee->employee_id,
                'type' => 'employee'
            ]);
        }

        foreach ($admins as $admin) {
            $users->push((object)[
                'id' => 'admin_' . $admin->id,
                'name' => $admin->name . ' (إدارة)',
                'employee_id' => 'ADM-' . $admin->id,
                'type' => 'admin'
            ]);
        }

        $users = $users->sortBy('name')->values();

        $monthlyStats = [
            'total_days' => $this->getWorkingDaysInMonth($year, $month),
            'on_time_percentage' => $this->getOnTimePercentage($year, $month, $employeeId),
            'average_hours' => $this->getAverageWorkingHours($year, $month, $employeeId),
            'total_overtime' => $this->getTotalOvertime($year, $month, $employeeId),
        ];

        return view('admin.attendance.index', compact(
            'attendances',
            'users',
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

        $employeeReports = collect();

        // تقارير الموظفين
        $employees = Employee::where('status', 'active')->get();
        foreach ($employees as $employee) {
            $attendanceData = $this->getEmployeeAttendanceData($employee->id, 'employee', $year, $month);
            if ($attendanceData['attended_days'] > 0) {
                $attendanceData['employee'] = $employee;
                $employeeReports->push($attendanceData);
            }
        }

        // تقارير الأدمن
        $admins = Admin::where('status', 'active')->get();
        foreach ($admins as $admin) {
            $attendanceData = $this->getEmployeeAttendanceData($admin->id, 'admin', $year, $month);
            if ($attendanceData['attended_days'] > 0) {
                // إضافة خصائص للأدمن للتوافق مع العرض
                $admin->employee_id = 'ADM-' . $admin->id;
                $admin->position = 'مدير';
                $admin->department_name = 'الإدارة';
                $attendanceData['employee'] = $admin;
                $employeeReports->push($attendanceData);
            }
        }

        // ترتيب التقارير حسب نسبة الحضور
        $employeeReports = $employeeReports->sortByDesc('attendance_percentage');

        $employees = $employeeReports; // تعيين المتغير بالاسم الصحيح
        
        return view('admin.attendance.reports', compact(
            'employees',
            'year',
            'month'
        ));
    }

    private function getEmployeeAttendanceData($employeeId, $employeeType, $year, $month)
    {
        $attendances = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('employee_type', $employeeType)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $workingDays = $this->getWorkingDaysInMonth($year, $month);
        $attendedDays = $attendances->count();
        $onTimeDays = $attendances->where('is_late', false)->count();
        $totalHours = $attendances->sum('total_hours');
        $overtimeHours = $attendances->sum('overtime_hours');
        
        $attendancePercentage = $workingDays > 0 ? round(($attendedDays / $workingDays) * 100, 2) : 0;
        $punctualityPercentage = $attendedDays > 0 ? round(($onTimeDays / $attendedDays) * 100, 2) : 0;
        $averageDailyHours = $attendedDays > 0 ? round($totalHours / $attendedDays, 2) : 0;

        return [
            'working_days' => $workingDays,
            'attended_days' => $attendedDays,
            'on_time_days' => $onTimeDays,
            'attendance_percentage' => $attendancePercentage,
            'punctuality_percentage' => $punctualityPercentage,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'average_daily_hours' => $averageDailyHours,
        ];
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
            $this->applyEmployeeFilter($query, $employeeId);
        }

        $totalDays = $query->count();

        if ($totalDays == 0) {
            return 0;
        }

        $onTimeDays = $query->where('is_late', false)->count();

        return round(($onTimeDays / $totalDays) * 100, 2);
    }

    private function getAverageWorkingHours($year, $month, $employeeId = null)
    {
        $query = EmployeeAttendance::whereYear('date', $year)->whereMonth('date', $month);

        if ($employeeId) {
            $this->applyEmployeeFilter($query, $employeeId);
        }

        $avgHours = $query->avg('total_hours');

        return $avgHours ? round($avgHours, 2) : 0;
    }

    private function getTotalOvertime($year, $month, $employeeId = null)
    {
        $query = EmployeeAttendance::whereYear('date', $year)->whereMonth('date', $month);

        if ($employeeId) {
            $this->applyEmployeeFilter($query, $employeeId);
        }

        $totalOvertime = $query->sum('overtime_hours');

        return $totalOvertime ? round($totalOvertime, 2) : 0;
    }

    /**
     * تطبيق فلتر الموظف/الأدمن على الاستعلام
     */
    private function applyEmployeeFilter($query, $employeeId)
    {
        if (strpos($employeeId, 'employee_') === 0) {
            $realEmployeeId = str_replace('employee_', '', $employeeId);
            $query->where('employee_id', $realEmployeeId)->where('employee_type', 'employee');
        } elseif (strpos($employeeId, 'admin_') === 0) {
            $realAdminId = str_replace('admin_', '', $employeeId);
            $query->where('employee_id', $realAdminId)->where('employee_type', 'admin');
        } else {
            // للتوافق مع النظام القديم
            $query->where(function($q) use ($employeeId) {
                $q->where(function($q2) use ($employeeId) {
                    $q2->where('employee_id', $employeeId)->where('employee_type', 'employee');
                })->orWhere(function($q2) use ($employeeId) {
                    $q2->where('employee_id', $employeeId)->where('employee_type', 'admin');
                });
            });
        }
    }
}