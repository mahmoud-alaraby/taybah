<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_permissions')->delete();
        
        \DB::table('role_permissions')->insert(array (
            0 => 
            array (
                'id' => 16,
                'role_id' => 1,
                'permission_id' => 1,
                'created_at' => '2025-07-26 19:42:51',
                'updated_at' => '2025-07-26 19:42:51',
            ),
            1 => 
            array (
                'id' => 19,
                'role_id' => 6,
                'permission_id' => 6,
                'created_at' => '2025-07-28 15:46:13',
                'updated_at' => '2025-07-28 15:46:13',
            ),
            2 => 
            array (
                'id' => 24,
                'role_id' => 7,
                'permission_id' => 6,
                'created_at' => '2025-07-28 18:46:20',
                'updated_at' => '2025-07-28 18:46:20',
            ),
            3 => 
            array (
                'id' => 28,
                'role_id' => 8,
                'permission_id' => 10,
                'created_at' => '2025-07-29 17:21:20',
                'updated_at' => '2025-07-29 17:21:20',
            ),
            4 => 
            array (
                'id' => 29,
                'role_id' => 9,
                'permission_id' => 2,
                'created_at' => '2025-07-31 15:53:39',
                'updated_at' => '2025-07-31 15:53:39',
            ),
            5 => 
            array (
                'id' => 30,
                'role_id' => 10,
                'permission_id' => 3,
                'created_at' => '2025-07-31 16:05:29',
                'updated_at' => '2025-07-31 16:05:29',
            ),
            6 => 
            array (
                'id' => 31,
                'role_id' => 6,
                'permission_id' => 11,
                'created_at' => '2025-08-03 15:21:40',
                'updated_at' => '2025-08-03 15:21:40',
            ),
            7 => 
            array (
                'id' => 33,
                'role_id' => 4,
                'permission_id' => 7,
                'created_at' => '2025-08-04 19:41:30',
                'updated_at' => '2025-08-04 19:41:30',
            ),
            8 => 
            array (
                'id' => 44,
                'role_id' => 7,
                'permission_id' => 14,
                'created_at' => '2025-08-16 11:27:29',
                'updated_at' => '2025-08-16 11:27:29',
            ),
            9 => 
            array (
                'id' => 45,
                'role_id' => 4,
                'permission_id' => 13,
                'created_at' => '2025-08-16 11:30:09',
                'updated_at' => '2025-08-16 11:30:09',
            ),
            10 => 
            array (
                'id' => 46,
                'role_id' => 9,
                'permission_id' => 12,
                'created_at' => '2025-08-17 13:28:43',
                'updated_at' => '2025-08-17 13:28:43',
            ),
            11 => 
            array (
                'id' => 49,
                'role_id' => 11,
                'permission_id' => 1,
                'created_at' => '2025-08-17 16:46:50',
                'updated_at' => '2025-08-17 16:46:50',
            ),
            12 => 
            array (
                'id' => 50,
                'role_id' => 11,
                'permission_id' => 3,
                'created_at' => '2025-08-17 16:46:50',
                'updated_at' => '2025-08-17 16:46:50',
            ),
            13 => 
            array (
                'id' => 51,
                'role_id' => 12,
                'permission_id' => 5,
                'created_at' => '2025-08-19 20:04:01',
                'updated_at' => '2025-08-19 20:04:01',
            ),
            14 => 
            array (
                'id' => 52,
                'role_id' => 12,
                'permission_id' => 1,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            15 => 
            array (
                'id' => 53,
                'role_id' => 12,
                'permission_id' => 2,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            16 => 
            array (
                'id' => 54,
                'role_id' => 12,
                'permission_id' => 3,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            17 => 
            array (
                'id' => 55,
                'role_id' => 12,
                'permission_id' => 4,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            18 => 
            array (
                'id' => 56,
                'role_id' => 12,
                'permission_id' => 19,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            19 => 
            array (
                'id' => 57,
                'role_id' => 12,
                'permission_id' => 22,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            20 => 
            array (
                'id' => 58,
                'role_id' => 12,
                'permission_id' => 23,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            21 => 
            array (
                'id' => 59,
                'role_id' => 12,
                'permission_id' => 6,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            22 => 
            array (
                'id' => 60,
                'role_id' => 12,
                'permission_id' => 11,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            23 => 
            array (
                'id' => 61,
                'role_id' => 12,
                'permission_id' => 14,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            24 => 
            array (
                'id' => 62,
                'role_id' => 12,
                'permission_id' => 7,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            25 => 
            array (
                'id' => 63,
                'role_id' => 12,
                'permission_id' => 13,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            26 => 
            array (
                'id' => 64,
                'role_id' => 12,
                'permission_id' => 8,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            27 => 
            array (
                'id' => 65,
                'role_id' => 12,
                'permission_id' => 12,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            28 => 
            array (
                'id' => 66,
                'role_id' => 12,
                'permission_id' => 9,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            29 => 
            array (
                'id' => 67,
                'role_id' => 12,
                'permission_id' => 18,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            30 => 
            array (
                'id' => 68,
                'role_id' => 12,
                'permission_id' => 20,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            31 => 
            array (
                'id' => 69,
                'role_id' => 12,
                'permission_id' => 21,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            32 => 
            array (
                'id' => 70,
                'role_id' => 12,
                'permission_id' => 24,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            33 => 
            array (
                'id' => 71,
                'role_id' => 12,
                'permission_id' => 25,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            34 => 
            array (
                'id' => 72,
                'role_id' => 12,
                'permission_id' => 26,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            35 => 
            array (
                'id' => 73,
                'role_id' => 12,
                'permission_id' => 10,
                'created_at' => '2025-08-19 20:06:10',
                'updated_at' => '2025-08-19 20:06:10',
            ),
            36 => 
            array (
                'id' => 74,
                'role_id' => 10,
                'permission_id' => 8,
                'created_at' => '2025-08-19 20:18:25',
                'updated_at' => '2025-08-19 20:18:25',
            ),
            37 => 
            array (
                'id' => 85,
                'role_id' => 9,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:44:55',
                'updated_at' => '2025-08-21 18:44:55',
            ),
            38 => 
            array (
                'id' => 86,
                'role_id' => 9,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            39 => 
            array (
                'id' => 87,
                'role_id' => 9,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            40 => 
            array (
                'id' => 88,
                'role_id' => 9,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            41 => 
            array (
                'id' => 89,
                'role_id' => 9,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            42 => 
            array (
                'id' => 90,
                'role_id' => 9,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            43 => 
            array (
                'id' => 91,
                'role_id' => 9,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            44 => 
            array (
                'id' => 92,
                'role_id' => 9,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            45 => 
            array (
                'id' => 93,
                'role_id' => 9,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:44:56',
                'updated_at' => '2025-08-21 18:44:56',
            ),
            46 => 
            array (
                'id' => 94,
                'role_id' => 8,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            47 => 
            array (
                'id' => 95,
                'role_id' => 8,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            48 => 
            array (
                'id' => 96,
                'role_id' => 8,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            49 => 
            array (
                'id' => 97,
                'role_id' => 8,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            50 => 
            array (
                'id' => 98,
                'role_id' => 8,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            51 => 
            array (
                'id' => 99,
                'role_id' => 8,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            52 => 
            array (
                'id' => 100,
                'role_id' => 8,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            53 => 
            array (
                'id' => 101,
                'role_id' => 8,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            54 => 
            array (
                'id' => 102,
                'role_id' => 8,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:45:33',
                'updated_at' => '2025-08-21 18:45:33',
            ),
            55 => 
            array (
                'id' => 103,
                'role_id' => 7,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            56 => 
            array (
                'id' => 104,
                'role_id' => 7,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            57 => 
            array (
                'id' => 105,
                'role_id' => 7,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            58 => 
            array (
                'id' => 106,
                'role_id' => 7,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            59 => 
            array (
                'id' => 107,
                'role_id' => 7,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            60 => 
            array (
                'id' => 108,
                'role_id' => 7,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            61 => 
            array (
                'id' => 109,
                'role_id' => 7,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            62 => 
            array (
                'id' => 110,
                'role_id' => 7,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            63 => 
            array (
                'id' => 111,
                'role_id' => 7,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            64 => 
            array (
                'id' => 112,
                'role_id' => 7,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:46:17',
                'updated_at' => '2025-08-21 18:46:17',
            ),
            65 => 
            array (
                'id' => 113,
                'role_id' => 6,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            66 => 
            array (
                'id' => 114,
                'role_id' => 6,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            67 => 
            array (
                'id' => 115,
                'role_id' => 6,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            68 => 
            array (
                'id' => 116,
                'role_id' => 6,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            69 => 
            array (
                'id' => 117,
                'role_id' => 6,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            70 => 
            array (
                'id' => 118,
                'role_id' => 6,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            71 => 
            array (
                'id' => 119,
                'role_id' => 6,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            72 => 
            array (
                'id' => 120,
                'role_id' => 6,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            73 => 
            array (
                'id' => 121,
                'role_id' => 6,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            74 => 
            array (
                'id' => 122,
                'role_id' => 6,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:47:29',
                'updated_at' => '2025-08-21 18:47:29',
            ),
            75 => 
            array (
                'id' => 123,
                'role_id' => 1,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            76 => 
            array (
                'id' => 124,
                'role_id' => 1,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            77 => 
            array (
                'id' => 125,
                'role_id' => 1,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            78 => 
            array (
                'id' => 126,
                'role_id' => 1,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            79 => 
            array (
                'id' => 127,
                'role_id' => 1,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            80 => 
            array (
                'id' => 128,
                'role_id' => 1,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            81 => 
            array (
                'id' => 129,
                'role_id' => 1,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            82 => 
            array (
                'id' => 130,
                'role_id' => 1,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            83 => 
            array (
                'id' => 131,
                'role_id' => 1,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            84 => 
            array (
                'id' => 132,
                'role_id' => 1,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:48:12',
                'updated_at' => '2025-08-21 18:48:12',
            ),
            85 => 
            array (
                'id' => 133,
                'role_id' => 4,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            86 => 
            array (
                'id' => 134,
                'role_id' => 4,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            87 => 
            array (
                'id' => 135,
                'role_id' => 4,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            88 => 
            array (
                'id' => 136,
                'role_id' => 4,
                'permission_id' => 23,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            89 => 
            array (
                'id' => 137,
                'role_id' => 4,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            90 => 
            array (
                'id' => 138,
                'role_id' => 4,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            91 => 
            array (
                'id' => 139,
                'role_id' => 4,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            92 => 
            array (
                'id' => 140,
                'role_id' => 4,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            93 => 
            array (
                'id' => 141,
                'role_id' => 4,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            94 => 
            array (
                'id' => 142,
                'role_id' => 4,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:49:27',
                'updated_at' => '2025-08-21 18:49:27',
            ),
            95 => 
            array (
                'id' => 143,
                'role_id' => 5,
                'permission_id' => 4,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            96 => 
            array (
                'id' => 144,
                'role_id' => 5,
                'permission_id' => 19,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            97 => 
            array (
                'id' => 145,
                'role_id' => 5,
                'permission_id' => 22,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            98 => 
            array (
                'id' => 146,
                'role_id' => 5,
                'permission_id' => 18,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            99 => 
            array (
                'id' => 147,
                'role_id' => 5,
                'permission_id' => 20,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            100 => 
            array (
                'id' => 148,
                'role_id' => 5,
                'permission_id' => 21,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            101 => 
            array (
                'id' => 149,
                'role_id' => 5,
                'permission_id' => 24,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            102 => 
            array (
                'id' => 150,
                'role_id' => 5,
                'permission_id' => 25,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            103 => 
            array (
                'id' => 151,
                'role_id' => 5,
                'permission_id' => 26,
                'created_at' => '2025-08-21 18:49:54',
                'updated_at' => '2025-08-21 18:49:54',
            ),
            104 => 
            array (
                'id' => 154,
                'role_id' => 1,
                'permission_id' => 27,
                'created_at' => '2025-09-09 11:42:52',
                'updated_at' => '2025-09-09 11:42:52',
            ),
            105 => 
            array (
                'id' => 170,
                'role_id' => 10,
                'permission_id' => 28,
                'created_at' => '2025-09-26 22:37:04',
                'updated_at' => '2025-09-26 22:37:04',
            ),
            106 => 
            array (
                'id' => 171,
                'role_id' => 10,
                'permission_id' => 29,
                'created_at' => '2025-09-27 00:14:50',
                'updated_at' => '2025-09-27 00:14:50',
            ),
            107 => 
            array (
                'id' => 172,
                'role_id' => 10,
                'permission_id' => 1,
                'created_at' => '2025-09-27 02:29:53',
                'updated_at' => '2025-09-27 02:29:53',
            ),
            108 => 
            array (
                'id' => 174,
                'role_id' => 10,
                'permission_id' => 4,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            109 => 
            array (
                'id' => 177,
                'role_id' => 10,
                'permission_id' => 23,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            110 => 
            array (
                'id' => 178,
                'role_id' => 10,
                'permission_id' => 20,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            111 => 
            array (
                'id' => 179,
                'role_id' => 10,
                'permission_id' => 21,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            112 => 
            array (
                'id' => 180,
                'role_id' => 10,
                'permission_id' => 24,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            113 => 
            array (
                'id' => 181,
                'role_id' => 10,
                'permission_id' => 25,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            114 => 
            array (
                'id' => 182,
                'role_id' => 10,
                'permission_id' => 26,
                'created_at' => '2025-11-10 23:45:16',
                'updated_at' => '2025-11-10 23:45:16',
            ),
            115 => 
            array (
                'id' => 183,
                'role_id' => 10,
                'permission_id' => 9,
                'created_at' => '2025-11-10 23:49:25',
                'updated_at' => '2025-11-10 23:49:25',
            ),
            116 => 
            array (
                'id' => 184,
                'role_id' => 10,
                'permission_id' => 18,
                'created_at' => '2025-11-10 23:49:25',
                'updated_at' => '2025-11-10 23:49:25',
            ),
            117 => 
            array (
                'id' => 185,
                'role_id' => 10,
                'permission_id' => 27,
                'created_at' => '2025-11-10 23:49:25',
                'updated_at' => '2025-11-10 23:49:25',
            ),
        ));
        
        
    }
}