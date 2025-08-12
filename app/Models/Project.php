<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'client_name',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'created_by_type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relations
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function timeTracking()
    {
        return $this->hasMany(TimeTracking::class);
    }

    public function creator()
    {
        return $this->created_by_type === 'admin' 
            ? $this->belongsTo(Admin::class, 'created_by')
            : $this->belongsTo(Employee::class, 'created_by');
    }

    // Accessors
    public function getTotalHoursAttribute()
    {
        return $this->timeTracking()->sum('total_seconds') / 3600;
    }

    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        
        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->whereHas('tasks', function($q) use ($employeeId) {
            $q->where('assigned_to', $employeeId);
        });
    }
}