<?php
// app/Models/RenewalDate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RenewalDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'renewal_date',
        'frequency',
        'amount',
        'status',
        'notification_sent',
        'last_notification_date',
        'next_renewal_date',
        'created_by_admin',
        'created_by_employee',
        'updated_by_admin',
        'updated_by_employee'
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'last_notification_date' => 'date',
        'next_renewal_date' => 'date',
        'amount' => 'decimal:2',
        'notification_sent' => 'boolean'
    ];

    // العلاقات
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin');
    }

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee');
    }

    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by_admin');
    }

    public function updatedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'updated_by_employee');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query, $days = 3)
    {
        return $query->where('renewal_date', '<=', Carbon::now()->addDays($days))
                    ->where('renewal_date', '>', Carbon::now()->startOfDay())
                    ->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('renewal_date', Carbon::today())
                    ->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('renewal_date', '<', Carbon::now()->startOfDay())
                    ->where('status', 'active');
    }

    // Helper methods
    public function isUpcoming($days = 3)
    {
        return $this->renewal_date <= Carbon::now()->addDays($days) && 
               $this->renewal_date > Carbon::now()->startOfDay() && 
               $this->status === 'active';
    }

    public function isOverdue()
    {
        return $this->renewal_date < Carbon::now()->startOfDay() && $this->status === 'active';
    }

    public function isToday()
    {
        return $this->renewal_date->isToday() && $this->status === 'active';
    }

    public function getDaysUntilRenewal()
    {
        return Carbon::now()->startOfDay()->diffInDays($this->renewal_date, false);
    }

    public function getCreatorName()
    {
        if ($this->createdByAdmin) {
            return $this->createdByAdmin->name;
        }
        return $this->createdByEmployee ? $this->createdByEmployee->name : 'غير محدد';
    }

    public function getUpdaterName()
    {
        if ($this->updatedByAdmin) {
            return $this->updatedByAdmin->name;
        }
        return $this->updatedByEmployee ? $this->updatedByEmployee->name : 'غير محدد';
    }

    public function calculateNextRenewalDate()
    {
        if (!$this->renewal_date) return null;

        $currentDate = Carbon::parse($this->renewal_date);
        
        switch ($this->frequency) {
            case 'yearly':
                return $currentDate->addYear();
            case 'quarterly':
                return $currentDate->addMonths(3);
            case 'monthly':
                return $currentDate->addMonth();
            default:
                return $currentDate->addYear();
        }
    }

    public function getFrequencyDisplayName()
    {
        $frequencies = [
            'yearly' => 'سنوي',
            'quarterly' => 'ربع سنوي',
            'monthly' => 'شهري'
        ];

        return $frequencies[$this->frequency] ?? 'غير محدد';
    }

    public function getStatusDisplayName()
    {
        $statuses = [
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? 'غير محدد';
    }
}
