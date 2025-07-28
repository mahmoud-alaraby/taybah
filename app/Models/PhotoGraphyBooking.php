<?php
// app/Models/PhotoGraphyBooking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Admin;

class PhotoGraphyBooking extends Model
{
    use HasFactory;

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
    'created_by'
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

    public function notifications()
    {
        return $this->hasMany(BookingNotification::class, 'booking_id');
    }

    // Scope للموظف
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('assigned_person_id', $employeeId);
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
}
