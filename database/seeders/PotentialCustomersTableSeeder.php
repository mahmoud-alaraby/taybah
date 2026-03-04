<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PotentialCustomersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('potential_customers')->delete();
        
        \DB::table('potential_customers')->insert(array (
            0 => 
            array (
                'id' => 4,
                'employee_id' => 19,
                'customer_name' => 'يصشيشص',
                'work_description' => 'يصشيشص',
                'phone' => 'يشصيشصيصش',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'يصشيشص',
                'created_at' => '2025-09-11 02:08:59',
                'updated_at' => '2025-09-11 02:08:59',
            ),
            1 => 
            array (
                'id' => 5,
                'employee_id' => 18,
                'customer_name' => 'يشصشيص',
                'work_description' => 'شيصيشصيصش',
                'phone' => 'يصششيص',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'يصششيص',
                'created_at' => '2025-09-11 02:32:29',
                'updated_at' => '2025-09-11 02:32:29',
            ),
            2 => 
            array (
                'id' => 6,
                'employee_id' => NULL,
                'customer_name' => 'test',
                'work_description' => 'شصيشصيصشيصش',
                'phone' => 'يصشيشصي',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'يصششيص',
                'created_at' => '2025-09-11 02:39:34',
                'updated_at' => '2025-09-11 02:39:34',
            ),
            3 => 
            array (
                'id' => 7,
                'employee_id' => NULL,
                'customer_name' => 'test2',
                'work_description' => 'dawdaw',
                'phone' => '2132132',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9662132132',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'dwadaw',
                'created_at' => '2025-09-11 02:41:38',
                'updated_at' => '2025-09-11 02:41:38',
            ),
            4 => 
            array (
                'id' => 8,
                'employee_id' => NULL,
                'customer_name' => 'fesfse',
                'work_description' => 'fesefs',
                'phone' => 'efesfs',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'efssef',
                'created_at' => '2025-09-11 02:42:42',
                'updated_at' => '2025-09-11 02:42:42',
            ),
            5 => 
            array (
                'id' => 9,
                'employee_id' => NULL,
                'customer_name' => 'dawdw',
                'work_description' => 'dwadawdaw',
                'phone' => 'dwadaw',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966',
                'customer_classifications' => '["requested_visit"]',
                'notes' => 'dawdaw',
                'created_at' => '2025-09-11 04:28:15',
                'updated_at' => '2025-09-11 04:28:16',
            ),
            6 => 
            array (
                'id' => 10,
                'employee_id' => 18,
                'customer_name' => 'ابو سلمي',
                'work_description' => 'انشاء هوية',
                'phone' => '0500000000',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966500000000',
                'customer_classifications' => '["replied_only"]',
                'notes' => NULL,
                'created_at' => '2025-09-22 18:07:20',
                'updated_at' => '2025-09-22 18:07:20',
            ),
            7 => 
            array (
                'id' => 11,
                'employee_id' => 19,
                'customer_name' => 'عبير كمال',
                'work_description' => 'وصقفغ',
                'phone' => '0534322341',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966534322341',
                'customer_classifications' => '["requested_visit"]',
                'notes' => NULL,
                'created_at' => '2025-11-10 23:20:08',
                'updated_at' => '2025-11-10 23:20:08',
            ),
            8 => 
            array (
                'id' => 12,
                'employee_id' => 18,
                'customer_name' => 'محسن حسن',
                'work_description' => 'وصف',
                'phone' => '09978765',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9669978765',
                'customer_classifications' => '["requested_call"]',
                'notes' => NULL,
                'created_at' => '2025-11-10 23:21:37',
                'updated_at' => '2025-11-11 00:24:25',
            ),
            9 => 
            array (
                'id' => 13,
                'employee_id' => 19,
                'customer_name' => 'علياء نور',
                'work_description' => 'حته',
                'phone' => '0534322341',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966534322341',
                'customer_classifications' => '["requested_call"]',
                'notes' => '08غ786قب',
                'created_at' => '2025-11-11 00:06:05',
                'updated_at' => '2025-11-11 00:06:05',
            ),
            10 => 
            array (
                'id' => 14,
                'employee_id' => 18,
                'customer_name' => 'عاطف',
                'work_description' => 'نمنم',
                'phone' => '01234567897',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9661234567897',
                'customer_classifications' => '["requested_call"]',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:45:44',
                'updated_at' => '2025-11-11 00:45:44',
            ),
            11 => 
            array (
                'id' => 15,
                'employee_id' => 18,
                'customer_name' => 'عزة ناصر',
                'work_description' => 'نكىتن',
                'phone' => '01143270057',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9661143270057',
                'customer_classifications' => '["requested_call"]',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:49:35',
                'updated_at' => '2025-11-11 00:49:35',
            ),
            12 => 
            array (
                'id' => 16,
                'employee_id' => 18,
                'customer_name' => 'رضا السيد احمد',
                'work_description' => 'منلاتنر',
                'phone' => '01234567897',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9661234567897',
                'customer_classifications' => '["requested_visit"]',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:51:04',
                'updated_at' => '2025-11-11 00:51:04',
            ),
            13 => 
            array (
                'id' => 17,
                'employee_id' => 18,
                'customer_name' => 'احمد علي',
                'work_description' => 'حخ',
                'phone' => '0534322341',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=966534322341',
                'customer_classifications' => '["requested_visit"]',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:52:42',
                'updated_at' => '2025-11-11 00:52:42',
            ),
            14 => 
            array (
                'id' => 18,
                'employee_id' => 19,
                'customer_name' => 'عميل تيست',
                'work_description' => 'خناتو',
                'phone' => '01000000000',
                'whatsapp_link' => 'https://api.whatsapp.com/send/?phone=9661000000000',
                'customer_classifications' => '["requested_call"]',
                'notes' => NULL,
                'created_at' => '2025-11-11 00:53:22',
                'updated_at' => '2025-11-11 00:53:22',
            ),
        ));
        
        
    }
}