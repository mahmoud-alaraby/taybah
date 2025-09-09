<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographyCost extends Model
{
    protected $fillable = [
        'date',
        'amount', 
        'type',
        'note',
        'created_by',
        'created_by_type'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقات - مُصححة
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Scopes للفلترة
    public function scopeForPeriod($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('note', 'like', '%' . $search . '%');
    }

    public function scopeReceipts($query)
    {
        return $query->where('type', 'receipt');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeByCreator($query, $creatorType, $creatorId)
    {
        return $query->where('created_by_type', $creatorType)
                     ->where('created_by', $creatorId);
    }

    // Accessors - مُصححة
    public function getCreatorNameAttribute()
    {
        if ($this->created_by_type === 'employee') {
            return $this->employee ? $this->employee->name : 'موظف محذوف';
        } elseif ($this->created_by_type === 'admin') {
            return $this->admin ? $this->admin->name : 'مدير محذوف';
        }
        return 'غير محدد';
    }

    public function getCreatorIdAttribute()
    {
        if ($this->created_by_type === 'employee') {
            return $this->employee ? $this->employee->employee_id : 'محذوف';
        } elseif ($this->created_by_type === 'admin') {
            return $this->admin ? 'Admin-' . $this->admin->id : 'محذوف';
        }
        return '';
    }

    public function getTypeTextAttribute()
    {
        return $this->type === 'receipt' ? 'مقبوض' : 'مدفوع';
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('Y-m-d');
    }

    // Static methods للإحصائيات
    public static function getTotalReceipts($year = null, $month = null, $employeeId = null)
    {
        $query = self::where('type', 'receipt');
        
        if ($year && $month) {
            $query->forPeriod($year, $month);
        }
        
        if ($employeeId) {
            $query->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                       ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                       ->where('created_by', $employeeId);
                });
            });
        }
        
        return $query->sum('amount');
    }

    public static function getTotalPayments($year = null, $month = null, $employeeId = null)
    {
        $query = self::where('type', 'payment');
        
        if ($year && $month) {
            $query->forPeriod($year, $month);
        }
        
        if ($employeeId) {
            $query->where(function($q) use ($employeeId) {
                $q->where(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'employee')
                       ->where('created_by', $employeeId);
                })->orWhere(function($qq) use ($employeeId) {
                    $qq->where('created_by_type', 'admin')
                       ->where('created_by', $employeeId);
                });
            });
        }
        
        return $query->sum('amount');
    }

    public static function getNetAmount($year = null, $month = null, $employeeId = null)
    {
        $receipts = self::getTotalReceipts($year, $month, $employeeId);
        $payments = self::getTotalPayments($year, $month, $employeeId);
        
        return $receipts - $payments;
    }
}