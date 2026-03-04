<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DailyTasksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('daily_tasks')->delete();
        
        \DB::table('daily_tasks')->insert(array (
            0 => 
            array (
                'id' => 47,
                'title' => 'بروفايل شركة بلازا',
                'details' => 'كتابة محتوي البروفايل',
                'task_date' => '2025-09-22',
                'status' => 'completed',
                'completed_at' => '2025-11-10 22:10:14',
                'created_by_admin' => 9,
                'created_by_employee' => NULL,
                'created_at' => '2025-09-22 16:45:47',
                'updated_at' => '2025-11-10 22:10:14',
            ),
            1 => 
            array (
                'id' => 49,
                'title' => 'test',
                'details' => 'test desc',
                'task_date' => '2025-11-10',
                'status' => 'completed',
                'completed_at' => '2025-11-10 22:08:23',
                'created_by_admin' => 1,
                'created_by_employee' => NULL,
                'created_at' => '2025-11-10 21:59:05',
                'updated_at' => '2025-11-10 22:08:23',
            ),
            2 => 
            array (
                'id' => 53,
                'title' => 'ok تتتييستوو',
                'details' => 'okokok0987',
                'task_date' => '2025-11-10',
                'status' => 'completed',
                'completed_at' => '2025-11-10 22:10:01',
                'created_by_admin' => 1,
                'created_by_employee' => NULL,
                'created_at' => '2025-11-10 22:09:42',
                'updated_at' => '2025-11-10 22:46:03',
            ),
        ));
        
        
    }
}