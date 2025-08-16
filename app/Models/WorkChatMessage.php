<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_type',
        'sender_id',
        'message_type',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'duration',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function chat(): BelongsTo
    {
        return $this->belongsTo(WorkChat::class, 'chat_id');
    }

    public function sender()
    {
        if ($this->sender_type === 'admin') {
            return $this->belongsTo(Admin::class, 'sender_id');
        }
        return $this->belongsTo(Employee::class, 'sender_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForChat($query, $chatId)
    {
        return $query->where('chat_id', $chatId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeFiles($query)
    {
        return $query->where('message_type', 'file');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        // رابط مباشر للملف
        return url('storage/work-chat/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getSenderNameAttribute()
    {
        $sender = $this->sender();
        return $sender ? $sender->first()->name ?? 'مستخدم محذوف' : 'مستخدم غير معروف';
    }

    public function getIsFileAttribute()
    {
        return $this->message_type === 'file';
    }

    public function getIsVoiceAttribute()
    {
        return $this->message_type === 'voice';
    }

    public function getIsTextAttribute()
    {
        return $this->message_type === 'text';
    }

    // Helper Methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function deleteFile()
    {
        if ($this->file_path && Storage::disk('public')->exists('work-chat/' . $this->file_path)) {
            Storage::disk('public')->delete('work-chat/' . $this->file_path);
        }
    }

    public function canAccess($userType, $userId)
    {
        return $this->chat->isParticipant($userType, $userId);
    }

    public static function createTextMessage($chatId, $senderType, $senderId, $content)
    {
        return self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'text',
            'content' => $content
        ]);
    }

    public static function createFileMessage($chatId, $senderType, $senderId, $fileData)
    {
        return self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'file',
            'file_path' => $fileData['path'],
            'file_name' => $fileData['name'],
            'file_size' => $fileData['size'],
            'file_type' => $fileData['type']
        ]);
    }

    public static function createVoiceMessage($chatId, $senderType, $senderId, $voiceData)
    {
        return self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'voice',
            'file_path' => $voiceData['path'],
            'file_name' => $voiceData['name'],
            'file_size' => $voiceData['size'],
            'file_type' => 'audio/webm',
            'duration' => $voiceData['duration'] ?? null
        ]);
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        // تحديث last_message_at في الشات عند إنشاء رسالة جديدة
        static::created(function ($message) {
            $message->chat->updateLastMessage();
        });

        // حذف الملف عند حذف الرسالة
        static::deleted(function ($message) {
            $message->deleteFile();
        });
    }
}