<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerResponsesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_responses')->delete();
        
        \DB::table('customer_responses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category_id' => 1,
                'title' => 'ل',
                'body' => 'للللللللللللللل',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-02 23:39:11',
                'updated_at' => '2025-09-02 23:39:11',
            ),
            1 => 
            array (
                'id' => 2,
                'category_id' => 1,
                'title' => 'سعر البروفايل',
                'body' => '222',
                'created_by' => 9,
                'created_by_type' => 'admin',
                'created_at' => '2025-09-22 21:05:05',
                'updated_at' => '2025-09-22 21:05:05',
            ),
        ));
        
        
    }
}