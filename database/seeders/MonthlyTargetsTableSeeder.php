<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MonthlyTargetsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('monthly_targets')->delete();
        
        \DB::table('monthly_targets')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 20,
                'year' => 2025,
                'month' => 9,
                'target_amount' => '30000.00',
                'created_at' => '2025-09-02 23:40:52',
                'updated_at' => '2025-09-02 23:40:52',
            ),
            1 => 
            array (
                'id' => 2,
                'employee_id' => 18,
                'year' => 2025,
                'month' => 9,
                'target_amount' => '30000.00',
                'created_at' => '2025-09-07 00:07:58',
                'updated_at' => '2025-09-07 00:07:58',
            ),
            2 => 
            array (
                'id' => 3,
                'employee_id' => 20,
                'year' => 2025,
                'month' => 11,
                'target_amount' => '1200.00',
                'created_at' => '2025-11-10 23:03:05',
                'updated_at' => '2025-11-10 23:03:05',
            ),
            3 => 
            array (
                'id' => 4,
                'employee_id' => 18,
                'year' => 2025,
                'month' => 11,
                'target_amount' => '1200.00',
                'created_at' => '2025-11-11 12:34:58',
                'updated_at' => '2025-11-11 12:34:58',
            ),
        ));
        
        
    }
}