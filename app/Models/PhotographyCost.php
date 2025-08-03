<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotographyCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'type', 'note',
        'created_by', 'created_by_type'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    // علاقة مع الموظف (في جدول employees)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // علاقة مع الأدمن (في جدول admins)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // اسم المنشئ (موظف أو أدمن)
    public function getCreatorNameAttribute()
    {
        if ($this->created_by_type === 'employee') {
            return optional($this->employee)->name ?? '---';
        }
        if ($this->created_by_type === 'admin') {
            return optional($this->admin)->name ?? '---';
        }
        return '---';
    }
}
