<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerChat extends Model
{
    protected $fillable = [
        'potential_customer_id',
        'admin_id',
        'employee_id',
        'status',
        'last_message_at',
        'priority',
        'customer_type'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function potentialCustomer(): BelongsTo
    {
        return $this->belongsTo(PotentialCustomer::class);
    }

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
        return $this->hasMany(CustomerChatMessage::class, 'chat_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    // Accessors
    public function getUnreadCountForEmployeeAttribute()
    {
        return $this->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
    }

    public function getUnreadCountForAdminAttribute()
    {
        return $this->messages()
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->count();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function getCustomerNameAttribute()
    {
        return $this->potentialCustomer->customer_name ?? 'عميل محذوف';
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->potentialCustomer->phone ?? '';
    }

    public function getWorkDescriptionAttribute()
    {
        return $this->potentialCustomer->work_description ?? '';
    }

    public function getCustomerClassificationsAttribute()
    {
        $classifications = $this->potentialCustomer->customer_classifications ?? [];
        if (is_string($classifications)) {
            return json_decode($classifications, true) ?? [];
        }
        return $classifications;
    }

    // Helper Methods
    public function markAsRead($userType)
    {
        $this->messages()
            ->where('sender_type', '!=', $userType)
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

    public function needsCall()
    {
        return in_array('requested_call', $this->customer_classifications);
    }

    public function needsVisit()
    {
        return in_array('requested_visit', $this->customer_classifications);
    }

    public function getPriorityBadgeAttribute()
    {
        switch ($this->priority) {
            case 'high':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">عاجل</span>';
            case 'medium':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">متوسط</span>';
            case 'low':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">منخفض</span>';
            default:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">عادي</span>';
        }
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">نشط</span>';
            case 'pending':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">في الانتظار</span>';
            case 'completed':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">مكتمل</span>';
            case 'closed':
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">مغلق</span>';
            default:
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">غير محدد</span>';
        }
    }

    // إنشاء شات جديد للتواصل بين الموظف والإدارة بخصوص عميل معين
    public static function createForCustomer($potentialCustomerId, $employeeId = null)
    {
        $potentialCustomer = PotentialCustomer::find($potentialCustomerId);
        
        if (!$potentialCustomer) {
            return null;
        }

        // تحديد الأولوية بناء على تصنيفات العميل
        $priority = 'normal';
        $classifications = $potentialCustomer->customer_classifications ?? [];
        if (is_string($classifications)) {
            $classifications = json_decode($classifications, true) ?? [];
        }

        if (in_array('difficult_customer', $classifications)) {
            $priority = 'high';
        } elseif (in_array('requested_call', $classifications) || in_array('requested_visit', $classifications)) {
            $priority = 'medium';
        }

        // تحديد نوع طلب العميل
        $customerType = 'general';
        if (in_array('requested_call', $classifications)) {
            $customerType = 'call_request';
        } elseif (in_array('requested_visit', $classifications)) {
            $customerType = 'visit_request';
        }

        return self::create([
            'potential_customer_id' => $potentialCustomerId,
            'employee_id' => $employeeId,
            'status' => 'pending', // pending = في انتظار التواصل، active = جاري التواصل، completed = تم التواصل
            'priority' => $priority,
            'customer_type' => $customerType,
            'last_message_at' => now()
        ]);
    }

    // البحث عن الشاتات النشطة للعميل
    public static function findByCustomer($potentialCustomerId)
    {
        return self::where('potential_customer_id', $potentialCustomerId)
                   ->where('status', '!=', 'closed')
                   ->first();
    }

     public function getUnreadCountForEmployee()
    {
        return $this->messages()
                   ->where('sender_type', 'admin')
                   ->where('is_read', false)
                   ->count();
    }

    // آخر رسالة
    public function getLastMessage()
    {
        return $this->messages()
                   ->orderBy('created_at', 'desc')
                   ->first();
    }

    // تحديث وقت آخر رسالة تلقائياً
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($chat) {
            if (!$chat->last_message_at) {
                $chat->last_message_at = now();
            }
        });
    }
}