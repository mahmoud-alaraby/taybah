<?php
// app/Models/Receipt.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'description',
        'amount',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
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
     * scope للبحث في البيان
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', '%' . $search . '%');
    }

    /**
     * scope للفترة
     */
    public function scopeForPeriod($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    /**
     * scope للموظف الحالي
     */
    public function scopeForCurrentEmployee($query)
    {
        return $query->where('employee_id', auth('employee')->id());
    }

    /**
     * الحصول على مجموع المقبوضات لشهر معين
     */
    public static function getTotalForMonth($year, $month, $employeeId = null)
    {
        $employeeId = $employeeId ?: auth('employee')->id();
        
        return self::where('employee_id', $employeeId)
                  ->whereYear('date', $year)
                  ->whereMonth('date', $month)
                  ->sum('amount');
    }
}