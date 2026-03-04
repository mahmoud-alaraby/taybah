<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PotentialCustomerClassificationHistoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('potential_customer_classification_history')->delete();
        
        \DB::table('potential_customer_classification_history')->insert(array (
            0 => 
            array (
                'id' => 1,
                'potential_customer_id' => 1,
                'employee_id' => 9,
                'classification_name' => 'replied_only',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:26:56',
            ),
            1 => 
            array (
                'id' => 2,
                'potential_customer_id' => 1,
                'employee_id' => 9,
                'classification_name' => 'requested_quote',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:26:56',
            ),
            2 => 
            array (
                'id' => 3,
                'potential_customer_id' => 1,
                'employee_id' => 9,
                'classification_name' => 'quote_sent',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:26:56',
            ),
            3 => 
            array (
                'id' => 4,
                'potential_customer_id' => 1,
                'employee_id' => 9,
                'classification_name' => 'requested_call',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:26:56',
            ),
            4 => 
            array (
                'id' => 5,
                'potential_customer_id' => 2,
                'employee_id' => 9,
                'classification_name' => 'replied_only',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:31:23',
            ),
            5 => 
            array (
                'id' => 6,
                'potential_customer_id' => 2,
                'employee_id' => 9,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-03 00:31:23',
            ),
            6 => 
            array (
                'id' => 7,
                'potential_customer_id' => 3,
                'employee_id' => 9,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:05:31',
            ),
            7 => 
            array (
                'id' => 8,
                'potential_customer_id' => 4,
                'employee_id' => 9,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:08:59',
            ),
            8 => 
            array (
                'id' => 9,
                'potential_customer_id' => 5,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:32:29',
            ),
            9 => 
            array (
                'id' => 10,
                'potential_customer_id' => 6,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:39:34',
            ),
            10 => 
            array (
                'id' => 11,
                'potential_customer_id' => 7,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:41:38',
            ),
            11 => 
            array (
                'id' => 12,
                'potential_customer_id' => 8,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 02:42:43',
            ),
            12 => 
            array (
                'id' => 13,
                'potential_customer_id' => 9,
                'employee_id' => 9,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-11 04:28:16',
            ),
            13 => 
            array (
                'id' => 14,
                'potential_customer_id' => 10,
                'employee_id' => 9,
                'classification_name' => 'replied_only',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-09-22 18:07:20',
            ),
            14 => 
            array (
                'id' => 15,
                'potential_customer_id' => 11,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-11-10 23:20:08',
            ),
            15 => 
            array (
                'id' => 16,
                'potential_customer_id' => 13,
                'employee_id' => 1,
                'classification_name' => 'requested_call',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:06:05',
            ),
            16 => 
            array (
                'id' => 17,
                'potential_customer_id' => 17,
                'employee_id' => 1,
                'classification_name' => 'requested_visit',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:52:42',
            ),
            17 => 
            array (
                'id' => 18,
                'potential_customer_id' => 18,
                'employee_id' => 1,
                'classification_name' => 'requested_call',
                'action' => 'added',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:53:22',
            ),
        ));
        
        
    }
}