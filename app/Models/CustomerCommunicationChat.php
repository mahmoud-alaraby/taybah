<?php
// app/Models/CustomerCommunicationChat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCommunicationChat extends Model
{
    protected $table = 'customer_communication_chats';
    protected $fillable = [
    'potential_customer_id',
    'employee_id',
    'admin_id',
    'status',
    'last_message_at',
    // أي حقول أخرى قابلة للتعبئة الجماعية
];


    public function messages()
    {
        return $this->hasMany(CustomerCommunicationMessage::class, 'chat_id');
    }

    // علاقات أخرى مثل:
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function potentialCustomer()
    {
        return $this->belongsTo(PotentialCustomer::class, 'potential_customer_id');
    }
}
