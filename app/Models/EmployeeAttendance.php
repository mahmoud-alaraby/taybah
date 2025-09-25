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
        'notes',
        'checkout_type',
        'temp_checkout_time',
        'temp_checkin_time',
        'temp_checkout_count',
        'is_temp_out'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'temp_checkout_time' => 'datetime',
        'temp_checkin_time' => 'datetime',
        'date' => 'date',
        'is_late' => 'boolean',
        'is_temp_out' => 'boolean',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    // Scopes
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeOnTime($query)
    {
        return $query->where('is_late', false);
    }

    public function scopeTempOut($query)
    {
        return $query->where('is_temp_out', true);
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
                'is_temp_out' => false, // إعادة تعيين حالة الانصراف المؤقت
                'temp_checkout_time' => null,
                'temp_checkin_time' => null,
            ]
        );
    }

    // انصراف مؤقت
    public function tempCheckOut()
    {
        if ($this->is_temp_out) {
            return ['success' => false, 'message' => 'أنت في انصراف مؤقت بالفعل'];
        }

        if ($this->check_out_time) {
            return ['success' => false, 'message' => 'تم الانصراف النهائي مسبقاً'];
        }

        $this->update([
            'temp_checkout_time' => now(),
            'is_temp_out' => true,
            'temp_checkout_count' => $this->temp_checkout_count + 1,
        ]);

        // إيقاف أي timer نشط
        $this->stopActiveTimers();

        return [
            'success' => true, 
            'message' => 'تم الانصراف المؤقت بنجاح',
            'temp_checkout_time' => $this->temp_checkout_time
        ];
    }

    // عودة من الانصراف المؤقت
    public function tempCheckIn()
    {
        if (!$this->is_temp_out) {
            return ['success' => false, 'message' => 'لست في انصراف مؤقت'];
        }

        $this->update([
            'temp_checkin_time' => now(),
            'is_temp_out' => false,
        ]);

        return [
            'success' => true, 
            'message' => 'تم العودة من الانصراف المؤقت بنجاح',
            'temp_checkin_time' => $this->temp_checkin_time
        ];
    }

    // انصراف نهائي
    public function checkOut($type = 'final')
    {
        $checkOutTime = Carbon::now();
        
        // إذا كان في انصراف مؤقت، العودة أولاً
        if ($this->is_temp_out) {
            $this->tempCheckIn();
        }

        $this->update([
            'check_out_time' => $checkOutTime,
            'checkout_type' => $type,
            'is_temp_out' => false,
        ]);
        
        if ($this->check_in_time) {
            // حساب إجمالي الساعات مع خصم وقت الانصراف المؤقت
            $totalMinutes = $this->calculateWorkingMinutes();
            $totalHours = round($totalMinutes / 60, 2);
            
            $standardWorkHours = 7; // 7 ساعات عمل معيارية
            $overtimeHours = max(0, $totalHours - $standardWorkHours);

            $this->update([
                'total_hours' => $totalHours,
                'overtime_hours' => round($overtimeHours, 2),
            ]);
        }

        // إيقاف أي timer نشط
        $this->stopActiveTimers();
        
        return $this;
    }

    // حساب الدقائق الفعلية للعمل (بخصم الانصراف المؤقت)
    private function calculateWorkingMinutes()
    {
        $totalMinutes = $this->check_in_time->diffInMinutes($this->check_out_time);
        
        // خصم وقت الانصراف المؤقت إذا وُجد
        if ($this->temp_checkout_time && $this->temp_checkin_time) {
            $tempOutMinutes = $this->temp_checkout_time->diffInMinutes($this->temp_checkin_time);
            $totalMinutes -= $tempOutMinutes;
        } elseif ($this->temp_checkout_time && !$this->temp_checkin_time) {
            // إذا لم يعد من الانصراف المؤقت، احسب حتى وقت الانصراف النهائي
            $tempOutMinutes = $this->temp_checkout_time->diffInMinutes($this->check_out_time);
            $totalMinutes -= $tempOutMinutes;
        }

        return max(0, $totalMinutes); // تأكد من عدم وجود قيم سالبة
    }

    // إيقاف جميع Timers النشطة للموظف
    private function stopActiveTimers()
    {
        \App\Models\TimeTracking::where('employee_id', $this->employee_id)
            ->where('employee_type', $this->employee_type)
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });
    }

    // Helper methods
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

    public function getTempOutDurationAttribute()
    {
        if (!$this->temp_checkout_time) {
            return null;
        }

        $endTime = $this->temp_checkin_time ?? now();
        $minutes = $this->temp_checkout_time->diffInMinutes($endTime);
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return sprintf('%d:%02d', $hours, $mins);
    }

    public function getStatusTextAttribute()
    {
        if ($this->check_out_time) {
            return $this->checkout_type === 'final' ? 'انصراف نهائي' : 'منصرف';
        }
        
        if ($this->is_temp_out) {
            return 'انصراف مؤقت';
        }
        
        return 'حاضر';
    }

    // Relations
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'employee_id');
    }

    public function getUserAttribute()
    {
        if ($this->employee_type === 'admin') {
            return $this->admin;
        }
        return $this->employee;
    }
}