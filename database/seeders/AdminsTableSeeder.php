<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admins')->delete();
        
        \DB::table('admins')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'مدير النظام',
                'email' => 'admin@admin.com',
                'email_verified_at' => NULL,
                'password' => Hash::make('password'),
                'phone' => NULL,
                'role' => 'super_admin',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-11-11 12:33:19',
                'remember_token' => NULL,
                'created_at' => '2025-07-25 18:53:29',
                'updated_at' => '2025-11-11 12:33:19',
            ),
            1 => 
            array (
                'id' => 9,
                'name' => 'عبدالعليم',
                'email' => 'admin@taybah.com',
                'email_verified_at' => NULL,
                'password' => Hash::make('password'),
                'phone' => '0534322348',
                'role' => 'super_admin',
                'status' => 'active',
                'avatar' => NULL,
                'last_login_at' => '2025-12-22 08:37:56',
                'remember_token' => NULL,
                'created_at' => '2025-08-19 21:04:19',
                'updated_at' => '2025-12-22 08:37:56',
            ),
        ));
        
        
    }
}