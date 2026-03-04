<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectTasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('project_tasks')->delete();
        
        \DB::table('project_tasks')->insert(array (
            0 => 
            array (
                'id' => 7,
                'project_id' => 9,
                'name' => 'بروفايل',
                'description' => '0',
                'estimated_hours' => '500.00',
                'actual_hours' => '1.08',
                'status' => 'in_progress',
                'assigned_to' => 9,
                'assigned_to_type' => 'admin',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'completed_at' => NULL,
                'created_at' => '2025-09-16 20:41:18',
                'updated_at' => '2025-09-26 12:55:30',
            ),
            1 => 
            array (
                'id' => 8,
                'project_id' => 14,
                'name' => 'محاسب',
                'description' => '321',
                'estimated_hours' => '2.00',
                'actual_hours' => '0.51',
                'status' => 'in_progress',
                'assigned_to' => 20,
                'assigned_to_type' => 'employee',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'completed_at' => NULL,
                'created_at' => '2025-09-25 14:23:16',
                'updated_at' => '2025-09-26 12:35:03',
            ),
        ));
        
        
    }
}