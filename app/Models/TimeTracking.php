<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeTracking extends Model
{
    use HasFactory;

    protected $table = 'time_trackings';

    protected $fillable = [
        'employee_id',
        'employee_type',
        'project_id',
        'task_id',
        'start_time',
        'end_time',
        'total_seconds',
        'description',
        'date',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'is_active' => 'boolean',
        'total_seconds' => 'integer',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'employee_id');
    }

    // Accessor للحصول على المستخدم حسب النوع
    public function getUserAttribute()
    {
        if ($this->employee_type === 'admin') {
            return $this->admin;
        }
        return $this->employee;
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForEmployee($query, $employeeId, $type = 'employee')
    {
        return $query->where('employee_id', $employeeId)->where('employee_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function stopTimer()
    {
        if ($this->is_active) {
            $endTime = now();
            $totalSeconds = $this->start_time->diffInSeconds($endTime);
            
            $this->update([
                'end_time' => $endTime,
                'total_seconds' => $totalSeconds,
                'is_active' => false,
            ]);

            // تحديث actual_hours في المهمة
            if ($this->task) {
                $this->task->increment('actual_hours', $totalSeconds / 3600);
            }
        }
        
        return $this;
    }

    // Accessor للوقت المنسق
    public function getFormattedDurationAttribute()
    {
        return $this->formatHoursMinutes($this->total_seconds / 3600);
    }

    public function getCurrentDurationAttribute()
    {
        if ($this->is_active) {
            $currentSeconds = $this->start_time->diffInSeconds(now());
            return $this->formatHoursMinutes($currentSeconds / 3600);
        }
        return $this->formatted_duration;
    }

    private function formatHoursMinutes($hours)
    {
        if ($hours < 0) $hours = 0;
        
        $totalMinutes = round($hours * 60);
        $displayHours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%02d:%02d', $displayHours, $minutes);
    }
}