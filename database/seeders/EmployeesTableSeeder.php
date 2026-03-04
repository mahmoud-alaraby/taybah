<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('employees')->delete();
        
        \DB::table('employees')->insert(array (
            0 => 
            array (
                'id' => 15,
                'name' => 'محمد',
                'email' => 'mohamed@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '01234567897',
                'employee_id' => 'EMP097',
                'department' => 'design',
                'position' => 'المصمم',
                'salary' => '900.00',
                'hire_date' => '2025-08-07',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-09-11 05:00:00',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-07 12:20:23',
                'updated_at' => '2025-09-11 05:00:00',
            ),
            1 => 
            array (
                'id' => 16,
                'name' => 'أحمد',
                'email' => 'ahmed@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '01143270057',
                'employee_id' => 'EMP297',
                'department' => 'production',
                'position' => 'المونتير',
                'salary' => NULL,
                'hire_date' => '2025-08-07',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-09-03 00:51:30',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-07 12:34:00',
                'updated_at' => '2025-09-03 00:51:30',
            ),
            2 => 
            array (
                'id' => 17,
                'name' => 'كريم',
                'email' => 'karim@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '0534322341',
                'employee_id' => '5EMP057',
                'department' => 'production',
                'position' => 'المصور والمونتير',
                'salary' => NULL,
                'hire_date' => '2025-08-01',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-09-03 00:50:40',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-07 12:35:03',
                'updated_at' => '2025-09-03 00:50:40',
            ),
            3 => 
            array (
                'id' => 18,
                'name' => 'إسلام',
                'email' => 'islam@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '01234567897',
                'employee_id' => 'EMP000',
                'department' => 'customers',
                'position' => 'مسئول خدمة العملاء',
                'salary' => NULL,
                'hire_date' => '2025-08-07',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-11-11 12:27:44',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-07 12:35:54',
                'updated_at' => '2025-11-11 12:27:44',
            ),
            4 => 
            array (
                'id' => 19,
                'name' => 'جمال',
                'email' => 'gamal@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '01000000000',
                'employee_id' => 'EMP011',
                'department' => 'communication',
                'position' => 'مسئول العلاقات العامة',
                'salary' => '8000.02',
                'hire_date' => '2025-08-07',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-11-11 12:32:55',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-07 12:37:19',
                'updated_at' => '2025-11-11 12:32:55',
            ),
            5 => 
            array (
                'id' => 20,
                'name' => 'أبو عمرو',
                'email' => 'aboamr@taybah.com',
                'password' => Hash::make('123456789'),
                'phone' => '0534327651',
                'employee_id' => 'PHT7702',
                'department' => 'tasks',
                'position' => 'المحاسب والمعقب',
                'salary' => '9000.00',
                'hire_date' => '2025-08-17',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-09-26 10:56:34',
                'email_verified_at' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-08-17 16:45:37',
                'updated_at' => '2025-09-26 10:56:34',
            ),
        ));
        
        
    }
}