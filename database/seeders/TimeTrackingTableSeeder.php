<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TimeTrackingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('time_tracking')->delete();
        
        
        
    }
}