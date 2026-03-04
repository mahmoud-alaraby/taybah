<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('projects')->delete();
        
        \DB::table('projects')->insert(array (
            0 => 
            array (
                'id' => 9,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:30',
                'updated_at' => '2025-09-16 20:40:30',
            ),
            1 => 
            array (
                'id' => 10,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:30',
                'updated_at' => '2025-09-16 20:40:30',
            ),
            2 => 
            array (
                'id' => 11,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:30',
                'updated_at' => '2025-09-16 20:40:30',
            ),
            3 => 
            array (
                'id' => 12,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:30',
                'updated_at' => '2025-09-16 20:40:30',
            ),
            4 => 
            array (
                'id' => 13,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:30',
                'updated_at' => '2025-09-16 20:40:30',
            ),
            5 => 
            array (
                'id' => 14,
                'name' => 'مشروع رقم 1',
                'description' => NULL,
                'client_name' => NULL,
                'start_date' => '2025-09-16',
                'end_date' => NULL,
                'status' => 'active',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-16 20:40:31',
                'updated_at' => '2025-09-16 20:40:31',
            ),
        ));
        
        
    }
}