<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignerTaskAccount extends Model
{
    protected $fillable = [
        'designer_id', 
        'task_date',
        'task1', 'price1', 'desc1',
        'task2', 'price2', 'desc2',
        'task3', 'price3', 'desc3',
        'task4', 'price4', 'desc4',
        'task5', 'price5', 'desc5',
        'created_by_admin'
    ];

    public function designer()
    {
        return $this->belongsTo(Employee::class, 'designer_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin');
    }
}
