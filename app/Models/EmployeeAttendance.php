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
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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
    public static function checkIn($employeeId)
    {
        $today = Carbon::today();
        $checkInTime = Carbon::now();
        $targetTime = Carbon::today()->setTime(10, 0); // 10:00 AM
        
        $isLate = $checkInTime->gt($targetTime->addMinutes(10)); // بعد 10:10
        $lateMinutes = $isLate ? $checkInTime->diffInMinutes($targetTime->subMinutes(10)) : 0;

        return self::updateOrCreate(
            ['employee_id' => $employeeId, 'date' => $today],
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
            $totalHours = $this->check_in_time->diffInHours($checkOutTime, true);
            $this->total_hours = $totalHours;
            $this->overtime_hours = max(0, $totalHours - 7); // 7 ساعات هو التارجت
        }
        
        $this->save();
        return $this;
    }
}