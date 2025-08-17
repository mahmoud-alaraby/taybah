<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCommunicationMessage extends Model
{
    protected $table = 'customer_communication_messages';

    protected $fillable = [
        'chat_id', 'sender_type', 'sender_id', 'message_type',
        'content', 'file_path', 'file_name', 'file_size', 'file_type',
        'duration', 'is_read', 'read_at'
    ];

    // علاقة الرسالة مع الشات
    public function chat()
    {
        return $this->belongsTo(CustomerCommunicationChat::class, 'chat_id');
    }
}
