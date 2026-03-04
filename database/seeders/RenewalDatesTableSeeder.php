<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RenewalDatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('renewal_dates')->delete();
        
        \DB::table('renewal_dates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'تجديد دومين',
                'description' => NULL,
                'renewal_date' => '2025-09-02',
                'frequency' => 'yearly',
                'amount' => NULL,
                'status' => 'active',
                'notification_sent' => 0,
                'last_notification_date' => NULL,
                'next_renewal_date' => '2026-09-02',
                'created_by_admin' => 9,
                'created_by_employee' => NULL,
                'updated_by_admin' => NULL,
                'updated_by_employee' => NULL,
                'created_at' => '2025-09-03 00:42:51',
                'updated_at' => '2025-09-03 00:42:51',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'تجديد الاستضافة',
                'description' => NULL,
                'renewal_date' => '2025-09-22',
                'frequency' => 'monthly',
                'amount' => '30.00',
                'status' => 'active',
                'notification_sent' => 0,
                'last_notification_date' => NULL,
                'next_renewal_date' => '2025-10-22',
                'created_by_admin' => 9,
                'created_by_employee' => NULL,
                'updated_by_admin' => NULL,
                'updated_by_employee' => NULL,
                'created_at' => '2025-09-22 18:43:40',
                'updated_at' => '2025-09-22 18:43:40',
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'ييييييييييييييييييي',
                'description' => NULL,
                'renewal_date' => '2025-09-25',
                'frequency' => 'monthly',
                'amount' => NULL,
                'status' => 'active',
                'notification_sent' => 0,
                'last_notification_date' => NULL,
                'next_renewal_date' => '2025-10-25',
                'created_by_admin' => 9,
                'created_by_employee' => NULL,
                'updated_by_admin' => NULL,
                'updated_by_employee' => NULL,
                'created_at' => '2025-09-25 16:46:14',
                'updated_at' => '2025-09-25 16:46:14',
            ),
        ));
        
        
    }
}