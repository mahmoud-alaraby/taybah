<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeAttendancesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employee_attendances')->delete();
        
        \DB::table('employee_attendances')->insert(array (
            0 => 
            array (
                'id' => 13,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'check_in_time' => '2025-09-25 14:04:57',
                'check_out_time' => '2025-09-25 14:17:53',
                'is_late' => 1,
                'late_minutes' => -245,
                'total_hours' => '0.21',
                'overtime_hours' => '0.00',
                'date' => '2025-09-25',
                'notes' => NULL,
                'created_at' => '2025-09-25 14:04:57',
                'updated_at' => '2025-09-25 14:17:53',
                'checkout_type' => 'final',
                'temp_checkout_time' => '2025-09-25 14:08:19',
                'temp_checkin_time' => '2025-09-25 14:08:28',
                'temp_checkout_count' => 2,
                'is_temp_out' => 0,
            ),
            1 => 
            array (
                'id' => 14,
                'employee_id' => 20,
                'employee_type' => 'employee',
                'check_in_time' => '2025-09-25 14:21:56',
                'check_out_time' => NULL,
                'is_late' => 1,
                'late_minutes' => -262,
                'total_hours' => '0.00',
                'overtime_hours' => '0.00',
                'date' => '2025-09-25',
                'notes' => NULL,
                'created_at' => '2025-09-25 14:21:56',
                'updated_at' => '2025-09-25 14:43:50',
                'checkout_type' => 'final',
                'temp_checkout_time' => '2025-09-25 14:43:38',
                'temp_checkin_time' => '2025-09-25 14:43:50',
                'temp_checkout_count' => 1,
                'is_temp_out' => 0,
            ),
            2 => 
            array (
                'id' => 15,
                'employee_id' => 9,
                'employee_type' => 'admin',
                'check_in_time' => '2025-09-26 01:52:33',
                'check_out_time' => NULL,
                'is_late' => 0,
                'late_minutes' => 0,
                'total_hours' => '0.00',
                'overtime_hours' => '0.00',
                'date' => '2025-09-26',
                'notes' => NULL,
                'created_at' => '2025-09-26 01:52:33',
                'updated_at' => '2025-09-26 02:32:40',
                'checkout_type' => 'final',
                'temp_checkout_time' => '2025-09-26 02:32:32',
                'temp_checkin_time' => '2025-09-26 02:32:40',
                'temp_checkout_count' => 4,
                'is_temp_out' => 0,
            ),
            3 => 
            array (
                'id' => 16,
                'employee_id' => 20,
                'employee_type' => 'employee',
                'check_in_time' => '2025-09-26 11:07:29',
                'check_out_time' => NULL,
                'is_late' => 1,
                'late_minutes' => -67,
                'total_hours' => '0.00',
                'overtime_hours' => '0.00',
                'date' => '2025-09-26',
                'notes' => NULL,
                'created_at' => '2025-09-26 11:07:29',
                'updated_at' => '2025-09-26 11:08:24',
                'checkout_type' => 'final',
                'temp_checkout_time' => '2025-09-26 11:08:21',
                'temp_checkin_time' => '2025-09-26 11:08:24',
                'temp_checkout_count' => 1,
                'is_temp_out' => 0,
            ),
            4 => 
            array (
                'id' => 17,
                'employee_id' => 1,
                'employee_type' => 'admin',
                'check_in_time' => '2025-11-10 23:48:13',
                'check_out_time' => NULL,
                'is_late' => 1,
                'late_minutes' => -828,
                'total_hours' => '0.00',
                'overtime_hours' => '0.00',
                'date' => '2025-11-10',
                'notes' => NULL,
                'created_at' => '2025-11-10 23:48:13',
                'updated_at' => '2025-11-10 23:48:13',
                'checkout_type' => 'final',
                'temp_checkout_time' => NULL,
                'temp_checkin_time' => NULL,
                'temp_checkout_count' => 0,
                'is_temp_out' => 0,
            ),
            5 => 
            array (
                'id' => 18,
                'employee_id' => 18,
                'employee_type' => 'employee',
                'check_in_time' => '2025-11-10 23:49:41',
                'check_out_time' => NULL,
                'is_late' => 1,
                'late_minutes' => -830,
                'total_hours' => '0.00',
                'overtime_hours' => '0.00',
                'date' => '2025-11-10',
                'notes' => NULL,
                'created_at' => '2025-11-10 23:49:41',
                'updated_at' => '2025-11-10 23:49:41',
                'checkout_type' => 'final',
                'temp_checkout_time' => NULL,
                'temp_checkin_time' => NULL,
                'temp_checkout_count' => 0,
                'is_temp_out' => 0,
            ),
        ));
        
        
    }
}