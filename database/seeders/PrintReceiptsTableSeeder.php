<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PrintReceiptsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('print_receipts')->delete();
        
        \DB::table('print_receipts')->insert(array (
            0 => 
            array (
                'id' => 6,
                'employee_id' => 18,
                'description' => 'jdsj',
                'amount' => '800.00',
                'date' => '2025-08-21',
                'created_at' => '2025-09-27 01:27:49',
                'updated_at' => '2025-09-27 01:27:49',
            ),
        ));
        
        
    }
}