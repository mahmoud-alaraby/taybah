<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DailyWorkSummariesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('daily_work_summaries')->delete();
        
        \DB::table('daily_work_summaries')->insert(array (
            0 => 
            array (
                'id' => 10,
                'employee_id' => 15,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            1 => 
            array (
                'id' => 11,
                'employee_id' => 16,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            2 => 
            array (
                'id' => 12,
                'employee_id' => 17,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            3 => 
            array (
                'id' => 13,
                'employee_id' => 18,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            4 => 
            array (
                'id' => 14,
                'employee_id' => 19,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            5 => 
            array (
                'id' => 15,
                'employee_id' => 20,
                'employee_type' => 'employee',
                'date' => '2025-08-17',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-08-17 16:55:03',
                'updated_at' => '2025-08-17 16:55:03',
            ),
            6 => 
            array (
                'id' => 16,
                'employee_id' => 18,
                'employee_type' => 'employee',
                'date' => '2025-09-09',
                'total_work_hours' => '6.32',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":4,"project_name":"\\u0645\\u0634\\u0631\\u0648\\u0639","hours":6.32,"tasks":[{"task_id":2,"task_name":"\\u0645\\u0647\\u0645\\u0647\\u0629","hours":6.28,"description":null},{"task_id":4,"task_name":"\\u0645\\u0647\\u0645\\u062966","hours":0.04,"description":"\\u0648\\u0635\\u0641"}]}]',
                'daily_target_percentage' => '90.25',
                'created_at' => '2025-09-09 11:17:19',
                'updated_at' => '2025-09-09 17:44:13',
            ),
            7 => 
            array (
                'id' => 17,
                'employee_id' => 1,
                'employee_type' => 'employee',
                'date' => '2025-09-09',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-09-09 13:55:02',
                'updated_at' => '2025-09-09 13:55:02',
            ),
            8 => 
            array (
                'id' => 18,
                'employee_id' => 1,
                'employee_type' => 'admin',
                'date' => '2025-09-09',
                'total_work_hours' => '0.02',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":5,"project_name":"\\u062c\\u062f\\u064a\\u062f","hours":0.02,"tasks":[{"task_id":3,"task_name":"\\u0645\\u0647\\u0645\\u06291","hours":0.02,"description":null}]}]',
                'daily_target_percentage' => '0.34',
                'created_at' => '2025-09-09 16:31:44',
                'updated_at' => '2025-09-09 16:31:44',
            ),
            9 => 
            array (
                'id' => 19,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'date' => '2025-09-09',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-09-09 17:52:25',
                'updated_at' => '2025-09-09 17:52:25',
            ),
            10 => 
            array (
                'id' => 20,
                'employee_id' => 1,
                'employee_type' => 'admin',
                'date' => '2025-09-10',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":5,"project_name":"\\u062c\\u062f\\u064a\\u062f","hours":0,"tasks":[{"task_id":"3","task_name":"\\u0645\\u0647\\u0645\\u06291","hours":0,"description":null}]}]',
                'daily_target_percentage' => '0.06',
                'created_at' => '2025-09-11 01:18:17',
                'updated_at' => '2025-09-11 01:18:17',
            ),
            11 => 
            array (
                'id' => 21,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'date' => '2025-09-16',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-09-16 20:35:22',
                'updated_at' => '2025-09-16 20:35:22',
            ),
            12 => 
            array (
                'id' => 22,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'date' => '2025-09-22',
                'total_work_hours' => '0.04',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":9,"project_name":"\\u0645\\u0634\\u0631\\u0648\\u0639 \\u0631\\u0642\\u0645 1","hours":0.04,"tasks":[{"task_id":"7","task_name":"\\u0628\\u0631\\u0648\\u0641\\u0627\\u064a\\u0644","hours":0.02,"description":"\\u0627\\u0646\\u0634\\u0627\\u0621"},{"task_id":"7","task_name":"\\u0628\\u0631\\u0648\\u0641\\u0627\\u064a\\u0644","hours":0,"description":null},{"task_id":"7","task_name":"\\u0628\\u0631\\u0648\\u0641\\u0627\\u064a\\u0644","hours":0.02,"description":null}]}]',
                'daily_target_percentage' => '0.56',
                'created_at' => '2025-09-22 17:12:41',
                'updated_at' => '2025-09-22 17:12:41',
            ),
            13 => 
            array (
                'id' => 23,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'date' => '2025-09-23',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-09-24 03:25:26',
                'updated_at' => '2025-09-24 03:25:26',
            ),
            14 => 
            array (
                'id' => 24,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'date' => '2025-09-25',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":9,"project_name":"\\u0645\\u0634\\u0631\\u0648\\u0639 \\u0631\\u0642\\u0645 1","hours":0,"tasks":[{"task_id":7,"task_name":"\\u0628\\u0631\\u0648\\u0641\\u0627\\u064a\\u0644","hours":0,"description":null}]}]',
                'daily_target_percentage' => '0.03',
                'created_at' => '2025-09-25 16:43:47',
                'updated_at' => '2025-09-25 14:17:53',
            ),
            15 => 
            array (
                'id' => 25,
                'employee_id' => 20,
                'employee_type' => 'employee',
                'date' => '2025-09-25',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[{"project_id":14,"project_name":"\\u0645\\u0634\\u0631\\u0648\\u0639 \\u0631\\u0642\\u0645 1","hours":0,"tasks":[{"task_id":8,"task_name":"\\u0645\\u062d\\u0627\\u0633\\u0628","hours":0,"description":"dawwad"}]}]',
                'daily_target_percentage' => '0.02',
                'created_at' => '2025-09-25 14:23:56',
                'updated_at' => '2025-09-25 14:23:56',
            ),
            16 => 
            array (
                'id' => 26,
                'employee_id' => 20,
                'employee_type' => 'employee',
                'date' => '2025-09-26',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-09-26 02:46:03',
                'updated_at' => '2025-09-26 02:46:03',
            ),
            17 => 
            array (
                'id' => 27,
                'employee_id' => 18,
                'employee_type' => 'employee',
                'date' => '2025-11-10',
                'total_work_hours' => '0.00',
                'overtime_hours' => '0.00',
                'projects_worked' => '[]',
                'daily_target_percentage' => '0.00',
                'created_at' => '2025-11-10 23:45:40',
                'updated_at' => '2025-11-10 23:45:40',
            ),
        ));
        
        
    }
}