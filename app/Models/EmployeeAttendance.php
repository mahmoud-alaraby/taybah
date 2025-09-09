<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_type',
        'check_in_time',
        'check_out_time',
        'is_late',
        'late_minutes',
        'total_hours',
        'overtime_hours',
        'date',
        'notes'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'date' => 'date',
        'is_late' => 'boolean',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    // Relations
    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class);
    // }

    // Scopes
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeOnTime($query)
    {
        return $query->where('is_late', false);
    }

    // Methods
    public static function checkIn($employeeId, $employeeType = 'employee')
    {
        $today = Carbon::today();
        $checkInTime = Carbon::now();
        $workStartTime = Carbon::today()->setTime(10, 0); // 10:00 AM
        
        // حساب التأخير بالدقائق
        $isLate = $checkInTime->gt($workStartTime);
        $lateMinutes = $isLate ? $checkInTime->diffInMinutes($workStartTime) : 0;

        return self::updateOrCreate(
            ['employee_id' => $employeeId, 'employee_type' => $employeeType, 'date' => $today],
            [
                'check_in_time' => $checkInTime,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
            ]
        );
    }

    public function checkOut()
    {
        $checkOutTime = Carbon::now();
        $this->check_out_time = $checkOutTime;
        
        if ($this->check_in_time) {
            // حساب إجمالي الساعات بالدقائق ثم تحويل للساعات
            $totalMinutes = $this->check_in_time->diffInMinutes($checkOutTime);
            $totalHours = round($totalMinutes / 60, 2);
            
            $standardWorkHours = 7; // 7 ساعات عمل معيارية
            $overtimeHours = max(0, $totalHours - $standardWorkHours);

            $this->total_hours = $totalHours;
            $this->overtime_hours = round($overtimeHours, 2);
        }
        
        $this->save();
        return $this;
    }

    // Helper methods لتحويل الوقت
    public function getLateTimeFormattedAttribute()
    {
        if (!$this->is_late || $this->late_minutes <= 0) {
            return null;
        }

        $hours = floor($this->late_minutes / 60);
        $minutes = $this->late_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours} ساعة و {$minutes} دقيقة";
        } else {
            return "{$minutes} دقيقة";
        }
    }

    public function getTotalHoursFormattedAttribute()
    {
        if (!$this->total_hours) {
            return '0:00';
        }

        $totalMinutes = round($this->total_hours * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%d:%02d', $hours, $minutes);
    }

//     public function admin()
// {
//     return $this->belongsTo(Admin::class, 'employee_id');
// }

public function getUserAttribute()
{
    if ($this->employee_type === 'admin') {
        return $this->admin;
    }
    // افتراضياً employee
    return $this->employee;
}

public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}

public function admin()
{
    return $this->belongsTo(Admin::class, 'employee_id');
}


}