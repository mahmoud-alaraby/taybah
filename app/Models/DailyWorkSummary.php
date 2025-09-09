<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyWorkSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_type',
        'date',
        'total_work_hours',
        'overtime_hours',
        'projects_worked',
        'daily_target_percentage'
    ];

    protected $casts = [
        'date' => 'date',
        'total_work_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'projects_worked' => 'array',
        'daily_target_percentage' => 'decimal:2',
    ];

    // Relations
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
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForEmployee($query, $employeeId, $type = 'employee')
    {
        return $query->where('employee_id', $employeeId)->where('employee_type', $type);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    // Methods
    public static function generateForEmployee($employeeId, $date, $employeeType = 'employee')
    {
        $timeEntries = TimeTracking::where('employee_id', $employeeId)
            ->where('employee_type', $employeeType)
            ->whereDate('date', $date)
            ->with(['project', 'task'])
            ->get();

        $totalSeconds = $timeEntries->sum('total_seconds');
        $totalHours = $totalSeconds / 3600;
        $overtimeHours = max(0, $totalHours - 7);
        $targetPercentage = $totalHours > 0 ? min(100, ($totalHours / 7) * 100) : 0;

        $projectsWorked = $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
            $project = $entries->first()->project;
            return [
                'project_id' => $projectId,
                'project_name' => $project ? $project->name : 'مشروع محذوف',
                'hours' => round($entries->sum('total_seconds') / 3600, 2),
                'tasks' => $entries->map(function($entry) {
                    return [
                        'task_id' => $entry->task_id,
                        'task_name' => $entry->task ? $entry->task->name : 'مهمة محذوفة',
                        'hours' => round($entry->total_seconds / 3600, 2),
                        'description' => $entry->description
                    ];
                })->toArray()
            ];
        })->values()->toArray();

        return self::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'employee_type' => $employeeType,
                'date' => $date
            ],
            [
                'total_work_hours' => round($totalHours, 2),
                'overtime_hours' => round($overtimeHours, 2),
                'projects_worked' => $projectsWorked,
                'daily_target_percentage' => round($targetPercentage, 2),
            ]
        );
    }

    // Accessors
    public function getFormattedTotalHoursAttribute()
    {
        return $this->formatHoursMinutes($this->total_work_hours);
    }

    public function getFormattedOvertimeHoursAttribute()
    {
        return $this->formatHoursMinutes($this->overtime_hours);
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