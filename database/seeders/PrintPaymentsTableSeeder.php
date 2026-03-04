<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PrintPaymentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('print_payments')->delete();
        
        
        
    }
}