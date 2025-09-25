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
        'is_active',
        'is_paused',
        'pause_count',
        'resume_count',
        'session_number',
        'parent_session_id',
        'pause_resume_log',
        'is_editable',
        'edited_at',
        'edited_by',
        'original_seconds'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
        'is_active' => 'boolean',
        'is_paused' => 'boolean',
        'pause_count' => 'integer',
        'resume_count' => 'integer',
        'session_number' => 'integer',
        'pause_resume_log' => 'array',
        'is_editable' => 'boolean',
        'edited_at' => 'datetime',
        'total_seconds' => 'integer',
        'original_seconds' => 'integer'
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

    // Parent session (for multiple sessions on same task)
    public function parentSession()
    {
        return $this->belongsTo(TimeTracking::class, 'parent_session_id');
    }

    // Child sessions (for multiple sessions on same task)
    public function childSessions()
    {
        return $this->hasMany(TimeTracking::class, 'parent_session_id');
    }

    // Get all sessions for same task
    public function allTaskSessions()
    {
        if ($this->parent_session_id) {
            // If this is a child session, get parent and all siblings
            return TimeTracking::where(function($query) {
                $query->where('id', $this->parent_session_id)
                      ->orWhere('parent_session_id', $this->parent_session_id);
            })->orderBy('session_number');
        } else {
            // If this is parent or standalone, get self and children
            return TimeTracking::where(function($query) {
                $query->where('id', $this->id)
                      ->orWhere('parent_session_id', $this->id);
            })->orderBy('session_number');
        }
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

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    // Methods
    public function pauseTimer()
    {
        if ($this->is_active && !$this->is_paused) {
            $pauseTime = now();
            
            // Log pause
            $pauseLog = $this->pause_resume_log ?? [];
            $pauseLog[] = [
                'action' => 'pause',
                'time' => $pauseTime->toISOString(),
                'total_seconds_at_pause' => $this->start_time->diffInSeconds($pauseTime)
            ];

            $this->update([
                'is_paused' => true,
                'pause_count' => $this->pause_count + 1,
                'pause_resume_log' => $pauseLog
            ]);
        }
        
        return $this;
    }

    public function resumeTimer()
    {
        if ($this->is_active && $this->is_paused) {
            $resumeTime = now();
            
            // Log resume
            $pauseLog = $this->pause_resume_log ?? [];
            $pauseLog[] = [
                'action' => 'resume',
                'time' => $resumeTime->toISOString()
            ];

            $this->update([
                'is_paused' => false,
                'resume_count' => $this->resume_count + 1,
                'pause_resume_log' => $pauseLog
            ]);
        }
        
        return $this;
    }

    public function stopTimer()
    {
        if ($this->is_active) {
            $endTime = now();
            $totalSeconds = $this->calculateTotalSeconds($endTime);
            
            $this->update([
                'end_time' => $endTime,
                'total_seconds' => $totalSeconds,
                'is_active' => false,
                'is_paused' => false,
                'is_editable' => true, // يمكن تعديله بعد الانتهاء
                'original_seconds' => $totalSeconds
            ]);

            // تحديث actual_hours في المهمة
            if ($this->task) {
                $this->task->increment('actual_hours', $totalSeconds / 3600);
            }
        }
        
        return $this;
    }

    public function restartTimer($description = null)
    {
        // إيقاف أي timer نشط أولاً
        static::where('employee_id', $this->employee_id)
              ->where('employee_type', $this->employee_type)
              ->where('is_active', true)
              ->each(function($timer) {
                  $timer->stopTimer();
              });

        // إنشاء session جديد
        $sessionNumber = $this->getNextSessionNumber();
        $parentId = $this->parent_session_id ?? $this->id;

        $newSession = static::create([
            'employee_id' => $this->employee_id,
            'employee_type' => $this->employee_type,
            'project_id' => $this->project_id,
            'task_id' => $this->task_id,
            'start_time' => now(),
            'description' => $description ?? $this->description,
            'date' => Carbon::today(),
            'is_active' => true,
            'is_paused' => false,
            'pause_count' => 0,
            'resume_count' => 0,
            'session_number' => $sessionNumber,
            'parent_session_id' => $parentId,
            'pause_resume_log' => [],
            'is_editable' => false,
            'total_seconds' => 0
        ]);

        // تحديث حالة المهمة إلى "قيد التنفيذ"
        if ($this->task) {
            $this->task->update(['status' => 'in_progress']);
        }

        return $newSession;
    }

    public function editTime($newSeconds, $editedBy = null, $editedByType = 'admin')
    {
        if (!$this->is_editable || $this->is_active) {
            return false;
        }

        $originalSeconds = $this->original_seconds ?? $this->total_seconds;
        $difference = $newSeconds - $this->total_seconds;

        $this->update([
            'total_seconds' => $newSeconds,
            'edited_at' => now(),
            'edited_by' => $editedBy,
            'original_seconds' => $originalSeconds
        ]);

        // تحديث actual_hours في المهمة
        if ($this->task && $difference != 0) {
            $this->task->increment('actual_hours', $difference / 3600);
        }

        return true;
    }

    private function getNextSessionNumber()
    {
        $taskId = $this->task_id;
        $parentId = $this->parent_session_id ?? $this->id;
        
        return static::where(function($query) use ($parentId) {
            $query->where('id', $parentId)
                  ->orWhere('parent_session_id', $parentId);
        })->max('session_number') + 1;
    }

    private function calculateTotalSeconds($endTime)
    {
        $totalSeconds = 0;
        $currentStart = $this->start_time;
        $pauseLog = $this->pause_resume_log ?? [];
        
        foreach ($pauseLog as $log) {
            if ($log['action'] === 'pause') {
                $pauseTime = Carbon::parse($log['time']);
                $totalSeconds += $currentStart->diffInSeconds($pauseTime);
            } elseif ($log['action'] === 'resume') {
                $currentStart = Carbon::parse($log['time']);
            }
        }
        
        // إضافة الوقت من آخر resume (أو البداية) حتى النهاية
        if (!$this->is_paused) {
            $totalSeconds += $currentStart->diffInSeconds($endTime);
        }
        
        return $totalSeconds;
    }



    // Accessors
    public function getFormattedDurationAttribute()
    {
        return $this->formatHoursMinutes($this->total_seconds / 3600);
    }

    public function getCurrentDurationAttribute()
    {
        if ($this->is_active) {
            if ($this->is_paused) {
                return $this->formatHoursMinutes($this->total_seconds / 3600);
            } else {
                $currentSeconds = $this->calculateTotalSeconds(now());
                return $this->formatHoursMinutes($currentSeconds / 3600);
            }
        }
        return $this->formatted_duration;
    }

    public function getTotalPauseTimeAttribute()
    {
        $totalPauseSeconds = 0;
        $pauseLog = $this->pause_resume_log ?? [];
        $currentPauseStart = null;
        
        foreach ($pauseLog as $log) {
            if ($log['action'] === 'pause') {
                $currentPauseStart = Carbon::parse($log['time']);
            } elseif ($log['action'] === 'resume' && $currentPauseStart) {
                $resumeTime = Carbon::parse($log['time']);
                $totalPauseSeconds += $currentPauseStart->diffInSeconds($resumeTime);
                $currentPauseStart = null;
            }
        }
        
        // إذا كان متوقف حالياً
        if ($this->is_paused && $currentPauseStart) {
            $totalPauseSeconds += $currentPauseStart->diffInSeconds(now());
        }
        
        return $totalPauseSeconds;
    }

    public function getSessionSummaryAttribute()
    {
        $sessions = $this->allTaskSessions()->get();
        
        return [
            'total_sessions' => $sessions->count(),
            'total_time' => $sessions->sum('total_seconds'),
            'total_pauses' => $sessions->sum('pause_count'),
            'total_resumes' => $sessions->sum('resume_count'),
            'sessions_details' => $sessions->map(function($session) {
                return [
                    'session_number' => $session->session_number,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'duration' => $session->formatted_duration,
                    'pause_count' => $session->pause_count,
                    'resume_count' => $session->resume_count,
                    'is_active' => $session->is_active,
                    'is_edited' => $session->edited_at !== null
                ];
            })
        ];
    }

    public function getIsEditedAttribute()
    {
        return $this->edited_at !== null;
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