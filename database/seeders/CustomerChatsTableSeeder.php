<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerChatsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_chats')->delete();
        
        \DB::table('customer_chats')->insert(array (
            0 => 
            array (
                'id' => 5,
                'potential_customer_id' => 4,
                'admin_id' => 9,
                'employee_id' => 19,
                'status' => 'active',
                'priority' => 'medium',
                'customer_type' => 'visit_request',
                'last_message_at' => '2025-09-11 02:25:02',
                'created_at' => '2025-09-11 02:09:15',
                'updated_at' => '2025-09-11 02:25:02',
            ),
            1 => 
            array (
                'id' => 6,
                'potential_customer_id' => 5,
                'admin_id' => NULL,
                'employee_id' => 19,
                'status' => 'active',
                'priority' => 'medium',
                'customer_type' => 'visit_request',
                'last_message_at' => '2025-09-25 02:18:05',
                'created_at' => '2025-09-11 02:33:11',
                'updated_at' => '2025-09-25 02:18:05',
            ),
            2 => 
            array (
                'id' => 7,
                'potential_customer_id' => 5,
                'admin_id' => NULL,
                'employee_id' => 18,
                'status' => 'active',
                'priority' => 'medium',
                'customer_type' => 'visit_request',
                'last_message_at' => '2025-09-11 02:37:30',
                'created_at' => '2025-09-11 02:37:19',
                'updated_at' => '2025-09-11 02:37:30',
            ),
            3 => 
            array (
                'id' => 8,
                'potential_customer_id' => 4,
                'admin_id' => NULL,
                'employee_id' => 18,
                'status' => 'active',
                'priority' => 'medium',
                'customer_type' => 'visit_request',
                'last_message_at' => '2025-09-11 02:37:41',
                'created_at' => '2025-09-11 02:37:37',
                'updated_at' => '2025-09-11 02:37:41',
            ),
            4 => 
            array (
                'id' => 25,
                'potential_customer_id' => 13,
                'admin_id' => NULL,
                'employee_id' => 18,
                'status' => 'active',
                'priority' => 'medium',
                'customer_type' => 'call_request',
                'last_message_at' => '2025-11-11 12:38:39',
                'created_at' => '2025-11-11 12:38:39',
                'updated_at' => '2025-11-11 12:38:39',
            ),
        ));
        
        
    }
}