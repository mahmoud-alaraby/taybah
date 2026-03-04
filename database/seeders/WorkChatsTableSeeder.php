<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WorkChatsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('work_chats')->delete();
        
        \DB::table('work_chats')->insert(array (
            0 => 
            array (
                'id' => 8,
                'title' => 'test 5',
                'type' => 'design',
                'admin_id' => 9,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => '2025-09-11 01:11:09',
                'created_at' => '2025-09-11 01:07:02',
                'updated_at' => '2025-09-11 01:11:09',
            ),
            1 => 
            array (
                'id' => 9,
                'title' => 'LOGO',
                'type' => 'design',
                'admin_id' => 1,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => '2025-09-11 01:21:25',
                'created_at' => '2025-09-11 01:21:15',
                'updated_at' => '2025-09-11 01:21:25',
            ),
            2 => 
            array (
                'id' => 10,
                'title' => 'test',
                'type' => 'design',
                'admin_id' => 1,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => '2025-09-11 05:10:42',
                'created_at' => '2025-09-11 04:58:29',
                'updated_at' => '2025-09-11 05:10:42',
            ),
            3 => 
            array (
                'id' => 11,
                'title' => 'looh',
                'type' => 'design',
                'admin_id' => 1,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => NULL,
                'created_at' => '2025-09-11 04:58:34',
                'updated_at' => '2025-09-11 04:58:34',
            ),
            4 => 
            array (
                'id' => 12,
                'title' => 'تصوير 2',
                'type' => 'design',
                'admin_id' => 9,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => '2025-09-22 20:38:01',
                'created_at' => '2025-09-22 20:37:53',
                'updated_at' => '2025-09-22 20:38:01',
            ),
            5 => 
            array (
                'id' => 13,
                'title' => 'صور',
                'type' => 'montage',
                'admin_id' => 9,
                'employee_id' => 16,
                'status' => 'active',
                'last_message_at' => NULL,
                'created_at' => '2025-09-22 20:51:27',
                'updated_at' => '2025-09-22 20:51:27',
            ),
            6 => 
            array (
                'id' => 14,
                'title' => 'هوية',
                'type' => 'design',
                'admin_id' => 9,
                'employee_id' => 15,
                'status' => 'active',
                'last_message_at' => '2025-12-22 08:40:25',
                'created_at' => '2025-09-23 00:36:23',
                'updated_at' => '2025-12-22 08:40:25',
            ),
        ));
        
        
    }
}