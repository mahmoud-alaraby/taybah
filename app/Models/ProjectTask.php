<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

   protected $fillable = [
        'project_id',
        'name',
        'description',
        'estimated_hours',
        'actual_hours',
        'status',
        'assigned_to',
        'assigned_to_type',
        'created_by',
        'created_by_type',
        'completed_at'
    ];

    protected $casts = [
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // public function assignedEmployee()
    // {
    //     return $this->belongsTo(Employee::class, 'assigned_to');
    // }

    public function timeTracking()
    {
        return $this->hasMany(TimeTracking::class, 'task_id');
    }

    // Accessors
    public function getTotalTrackedHoursAttribute()
    {
        return $this->timeTracking()->sum('total_seconds') / 3600;
    }

    public function getIsOverEstimateAttribute()
    {
        return $this->total_tracked_hours > $this->estimated_hours;
    }

    // Scopes
    public function scopeAssignedTo($query, $employeeId)
    {
        return $query->where('assigned_to', $employeeId);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }


public function timeTrackings()
{
    return $this->hasMany(TimeTracking::class, 'task_id');
}


    /**
     * علاقة المنشئ
     */
    public function creator()
    {
        if ($this->created_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'created_by');
        }
        return $this->belongsTo(Employee::class, 'created_by');
    }


    public function getProgressPercentageAttribute()
    {
        if ($this->estimated_hours <= 0) {
            return 0;
        }
        
        return min(100, round(($this->actual_hours / $this->estimated_hours) * 100, 1));
    }

    /**
     * Scope للمهام النشطة
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Scope للمهام المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
public function assignedEmployee()
{
    // بدون أي شرط assigned_to_type لأنها في جدول المشروع نفسه
    return $this->belongsTo(\App\Models\Employee::class, 'assigned_to');
}

public function assignedAdmin()
{
    return $this->belongsTo(\App\Models\Admin::class, 'assigned_to');
}

public function getAssignedPersonAttribute()
{
    if ($this->assigned_to_type === 'employee') {
        return $this->assignedEmployee;
    } elseif ($this->assigned_to_type === 'admin') {
        return $this->assignedAdmin;
    }
    return null;
}



}

