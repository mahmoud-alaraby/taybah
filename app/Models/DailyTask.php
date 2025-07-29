<?php
// app/Models/DailyTask.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyTask extends Model
{
    protected $fillable = [
        'title', 'details', 'task_date', 'status', 
        'completed_at', 'created_by_admin', 'created_by_employee'
    ];

    protected $casts = [
        'task_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // علاقات
    public function creatorAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin');
    }

    public function creatorEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by_employee');
    }

    // وظيفة ترحيل المهام للغد
    public static function carryOverTasks()
    {
        $today = Carbon::now()->toDateString();
        
        self::where('status', 'pending')
            ->where('task_date', '<', $today)
            ->update(['task_date' => $today]);
    }
}
