<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReceiptsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('receipts')->delete();
        
        \DB::table('receipts')->insert(array (
            0 => 
            array (
                'id' => 5,
                'employee_id' => 1,
                'description' => 'احمد',
                'amount' => '19999.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:49:36',
                'updated_at' => '2025-07-26 18:49:36',
            ),
            1 => 
            array (
                'id' => 7,
                'employee_id' => 1,
                'description' => 'محمود',
                'amount' => '20000.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:55:10',
                'updated_at' => '2025-07-26 18:55:10',
            ),
            2 => 
            array (
                'id' => 8,
                'employee_id' => 1,
                'description' => 'احمد',
                'amount' => '20003.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:55:26',
                'updated_at' => '2025-07-26 18:55:26',
            ),
            3 => 
            array (
                'id' => 9,
                'employee_id' => 1,
                'description' => 'حسام',
                'amount' => '232121.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:55:35',
                'updated_at' => '2025-07-26 18:55:35',
            ),
            4 => 
            array (
                'id' => 10,
                'employee_id' => 1,
                'description' => 'حسام',
                'amount' => '10000.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:57:56',
                'updated_at' => '2025-07-26 18:57:56',
            ),
            5 => 
            array (
                'id' => 11,
                'employee_id' => 1,
                'description' => 'حسن',
                'amount' => '231321.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:58:04',
                'updated_at' => '2025-07-26 18:58:04',
            ),
            6 => 
            array (
                'id' => 12,
                'employee_id' => 1,
                'description' => 'سيد',
                'amount' => '123123.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:58:08',
                'updated_at' => '2025-07-26 18:58:08',
            ),
            7 => 
            array (
                'id' => 13,
                'employee_id' => 1,
                'description' => 'سيد234',
                'amount' => '323241.00',
                'date' => '2025-07-26',
                'created_at' => '2025-07-26 18:58:13',
                'updated_at' => '2025-07-26 18:58:13',
            ),
            8 => 
            array (
                'id' => 16,
                'employee_id' => 13,
                'description' => 'وصف',
                'amount' => '6000.00',
                'date' => '2025-07-31',
                'created_at' => '2025-07-31 18:00:16',
                'updated_at' => '2025-07-31 18:00:16',
            ),
            9 => 
            array (
                'id' => 18,
                'employee_id' => 20,
                'description' => '0',
                'amount' => '100.00',
                'date' => '2025-08-30',
                'created_at' => '2025-08-30 16:40:43',
                'updated_at' => '2025-08-30 16:40:43',
            ),
            10 => 
            array (
                'id' => 19,
                'employee_id' => 20,
                'description' => 'بروفايل ابو علي',
                'amount' => '500.00',
                'date' => '2025-08-30',
                'created_at' => '2025-08-30 16:41:29',
                'updated_at' => '2025-08-30 16:41:29',
            ),
            11 => 
            array (
                'id' => 20,
                'employee_id' => 20,
                'description' => 'جديد',
                'amount' => '50000.00',
                'date' => '2025-10-14',
                'created_at' => '2025-08-30 16:43:18',
                'updated_at' => '2025-08-30 16:43:18',
            ),
            12 => 
            array (
                'id' => 21,
                'employee_id' => 20,
                'description' => 'بروفايل االنخبة',
                'amount' => '500.00',
                'date' => '2025-09-02',
                'created_at' => '2025-09-02 23:40:03',
                'updated_at' => '2025-09-02 23:40:03',
            ),
            13 => 
            array (
                'id' => 22,
                'employee_id' => 20,
                'description' => 'انشاء موقعب',
                'amount' => '2000.00',
                'date' => '2025-09-26',
                'created_at' => '2025-09-22 18:12:32',
                'updated_at' => '2025-09-26 13:02:43',
            ),
            14 => 
            array (
                'id' => 23,
                'employee_id' => 20,
                'description' => 'مم',
                'amount' => '90.00',
                'date' => '2025-09-27',
                'created_at' => '2025-09-27 00:34:06',
                'updated_at' => '2025-09-27 00:34:06',
            ),
            15 => 
            array (
                'id' => 24,
                'employee_id' => 18,
                'description' => 'مم',
                'amount' => '90.00',
                'date' => '2025-09-27',
                'created_at' => '2025-09-27 00:34:35',
                'updated_at' => '2025-09-27 00:34:35',
            ),
            16 => 
            array (
                'id' => 30,
                'employee_id' => 19,
                'description' => 'وصف',
                'amount' => '500.00',
                'date' => '2025-11-11',
                'created_at' => '2025-11-11 12:41:59',
                'updated_at' => '2025-11-11 12:41:59',
            ),
        ));
        
        
    }
}