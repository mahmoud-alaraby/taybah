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
        return round($this->timeTracking()->sum('total_seconds') / 3600, 2);
    }

    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();

        if ($totalTasks == 0) {
            return 0;
        }

        // النسبة بناءً على المهام المكتملة
        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        $tasksPercentage = ($completedTasks / $totalTasks) * 100;

        // النسبة بناءً على الساعات
        $totalEstimatedHours = $this->tasks()->sum('estimated_hours');
        $totalActualHours = $this->tasks()->sum('actual_hours');

        $hoursPercentage = 0;
        if ($totalEstimatedHours > 0) {
            $hoursPercentage = min(100, ($totalActualHours / $totalEstimatedHours) * 100);
        }

        // إذا جميع المهام مكتملة، النسبة 100%
        if ($completedTasks == $totalTasks) {
            return 100;
        }

        // إذا تم إنجاز جميع الساعات المطلوبة أو أكثر، النسبة 100%
        if ($totalEstimatedHours > 0 && $totalActualHours >= $totalEstimatedHours) {
            return 100;
        }

        // حساب نسبة المهام قيد التنفيذ
        $inProgressTasks = $this->tasks()->where('status', 'in_progress')->count();

        // النسبة النهائية: أعلى نسبة بين الساعات والمهام
        $finalPercentage = max($hoursPercentage, $tasksPercentage);

        // إضافة نصف نقطة لكل مهمة قيد التنفيذ
        if ($inProgressTasks > 0) {
            $inProgressBonus = ($inProgressTasks / $totalTasks) * 50;
            $finalPercentage += $inProgressBonus;
        }

        return round(min(100, $finalPercentage), 1);
    }


    public function getHoursCompletionPercentageAttribute()
    {
        $totalEstimatedHours = $this->tasks()->sum('estimated_hours');
        $totalActualHours = $this->tasks()->sum('actual_hours');

        if ($totalEstimatedHours <= 0) {
            return 0;
        }

        return round(min(100, ($totalActualHours / $totalEstimatedHours) * 100), 1);
    }

    // نسبة إنجاز بناءً على المهام فقط (مبسطة)
    public function getTasksCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();

        if ($totalTasks == 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        $inProgressTasks = $this->tasks()->where('status', 'in_progress')->count();

        // المهام المكتملة = 100%، قيد التنفيذ = 50%
        $weightedCompletion = ($completedTasks * 1.0) + ($inProgressTasks * 0.5);

        return round(($weightedCompletion / $totalTasks) * 100, 1);
    }


    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->whereHas('tasks', function ($q) use ($employeeId) {
            $q->where('assigned_to', $employeeId);
        });
    }
}
