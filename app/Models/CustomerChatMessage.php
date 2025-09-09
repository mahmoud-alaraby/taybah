<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CustomerChatMessage extends Model
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
        'duration' => 'integer',
    ];

    // العلاقات
    public function chat(): BelongsTo
    {
        return $this->belongsTo(CustomerChat::class, 'chat_id');
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

    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    public function scopeFromEmployee($query)
    {
        return $query->where('sender_type', 'employee');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        // التأكد من وجود المسار الصحيح
        $fullPath = 'customer-chat/' . $this->file_path;
        
        // التحقق من وجود الملف
        if (Storage::disk('public')->exists($fullPath)) {
            return asset('storage/' . $fullPath);
        }
        
        return null;
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
        if ($this->sender_type === 'admin') {
            $admin = Admin::find($this->sender_id);
            return $admin ? $admin->name : 'مدير محذوف';
        } else {
            $employee = Employee::find($this->sender_id);
            return $employee ? $employee->name : 'موظف محذوف';
        }
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration || $this->duration <= 0) {
            return '0:00';
        }
        
        $seconds = (int) $this->duration;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    public function getVoiceDurationAttribute()
    {
        if (!$this->duration || $this->duration <= 0) {
            return [
                'formatted' => '0:00',
                'seconds' => 0,
                'minutes' => 0
            ];
        }
        
        $seconds = (int) $this->duration;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        
        return [
            'formatted' => sprintf('%d:%02d', $minutes, $remainingSeconds),
            'seconds' => $remainingSeconds,
            'minutes' => $minutes,
            'total_seconds' => $seconds
        ];
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

    public function getSenderAvatarAttribute()
    {
        if ($this->sender_type === 'admin') {
            return '<div class="w-8 h-8 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white font-medium text-sm">إ</div>';
        } else {
            return '<div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-medium text-sm">م</div>';
        }
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
        if ($this->file_path && Storage::disk('public')->exists('customer-chat/' . $this->file_path)) {
            Storage::disk('public')->delete('customer-chat/' . $this->file_path);
        }
    }

    public function canAccess($userType, $userId)
    {
        return $this->chat->isParticipant($userType, $userId);
    }

    public function canDelete($userType, $userId)
    {
        // فقط المرسل يستطيع حذف الرسالة خلال 5 دقائق من الإرسال
        return $this->sender_type === $userType && 
               $this->sender_id == $userId && 
               $this->created_at->diffInMinutes(now()) <= 5;
    }

    // إنشاء رسائل مختلفة الأنواع
    public static function createTextMessage($chatId, $senderType, $senderId, $content)
    {
        $message = self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'text',
            'content' => $content
        ]);
        
        // تحديث آخر رسالة في الشات
        CustomerChat::find($chatId)->update(['last_message_at' => now()]);
        
        return $message;
    }

    public static function createFileMessage($chatId, $senderType, $senderId, $fileData)
    {
        $message = self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'file',
            'file_path' => $fileData['path'],
            'file_name' => $fileData['name'],
            'file_size' => $fileData['size'],
            'file_type' => $fileData['type']
        ]);
        
        // تحديث آخر رسالة في الشات
        CustomerChat::find($chatId)->update(['last_message_at' => now()]);
        
        return $message;
    }

    public static function createVoiceMessage($chatId, $senderType, $senderId, $voiceData)
    {
        $duration = isset($voiceData['duration']) ? (int) $voiceData['duration'] : 0;
        
        if ($duration > 1000) {
            $duration = round($duration / 1000);
        }
        
        if ($duration > 3600) {
            $duration = 0;
        }

        $message = self::create([
            'chat_id' => $chatId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => 'voice',
            'file_path' => $voiceData['path'],
            'file_name' => $voiceData['name'],
            'file_size' => $voiceData['size'],
            'file_type' => 'audio/webm',
            'duration' => $duration
        ]);

        // تحديث آخر رسالة في الشات
        CustomerChat::find($chatId)->update(['last_message_at' => now()]);

        return $message;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        // حذف الملف عند حذف الرسالة
        static::deleted(function ($message) {
            $message->deleteFile();
        });
    }
}