<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PhotographyBookingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('photography_bookings')->delete();
        
        \DB::table('photography_bookings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'work_description' => 'تصوير 50 صورة',
                'work_notes' => NULL,
                'client_name' => 'تصوير قاعة',
                'client_phone' => '05000000000',
                'agreement' => 'تم الاتفاق علي 1000 ريال ودفع 700 والباقي بعدين',
                'first_session' => '2025-09-16',
                'last_session' => '2025-09-24',
                'sessions_count' => 5,
                'montage_start' => '2025-09-16',
                'initial_delivery' => NULL,
                'final_delivery' => NULL,
                'booking_date' => '2025-09-02',
                'booking_time' => NULL,
                'duration_hours' => 1,
                'location' => 'المملكة العربية السعودية',
                'status' => 'in_progress',
                'notes' => NULL,
                'assigned_person_id' => 17,
                'created_by' => 9,
                'created_by_employee' => NULL,
                'created_at' => '2025-09-02 23:43:43',
                'updated_at' => '2025-09-02 23:43:43',
            ),
            1 => 
            array (
                'id' => 2,
                'work_description' => 'جديد',
                'work_notes' => NULL,
                'client_name' => 'تصوير قاعة 2',
                'client_phone' => '05000000000',
                'agreement' => 'ي',
                'first_session' => NULL,
                'last_session' => NULL,
                'sessions_count' => 1,
                'montage_start' => NULL,
                'initial_delivery' => NULL,
                'final_delivery' => NULL,
                'booking_date' => '2025-09-02',
                'booking_time' => NULL,
                'duration_hours' => 1,
                'location' => NULL,
                'status' => 'in_progress',
                'notes' => NULL,
                'assigned_person_id' => 17,
                'created_by' => 9,
                'created_by_employee' => NULL,
                'created_at' => '2025-09-03 00:40:13',
                'updated_at' => '2025-09-03 00:40:13',
            ),
            2 => 
            array (
                'id' => 3,
                'work_description' => 'تصوير منتجات',
                'work_notes' => NULL,
                'client_name' => 'شركة بلازا',
                'client_phone' => '052662522222',
                'agreement' => 'يوم الاثنين',
                'first_session' => '2025-09-23',
                'last_session' => '2025-09-23',
                'sessions_count' => 2,
                'montage_start' => '2025-09-23',
                'initial_delivery' => '2025-09-23',
                'final_delivery' => '2025-09-23',
                'booking_date' => '2025-09-22',
                'booking_time' => '2025-09-22 14:06:00',
                'duration_hours' => 2,
                'location' => 'الرياض',
                'status' => 'in_progress',
                'notes' => NULL,
                'assigned_person_id' => 17,
                'created_by' => 9,
                'created_by_employee' => NULL,
                'created_at' => '2025-09-22 22:04:04',
                'updated_at' => '2025-09-22 22:04:04',
            ),
            3 => 
            array (
                'id' => 4,
                'work_description' => 'وصف',
                'work_notes' => NULL,
                'client_name' => 'محمد وفاطمة',
                'client_phone' => '0123456789',
                'agreement' => 'اتفاث',
                'first_session' => '2025-09-27',
                'last_session' => '2025-09-28',
                'sessions_count' => 1,
                'montage_start' => '2025-09-27',
                'initial_delivery' => '2025-09-27',
                'final_delivery' => NULL,
                'booking_date' => '2025-09-27',
                'booking_time' => NULL,
                'duration_hours' => 1,
                'location' => NULL,
                'status' => 'in_progress',
                'notes' => NULL,
                'assigned_person_id' => 18,
                'created_by' => NULL,
                'created_by_employee' => 18,
                'created_at' => '2025-09-26 22:40:37',
                'updated_at' => '2025-09-26 22:40:37',
            ),
        ));
        
        
    }
}