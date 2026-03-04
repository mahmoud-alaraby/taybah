<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerResponseCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_response_categories')->delete();
        
        \DB::table('customer_response_categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'أسئلة بروفايل',
                'icon' => 'fas fa-clipboard-list',
                'created_at' => '2025-08-04 00:22:54',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'أسئلة تصوير',
                'icon' => NULL,
                'created_at' => '2025-08-04 00:22:54',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'أسئلة تصميم مواقع',
                'icon' => NULL,
                'created_at' => '2025-08-04 00:22:54',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'أسئلة تصميم بشكل عام',
                'icon' => NULL,
                'created_at' => '2025-08-04 00:22:54',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'أسئلة تسويق',
                'icon' => NULL,
                'created_at' => '2025-08-04 00:22:54',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'أسئلة طباعة',
                'icon' => NULL,
                'created_at' => '2025-08-04 00:22:54',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'بروفايل شخصس',
                'icon' => 'fas fa-lightbulb',
                'created_at' => '2025-08-17 16:57:42',
            ),
            7 => 
            array (
                'id' => 9,
                'name' => 'alim MOHAMMAD',
                'icon' => 'fas fa-id-card',
                'created_at' => '2025-09-02 23:38:19',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'Gamal',
                'icon' => 'fas fa-file-alt',
                'created_at' => '2025-09-22 18:49:15',
            ),
        ));
        
        
    }
}