<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payments')->delete();
        
        \DB::table('payments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 20,
                'description' => 'مواصلات كريم',
                'amount' => '250.00',
                'date' => '2025-08-30',
                'created_at' => '2025-08-30 16:42:31',
                'updated_at' => '2025-08-30 16:42:31',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 20,
                'description' => 'مياة',
                'amount' => '50.00',
                'date' => '2025-09-02',
                'created_at' => '2025-09-02 23:40:20',
                'updated_at' => '2025-09-02 23:40:20',
            ),
            2 => 
            array (
                'id' => 3,
                'employee_id' => 20,
                'description' => 'شراء دومين للموقع',
                'amount' => '100.00',
                'date' => '2025-09-22',
                'created_at' => '2025-09-22 18:13:55',
                'updated_at' => '2025-09-22 18:13:55',
            ),
            3 => 
            array (
                'id' => 4,
                'employee_id' => 18,
                'description' => 'وصف',
                'amount' => '5000.00',
                'date' => '2025-09-26',
                'created_at' => '2025-09-27 00:24:50',
                'updated_at' => '2025-09-27 00:24:50',
            ),
            4 => 
            array (
                'id' => 5,
                'employee_id' => 18,
                'description' => 'تيست',
                'amount' => '500.00',
                'date' => '2025-09-27',
                'created_at' => '2025-09-27 02:30:14',
                'updated_at' => '2025-09-27 02:30:14',
            ),
        ));
        
        
    }
}