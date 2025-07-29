<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoGraphyBooking extends Model
{
    use HasFactory;

    // إضافة هذا السطر لتحديد اسم الجدول الصحيح
    protected $table = 'photography_bookings';

    protected $fillable = [
        'work_description',
        'work_notes',
        'client_name',
        'client_phone',
        'agreement',
        'first_session',
        'last_session',
        'sessions_count',
        'montage_start',
        'initial_delivery',
        'final_delivery',
        'booking_date',
        'booking_time',
        'duration_hours',
        'location',
        'status',
        'notes',
        'assigned_person_id',
        'created_by',
        'created_by_employee'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'first_session' => 'date',
        'last_session' => 'date',
        'montage_start' => 'date',
        'initial_delivery' => 'date',
        'final_delivery' => 'date',
        'booking_time' => 'datetime',
        'duration_hours' => 'integer',
        'sessions_count' => 'integer'
    ];

    // العلاقات
    public function assignedPerson()
    {
        return $this->belongsTo(Employee::class, 'assigned_person_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function employeeCreator()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee');
    }

    public function notifications()
    {
        return $this->hasMany(BookingNotification::class, 'booking_id');
    }

    // Scopes للموظف
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('assigned_person_id', $employeeId);
    }

    public function scopeCreatedByEmployee($query, $employeeId)
    {
        return $query->where('created_by_employee', $employeeId);
    }
    
    public function scopeAssignedByAdmin($query, $employeeId)
    {
        return $query->where('assigned_person_id', $employeeId)
                    ->whereNotNull('created_by')
                    ->whereNull('created_by_employee');
    }
    
    public function scopeNotAssignedToEmployee($query, $employeeId)
    {
        return $query->where(function($q) use ($employeeId) {
            $q->where('assigned_person_id', '!=', $employeeId)
              ->orWhereNull('assigned_person_id');
        })->where(function($q) use ($employeeId) {
            $q->where('created_by_employee', '!=', $employeeId)
              ->orWhereNull('created_by_employee');
        });
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'in_progress' => 'جاري العمل',
            'completed' => 'تم الانتهاء',
            'bad_debt' => 'ديون معدومة'
        ];

        return $labels[$this->status] ?? $this->status;
    }
    
    public function getBookingTypeAttribute()
    {
        if ($this->created_by_employee) {
            return 'created_by_me';
        } elseif ($this->created_by && $this->assigned_person_id) {
            return 'assigned_by_admin';
        } else {
            return 'not_assigned';
        }
    }

    // Helper methods
    public function isAssignedTo($employeeId)
    {
        return $this->assigned_person_id == $employeeId;
    }

    public function isCreatedByEmployee($employeeId)
    {
        return $this->created_by_employee == $employeeId;
    }
    
    public function isAssignedByAdmin($employeeId)
    {
        return $this->assigned_person_id == $employeeId && 
               $this->created_by && 
               !$this->created_by_employee;
    }
}
