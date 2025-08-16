<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkChat extends Model
{
    protected $fillable = [
        'title',
        'type',
        'admin_id',
        'employee_id',
        'status',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WorkChatMessage::class, 'chat_id');
    }



    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userType, $userId)
    {
        if ($userType === 'admin') {
            return $query->where('admin_id', $userId);
        }
        return $query->where('employee_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors & Mutators
    public function getUnreadCountAttribute()
    {
        return $this->messages()->where('is_read', false)->count();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function getTotalFileSizeAttribute()
    {
        return $this->messages()
            ->whereNotNull('file_size')
            ->sum('file_size');
    }

    // Helper Methods
    public function markAsRead($userType, $userId)
    {
        $this->messages()
            ->where('sender_type', '!=', $userType)
            ->orWhere('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public function updateLastMessage()
    {
        $this->update(['last_message_at' => now()]);
    }

    public function isParticipant($userType, $userId)
    {
        if ($userType === 'admin') {
            return $this->admin_id == $userId;
        }
        return $this->employee_id == $userId;
    }

    public function getOtherParticipant($currentUserType, $currentUserId)
    {
        if ($currentUserType === 'admin') {
            return $this->employee;
        }
        return $this->admin;
    }

    public static function createChat($title, $type, $adminId, $employeeId)
    {
        return self::create([
            'title' => $title,
            'type' => $type,
            'admin_id' => $adminId,
            'employee_id' => $employeeId,
            'status' => 'active'
        ]);
    }

    public function canDelete($userType, $userId)
    {
        // المدير يستطيع حذف أي شات
        if ($userType === 'admin') {
            return true;
        }
        
        // الموظف يستطيع حذف الشاتات التي هو مشارك فيها
        return $this->employee_id == $userId;
    }
}