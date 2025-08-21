<?php
// app/Models/CustomerMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'customer_name',
        'customer_phone',
        'work_description',
        'agreement_start_date',
        'initial_delivery_date',
        'final_delivery_date',
        'agreed_amount',
        'first_payment',
        'second_payment',
        'third_payment',
        'fourth_payment',
        'customer_type',
        'work_status',
    ];

    protected function casts(): array
    {
        return [
            'agreement_start_date' => 'date',
            'initial_delivery_date' => 'date',
            'final_delivery_date' => 'date',
            'agreed_amount' => 'decimal:2',
            'first_payment' => 'decimal:2',
            'second_payment' => 'decimal:2',
            'third_payment' => 'decimal:2',
            'fourth_payment' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withDefault([
            'name' => 'غير محدد',
            'employee_id' => '-'
        ]);
    }

    /**
     * حساب المتبقي
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->agreed_amount - ($this->first_payment + $this->second_payment + $this->third_payment + $this->fourth_payment);
    }

    /**
     * حساب إجمالي المدفوع
     */
  // حساب إجمالي المدفوع لكل سجل
public function getTotalPaidAttribute(): float
{
    return $this->first_payment + $this->second_payment + $this->third_payment + $this->fourth_payment;
}

    /**
     * الحصول على أنواع العملاء
     */
    public static function getCustomerTypes(): array
    {
        return [
            'C' => 'C',
            'B' => 'B',
            'A' => 'A',
            'A+' => 'A+',
            'A++' => 'A++',
            'غير محدد' => 'غير محدد',
        ];
    }

    /**
     * الحصول على حالات العمل
     */
    public static function getWorkStatuses(): array
    {
        return [
            'تم الانسحاب' => 'تم الانسحاب',
            'لم يتم الاكمال' => 'لم يتم الاكمال',
            'تم الانتهاء' => 'تم الانتهاء',
            'ديون معدومة' => 'ديون معدومة',
            'جاري العمل' => 'جاري العمل',
        ];
    }

  
// scope للبحث النصي
public function scopeSearch($query, $search)
{
    return $query->where(function ($q) use ($search) {
        $q->where('customer_name', 'like', '%' . $search . '%')
          ->orWhere('customer_phone', 'like', '%' . $search . '%')
          ->orWhere('work_description', 'like', '%' . $search . '%');
    });
}

   
// scope للفترة (سنة وشهر)
public function scopeForPeriod($query, $year, $month)
{
    return $query->whereYear('agreement_start_date', $year)
                 ->whereMonth('agreement_start_date', $month);
}

    /**
     * scope للموظف الحالي
     */
    public function scopeForCurrentEmployee($query)
    {
        return $query->where('employee_id', auth('employee')->id());
    }

  
// الحصول على إجمالي المبالغ المتفق عليها لشهر معين وموظف (اختياري)
public static function getTotalAgreedForMonth($year, $month, $employeeId = null)
{
    $query = self::whereYear('agreement_start_date', $year)
                 ->whereMonth('agreement_start_date', $month);

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }

    return $query->sum('agreed_amount');
}


   
public static function getTotalPaidForMonth($year, $month, $employeeId = null)
{
    $query = self::whereYear('agreement_start_date', $year)
                 ->whereMonth('agreement_start_date', $month);

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }

    // بدل استخدام SUM(first_payment + second_payment + ...) بشكل مباشر،
    // يمكن استخدام دالة sum للحقول منفصلة ثم جمعهم برمجياً إذا كانت هناك مشكلة

    $totalFirstPayment = (clone $query)->sum('first_payment');
    $totalSecondPayment = (clone $query)->sum('second_payment');
    $totalThirdPayment = (clone $query)->sum('third_payment');
    $totalFourthPayment = (clone $query)->sum('fourth_payment');

    return $totalFirstPayment + $totalSecondPayment + $totalThirdPayment + $totalFourthPayment;
}

   
// الحصول على إجمالي الديون لشهر معين وموظف (اختياري)
public static function getTotalDebtsForMonth($year, $month, $employeeId = null)
{
    $query = self::whereYear('agreement_start_date', $year)
                 ->whereMonth('agreement_start_date', $month);

    if ($employeeId) {
        $query->where('employee_id', $employeeId);
    }

    $results = $query->selectRaw('SUM(agreed_amount - (first_payment + second_payment + third_payment + fourth_payment)) as total_debts')->first();

    return $results->total_debts ?? 0;
}
}