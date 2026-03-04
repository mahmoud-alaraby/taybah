<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerChatMessagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_chat_messages')->delete();
        
        \DB::table('customer_chat_messages')->insert(array (
            0 => 
            array (
                'id' => 19,
                'chat_id' => 5,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'مرحباً جمال،

تم تكليفك بالتواصل مع العميل: يصشيشص
رقم الهاتف: يشصيشصيصش
وصف العمل: يصشيشص

نوع الطلب: يطلب زيارة للمكتب

يرجى التواصل مع العميل في أقرب وقت ممكن وتحديثي بنتائج المحادثة.',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:09:20',
                'created_at' => '2025-09-11 02:09:15',
                'updated_at' => '2025-09-11 02:09:20',
            ),
            1 => 
            array (
                'id' => 20,
                'chat_id' => 5,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:09:37',
                'created_at' => '2025-09-11 02:09:22',
                'updated_at' => '2025-09-11 02:09:37',
            ),
            2 => 
            array (
                'id' => 21,
                'chat_id' => 5,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:09:37',
                'created_at' => '2025-09-11 02:09:23',
                'updated_at' => '2025-09-11 02:09:37',
            ),
            3 => 
            array (
                'id' => 23,
                'chat_id' => 5,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'مساء الخير',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:11:20',
                'created_at' => '2025-09-11 02:09:52',
                'updated_at' => '2025-09-11 02:11:20',
            ),
            4 => 
            array (
                'id' => 24,
                'chat_id' => 5,
                'sender_type' => 'employee',
                'sender_id' => 19,
                'message_type' => 'text',
                'content' => 'tesr',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:25:12',
                'created_at' => '2025-09-11 02:25:02',
                'updated_at' => '2025-09-11 02:25:12',
            ),
            5 => 
            array (
                'id' => 34,
                'chat_id' => 6,
                'sender_type' => 'employee',
                'sender_id' => 19,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:33:30',
                'created_at' => '2025-09-11 02:33:26',
                'updated_at' => '2025-09-11 02:33:30',
            ),
            6 => 
            array (
                'id' => 35,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 1,
                'message_type' => 'text',
                'content' => 'اهلا',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:33:37',
                'created_at' => '2025-09-11 02:33:35',
                'updated_at' => '2025-09-11 02:33:37',
            ),
            7 => 
            array (
                'id' => 36,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 1,
                'message_type' => 'text',
                'content' => 'اهلا',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:33:37',
                'created_at' => '2025-09-11 02:33:36',
                'updated_at' => '2025-09-11 02:33:37',
            ),
            8 => 
            array (
                'id' => 37,
                'chat_id' => 6,
                'sender_type' => 'employee',
                'sender_id' => 19,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:34:12',
                'created_at' => '2025-09-11 02:33:41',
                'updated_at' => '2025-09-11 02:34:12',
            ),
            9 => 
            array (
                'id' => 38,
                'chat_id' => 6,
                'sender_type' => 'employee',
                'sender_id' => 19,
                'message_type' => 'text',
                'content' => 'مساء الخير',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:34:46',
                'created_at' => '2025-09-11 02:34:45',
                'updated_at' => '2025-09-11 02:34:46',
            ),
            10 => 
            array (
                'id' => 39,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 1,
                'message_type' => 'text',
                'content' => 'test',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 1,
                'read_at' => '2025-09-11 02:34:47',
                'created_at' => '2025-09-11 02:34:46',
                'updated_at' => '2025-09-11 02:34:47',
            ),
            11 => 
            array (
                'id' => 40,
                'chat_id' => 7,
                'sender_type' => 'employee',
                'sender_id' => 18,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-11 02:37:30',
                'updated_at' => '2025-09-11 02:37:30',
            ),
            12 => 
            array (
                'id' => 41,
                'chat_id' => 8,
                'sender_type' => 'employee',
                'sender_id' => 18,
                'message_type' => 'text',
                'content' => 'سلام عليكم',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-11 02:37:41',
                'updated_at' => '2025-09-11 02:37:41',
            ),
            13 => 
            array (
                'id' => 46,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'ازيك',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-11 04:20:38',
                'updated_at' => '2025-09-11 04:20:38',
            ),
            14 => 
            array (
                'id' => 48,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'text',
                'content' => 'الوو',
                'file_path' => NULL,
                'file_name' => NULL,
                'file_size' => NULL,
                'file_type' => NULL,
                'duration' => NULL,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-22 20:34:29',
                'updated_at' => '2025-09-22 20:34:29',
            ),
            15 => 
            array (
                'id' => 51,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'file',
                'content' => NULL,
                'file_path' => 'files/1758730662_nkEokssIYs.png',
                'file_name' => '122491.png',
                'file_size' => 20676,
                'file_type' => 'image/png',
                'duration' => NULL,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-25 02:17:42',
                'updated_at' => '2025-09-25 02:17:42',
            ),
            16 => 
            array (
                'id' => 52,
                'chat_id' => 6,
                'sender_type' => 'admin',
                'sender_id' => 9,
                'message_type' => 'voice',
                'content' => NULL,
                'file_path' => 'voice/1758730685_z5GnVifIsg.webm',
                'file_name' => '1758730685_z5GnVifIsg.webm',
                'file_size' => 236966,
                'file_type' => 'audio/webm',
                'duration' => 15,
                'is_read' => 0,
                'read_at' => NULL,
                'created_at' => '2025-09-25 02:18:05',
                'updated_at' => '2025-09-25 02:18:05',
            ),
        ));
        
        
    }
}