<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectEmployeesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('project_employees')->delete();
        
        \DB::table('project_employees')->insert(array (
            0 => 
            array (
                'id' => 7,
                'project_id' => 1,
                'employee_id' => 1,
                'role' => 'manager',
                'assigned_by' => 2,
                'assigned_at' => '2025-08-13 00:35:53',
                'created_at' => '2025-08-13 00:35:53',
                'updated_at' => '2025-08-13 00:35:53',
            ),
            1 => 
            array (
                'id' => 8,
                'project_id' => 1,
                'employee_id' => 11,
                'role' => 'developer',
                'assigned_by' => 2,
                'assigned_at' => '2025-08-13 00:35:53',
                'created_at' => '2025-08-13 00:35:53',
                'updated_at' => '2025-08-13 00:35:53',
            ),
            2 => 
            array (
                'id' => 9,
                'project_id' => 1,
                'employee_id' => 12,
                'role' => 'developer',
                'assigned_by' => 2,
                'assigned_at' => '2025-08-13 00:35:53',
                'created_at' => '2025-08-13 00:35:53',
                'updated_at' => '2025-08-13 00:35:53',
            ),
        ));
        
        
    }
}