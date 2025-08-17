<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCommunication extends Model
{
    protected $table = 'customer_communications';

    protected $fillable = [
        'potential_customer_id',
        'employee_id',
        'notes',
        'voice_message',
        'admin_reply',
        'admin_voice_reply',
        'is_read_by_admin',
        'is_read_by_employee',
        'created_at',
        'updated_at',
        // بيانات إضافية مثل مرفقات إذا وجدت
    ];

    public function potentialCustomer()
    {
        return $this->belongsTo(PotentialCustomer::class, 'potential_customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    
    // يمكن إضافة علاقات أخرى إذا لزم
}
