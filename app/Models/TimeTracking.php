<?php
// تحديث TimeTracking Model لتحديث حالة المهام تلقائياً

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
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
    ];

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->total_seconds / 3600);
        $minutes = floor(($this->total_seconds % 3600) / 60);
        $seconds = $this->total_seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function getHoursAttribute()
    {
        return round($this->total_seconds / 3600, 2);
    }

    // Methods
    public static function startTimer($employeeId, $projectId, $taskId, $description = null)
    {
        // إيقاف أي timer نشط للموظف
        self::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->each(function($timer) {
                $timer->stopTimer();
            });

        return self::create([
            'employee_id' => $employeeId,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'start_time' => now(),
            'description' => $description,
            'date' => Carbon::today(),
            'is_active' => true,
            'total_seconds' => 0,
        ]);
    }

    public function stopTimer()
    {
        $this->end_time = now();
        $this->total_seconds = $this->start_time->diffInSeconds($this->end_time);
        $this->is_active = false;
        $this->save();

        // تحديث actual_hours في المهمة
        if ($this->task) {
            $oldActualHours = $this->task->actual_hours;
            $this->task->increment('actual_hours', $this->hours);
            
            // تحديث حالة المهمة تلقائياً
            $this->updateTaskStatus();
        }

        return $this;
    }

    // تحديث حالة المهمة بناءً على الساعات المنجزة
    private function updateTaskStatus()
    {
        $task = $this->task;
        
        if (!$task || $task->status === 'completed') {
            return;
        }

        $completionPercentage = 0;
        if ($task->estimated_hours > 0) {
            $completionPercentage = ($task->actual_hours / $task->estimated_hours) * 100;
        }

        // تحديث الحالة بناءً على نسبة الإنجاز
        if ($completionPercentage >= 100) {
            $task->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        } elseif ($completionPercentage >= 10 && $task->status === 'pending') {
            $task->update([
                'status' => 'in_progress'
            ]);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}