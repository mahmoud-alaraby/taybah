<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerMovementsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_movements')->delete();
        
        \DB::table('customer_movements')->insert(array (
            0 => 
            array (
                'id' => 1,
                'employee_id' => NULL,
                'customer_name' => 'احمد',
                'customer_phone' => '050321321231',
                'work_description' => 'صيشيشصيصش',
                'agreement_start_date' => '2025-07-26',
                'initial_delivery_date' => '2025-07-31',
                'final_delivery_date' => '2025-08-26',
                'agreed_amount' => '20000.00',
                'first_payment' => '1000.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'C',
                'work_status' => 'تم الانتهاء',
                'created_at' => '2025-07-26 20:17:09',
                'updated_at' => '2025-07-26 21:52:28',
            ),
            1 => 
            array (
                'id' => 3,
                'employee_id' => NULL,
                'customer_name' => 'هدي',
                'customer_phone' => '01276740266',
                'work_description' => 'ةووةو',
                'agreement_start_date' => '2025-07-31',
                'initial_delivery_date' => '2025-08-01',
                'final_delivery_date' => '2025-08-02',
                'agreed_amount' => '4000.00',
                'first_payment' => '1000.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'B',
                'work_status' => 'جاري العمل',
                'created_at' => '2025-07-31 17:59:03',
                'updated_at' => '2025-07-31 17:59:03',
            ),
            2 => 
            array (
                'id' => 4,
                'employee_id' => 19,
                'customer_name' => 'ابو سفيان',
                'customer_phone' => '050000000000',
                'work_description' => 'بروفايل كبير',
                'agreement_start_date' => '2025-09-16',
                'initial_delivery_date' => '2025-09-21',
                'final_delivery_date' => '2025-09-23',
                'agreed_amount' => '5000.00',
                'first_payment' => '100.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'غير محدد',
                'work_status' => 'جاري العمل',
                'created_at' => '2025-09-03 00:24:29',
                'updated_at' => '2025-09-03 00:24:29',
            ),
            3 => 
            array (
                'id' => 5,
                'employee_id' => 19,
                'customer_name' => 'احمد حسن محمد',
                'customer_phone' => '01276740266',
                'work_description' => 'وو',
                'agreement_start_date' => '2025-09-06',
                'initial_delivery_date' => '2025-09-07',
                'final_delivery_date' => '2025-09-20',
                'agreed_amount' => '90.00',
                'first_payment' => '90.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'A++',
                'work_status' => 'جاري العمل',
                'created_at' => '2025-09-06 21:15:40',
                'updated_at' => '2025-09-06 21:24:33',
            ),
            4 => 
            array (
                'id' => 6,
                'employee_id' => 18,
                'customer_name' => 'عاطف',
                'customer_phone' => '98876y66',
                'work_description' => 'llk',
                'agreement_start_date' => '2025-09-06',
                'initial_delivery_date' => '2025-09-13',
                'final_delivery_date' => NULL,
                'agreed_amount' => '90.00',
                'first_payment' => '90.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'B',
                'work_status' => 'جاري العمل',
                'created_at' => '2025-09-06 21:34:56',
                'updated_at' => '2025-09-06 21:34:56',
            ),
            5 => 
            array (
                'id' => 7,
                'employee_id' => 18,
                'customer_name' => 'هدي',
                'customer_phone' => '98876y66',
                'work_description' => 'زززز',
                'agreement_start_date' => '2025-09-06',
                'initial_delivery_date' => '2025-09-10',
                'final_delivery_date' => '2025-10-11',
                'agreed_amount' => '80.00',
                'first_payment' => '90.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '0.00',
                'customer_type' => 'A+',
                'work_status' => 'جاري العمل',
                'created_at' => '2025-09-06 21:50:27',
                'updated_at' => '2025-09-06 21:50:44',
            ),
            6 => 
            array (
                'id' => 8,
                'employee_id' => 19,
                'customer_name' => 'ابو سلمي',
                'customer_phone' => '05525525555',
                'work_description' => 'انشاء ايميل رسمي',
                'agreement_start_date' => '2025-09-22',
                'initial_delivery_date' => '2025-09-22',
                'final_delivery_date' => NULL,
                'agreed_amount' => '250.00',
                'first_payment' => '100.00',
                'second_payment' => '0.00',
                'third_payment' => '0.00',
                'fourth_payment' => '150.00',
                'customer_type' => 'C',
                'work_status' => 'تم الانتهاء',
                'created_at' => '2025-09-22 17:52:44',
                'updated_at' => '2025-09-22 17:52:44',
            ),
        ));
        
        
    }
}