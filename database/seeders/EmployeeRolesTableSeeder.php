<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_roles')->delete();
        
        \DB::table('employee_roles')->insert(array (
            0 => 
            array (
                'id' => 85,
                'employee_id' => 20,
                'role_id' => 1,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 20:43:55',
                'created_at' => '2025-08-19 20:43:55',
                'updated_at' => '2025-08-19 20:43:55',
            ),
            1 => 
            array (
                'id' => 86,
                'employee_id' => 20,
                'role_id' => 8,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 20:43:55',
                'created_at' => '2025-08-19 20:43:55',
                'updated_at' => '2025-08-19 20:43:55',
            ),
            2 => 
            array (
                'id' => 89,
                'employee_id' => 17,
                'role_id' => 6,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 20:54:16',
                'created_at' => '2025-08-19 20:54:16',
                'updated_at' => '2025-08-19 20:54:16',
            ),
            3 => 
            array (
                'id' => 90,
                'employee_id' => 17,
                'role_id' => 7,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 20:54:16',
                'created_at' => '2025-08-19 20:54:16',
                'updated_at' => '2025-08-19 20:54:16',
            ),
            4 => 
            array (
                'id' => 91,
                'employee_id' => 16,
                'role_id' => 7,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 20:58:19',
                'created_at' => '2025-08-19 20:58:19',
                'updated_at' => '2025-08-19 20:58:19',
            ),
            5 => 
            array (
                'id' => 102,
                'employee_id' => 15,
                'role_id' => 4,
                'assigned_by' => 1,
                'assigned_at' => '2025-08-19 21:00:52',
                'created_at' => '2025-08-19 21:00:52',
                'updated_at' => '2025-08-19 21:00:52',
            ),
            6 => 
            array (
                'id' => 103,
                'employee_id' => 19,
                'role_id' => 9,
                'assigned_by' => 1,
                'assigned_at' => '2025-11-10 23:18:33',
                'created_at' => '2025-11-10 23:18:33',
                'updated_at' => '2025-11-10 23:18:33',
            ),
            7 => 
            array (
                'id' => 104,
                'employee_id' => 19,
                'role_id' => 10,
                'assigned_by' => 1,
                'assigned_at' => '2025-11-10 23:18:33',
                'created_at' => '2025-11-10 23:18:33',
                'updated_at' => '2025-11-10 23:18:33',
            ),
            8 => 
            array (
                'id' => 105,
                'employee_id' => 18,
                'role_id' => 9,
                'assigned_by' => 1,
                'assigned_at' => '2025-11-11 00:46:54',
                'created_at' => '2025-11-11 00:46:54',
                'updated_at' => '2025-11-11 00:46:54',
            ),
            9 => 
            array (
                'id' => 106,
                'employee_id' => 18,
                'role_id' => 10,
                'assigned_by' => 1,
                'assigned_at' => '2025-11-11 00:46:54',
                'created_at' => '2025-11-11 00:46:54',
                'updated_at' => '2025-11-11 00:46:54',
            ),
        ));
        
        
    }
}