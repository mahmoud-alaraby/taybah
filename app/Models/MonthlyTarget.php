<?php
// app/Models/MonthlyTarget.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'target_amount',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'year' => 'integer',
            'month' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * الحصول على التارجت للشهر الحالي
     */
    public static function getCurrentTarget($employeeId = null)
    {
        $employeeId = $employeeId ?: auth('employee')->id();
        $currentYear = date('Y');
        $currentMonth = date('n');

        return self::where('employee_id', $employeeId)
                  ->where('year', $currentYear)
                  ->where('month', $currentMonth)
                  ->first();
    }

    /**
     * تحديث أو إنشاء التارجت
     */
    public static function updateOrCreateTarget($year, $month, $targetAmount, $employeeId = null)
    {
        $employeeId = $employeeId ?: auth('employee')->id();

        return self::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'target_amount' => $targetAmount,
            ]
        );
    }

    /**
     * حساب نسبة التحقق
     */
    public function getAchievementPercentage($actualAmount)
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return round(($actualAmount / $this->target_amount) * 100, 2);
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return $months[$this->month] ?? '';
    }
}