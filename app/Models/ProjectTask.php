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

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

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
}