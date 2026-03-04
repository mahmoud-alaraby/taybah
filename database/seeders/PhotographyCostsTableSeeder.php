<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PhotographyCostsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('photography_costs')->delete();
        
        \DB::table('photography_costs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'date' => '2025-09-06',
                'amount' => '7000.00',
                'type' => 'receipt',
                'note' => 'وصف',
                'created_by' => 1,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-06 23:27:26',
                'updated_at' => '2025-09-06 23:27:26',
            ),
            1 => 
            array (
                'id' => 2,
                'date' => '2025-09-06',
                'amount' => '3000.00',
                'type' => 'payment',
                'note' => 'وو',
                'created_by' => 1,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-06 23:40:27',
                'updated_at' => '2025-09-06 23:40:27',
            ),
            2 => 
            array (
                'id' => 3,
                'date' => '2025-09-07',
                'amount' => '900.00',
                'type' => 'receipt',
                'note' => 'll',
                'created_by' => 18,
                'created_by_type' => 'employee',
                'created_at' => '2025-09-07 00:07:30',
                'updated_at' => '2025-09-07 00:07:30',
            ),
            3 => 
            array (
                'id' => 4,
                'date' => '2025-09-22',
                'amount' => '1000.00',
                'type' => 'receipt',
                'note' => 'تصوير 55',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-22 22:05:14',
                'updated_at' => '2025-09-22 22:05:14',
            ),
            4 => 
            array (
                'id' => 5,
                'date' => '2025-09-22',
                'amount' => '200.00',
                'type' => 'payment',
                'note' => 'مواصلات',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-22 22:05:34',
                'updated_at' => '2025-09-22 22:05:34',
            ),
            5 => 
            array (
                'id' => 6,
                'date' => '2025-09-27',
                'amount' => '80.00',
                'type' => 'receipt',
                'note' => 'نن',
                'created_by' => 1,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-27 00:35:15',
                'updated_at' => '2025-09-27 00:35:15',
            ),
        ));
        
        
    }
}