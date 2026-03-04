<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PrintMonthlyTargetsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('print_monthly_targets')->delete();
        
        \DB::table('print_monthly_targets')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 18,
                'year' => 2025,
                'month' => 9,
                'target_amount' => '20000.00',
                'created_at' => '2025-09-27 00:15:05',
                'updated_at' => '2025-09-27 00:40:08',
            ),
        ));
        
        
    }
}