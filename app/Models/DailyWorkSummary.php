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
        return $this->belongsTo(Employee::class);
    }

    // Methods
    public static function generateForEmployee($employeeId, $date = null)
    {
        $date = $date ?? Carbon::today();
        
        $timeEntries = TimeTracking::forEmployee($employeeId)
            ->forDate($date)
            ->with(['project', 'task'])
            ->get();

        $totalHours = $timeEntries->sum('hours');
        $overtimeHours = max(0, $totalHours - 7);
        $targetPercentage = min(100, ($totalHours / 7) * 100);

        $projectsWorked = $timeEntries->groupBy('project_id')->map(function($entries, $projectId) {
            $project = $entries->first()->project;
            return [
                'project_id' => $projectId,
                'project_name' => $project->name,
                'hours' => $entries->sum('hours'),
                'tasks' => $entries->map(function($entry) {
                    return [
                        'task_id' => $entry->task_id,
                        'task_name' => $entry->task->name,
                        'hours' => $entry->hours,
                    ];
                })->toArray()
            ];
        })->values()->toArray();

        return self::updateOrCreate(
            ['employee_id' => $employeeId, 'date' => $date],
            [
                'total_work_hours' => $totalHours,
                'overtime_hours' => $overtimeHours,
                'projects_worked' => $projectsWorked,
                'daily_target_percentage' => $targetPercentage,
            ]
        );
    }

    // Scopes
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }
}