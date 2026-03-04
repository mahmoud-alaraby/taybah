<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'المحاسب',
                'description' => 'له صلاحية الدخول على سيستم المقبوضات والمدفوعات فقط',
                'status' => 'active',
                'created_at' => '2025-07-25 22:41:08',
                'updated_at' => '2025-08-19 20:19:55',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'المصمم',
                'description' => 'له صلاحية الدخول على سيستم متابعة التصميم وسيستم حساب المصممين',
                'status' => 'active',
                'created_at' => '2025-07-25 22:41:08',
                'updated_at' => '2025-08-19 20:13:47',
            ),
            2 => 
            array (
                'id' => 5,
                'name' => 'محرر مونتاج',
                'description' => 'مسؤول عن أعمال المونتاج والتحرير',
                'status' => 'active',
                'created_at' => '2025-07-25 22:41:08',
                'updated_at' => '2025-07-25 22:41:08',
            ),
            3 => 
            array (
                'id' => 6,
                'name' => 'المصور',
                'description' => 'له صلاحية الدخول على سيستم حجز تكاليف التصوير والمونتاج وكذلك سيستم تكاليف التصوير',
                'status' => 'active',
                'created_at' => '2025-07-28 15:46:13',
                'updated_at' => '2025-08-19 20:08:55',
            ),
            4 => 
            array (
                'id' => 7,
                'name' => 'المونتير',
                'description' => 'له صلاحية الدخول على سيستم حجز وتشغيل التصوير والمونتاج وسيستم متابعة المونتاج',
                'status' => 'active',
                'created_at' => '2025-07-28 18:46:19',
                'updated_at' => '2025-08-19 20:12:28',
            ),
            5 => 
            array (
                'id' => 8,
                'name' => 'المعقب',
                'description' => 'له صلاحية الدخول على سيستم مواعيد التجديد',
                'status' => 'active',
                'created_at' => '2025-07-29 17:21:20',
                'updated_at' => '2025-08-19 20:23:25',
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'مسئول العلاقات العامة',
                'description' => 'له صلاحية الدخول على سيستم حركة العملاء وسيستم التواصل مع العملاء',
                'status' => 'active',
                'created_at' => '2025-07-31 15:53:39',
                'updated_at' => '2025-08-19 20:21:35',
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'مسؤول خدمة العملاء',
                'description' => 'له صلاحية الدخول على سيستم قاموس الرد على العملاء وسيستم العملاء المحتملين',
                'status' => 'active',
                'created_at' => '2025-07-31 16:05:29',
                'updated_at' => '2025-08-19 20:18:25',
            ),
            8 => 
            array (
                'id' => 12,
                'name' => 'مدير النظام',
                'description' => 'مدير السيستم هو المسؤول عن تنظيم وإدارة أنظمة المعلومات داخل الشركة، ويشرف على فريق العمل ويتابع تنفيذ المهام التقنية والإدارية',
                'status' => 'active',
                'created_at' => '2025-08-19 20:04:01',
                'updated_at' => '2025-08-19 20:04:58',
            ),
        ));
        
        
    }
}