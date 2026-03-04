<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OperationTasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('operation_tasks')->delete();
        
        \DB::table('operation_tasks')->insert(array (
            0 => 
            array (
                'id' => 2,
                'task_date' => '2025-09-02',
                'task_type' => 'design',
                'assigned_person_id' => 20,
                'task_description' => 'تصميم شعار',
                'is_reserved' => 0,
                'status' => 'pending',
                'notes' => NULL,
                'created_by' => 9,
                'created_at' => '2025-09-03 00:38:15',
                'updated_at' => '2025-09-03 00:38:15',
            ),
            1 => 
            array (
                'id' => 3,
                'task_date' => '2025-09-22',
                'task_type' => 'design',
                'assigned_person_id' => 20,
                'task_description' => '7777',
                'is_reserved' => 0,
                'status' => 'pending',
                'notes' => NULL,
                'created_by' => 9,
                'created_at' => '2025-09-22 18:18:35',
                'updated_at' => '2025-09-22 18:18:35',
            ),
        ));
        
        
    }
}