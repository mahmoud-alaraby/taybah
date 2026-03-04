<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerMovementTargetsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_movement_targets')->delete();
        
        \DB::table('customer_movement_targets')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => 19,
                'year' => 2025,
                'month' => 9,
                'target_amount' => '8000.00',
                'created_at' => '2025-09-03 16:56:20',
                'updated_at' => '2025-09-03 16:56:20',
            ),
        ));
        
        
    }
}