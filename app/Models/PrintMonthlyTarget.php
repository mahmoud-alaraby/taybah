<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintMonthlyTarget extends Model
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
     * تحديث أو إنشاء تارجت شهري
     */
    public static function updateOrCreateTarget($year, $month, $targetAmount, $employeeId)
    {
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
     * الحصول على التارجت لموظف وشهر معين
     */
    public static function getTargetForMonth($year, $month, $employeeId)
    {
        $target = self::where('employee_id', $employeeId)
                     ->where('year', $year)
                     ->where('month', $month)
                     ->first();

        return $target ? $target->target_amount : 0;
    }

    /**
     * scope للفترة
     */
    public function scopeForPeriod($query, $year, $month)
    {
        return $query->where('year', $year)
                    ->where('month', $month);
    }
}