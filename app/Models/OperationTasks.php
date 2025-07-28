<?php
// app/Models/OperationTask.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationTask extends Model
{
    protected $table = 'operation_tasks';
    
    protected $fillable = [
        'task_date', 
        'task_type', 
        'assigned_person_id', 
        'task_description', 
        'is_reserved', 
        'status', 
        'notes', 
        'created_by'
    ];
    
    protected $casts = [
        'task_date' => 'date',
        'is_reserved' => 'boolean',
    ];
    
    // العلاقة مع الموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'assigned_person_id');
    }
    
    // العلاقة مع من أنشأ المهمة
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
