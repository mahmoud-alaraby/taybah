<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerResponse extends Model
{
    use HasFactory;

    protected $table = 'customer_responses'; // اسم الجدول

    protected $fillable = [
        'category_id',
        'title',
        'body',
        'created_by',
        'created_by_type',
    ];

    // علاقة التصنيف لكل رد جاهز
    public function category()
    {
        return $this->belongsTo(CustomerResponseCategory::class, 'category_id');
    }

    // علاقة منشئ الرد (أدمن)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // علاقة منشئ الرد (موظف)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    // دالة مساعدة لجلب اسم المنشئ سواء أدمن أو موظف
    public function getCreatorNameAttribute()
    {
        if ($this->created_by_type === 'admin') {
            return optional($this->admin)->name ?? 'أدمن';
        }
        if ($this->created_by_type === 'employee') {
            return optional($this->employee)->name ?? 'موظف';
        }
        return '---';
    }
}
