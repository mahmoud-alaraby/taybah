<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'receipts_payments',
                'display_name' => 'سيستم المقبوضات والمدفوعات',
                'description' => 'إدارة المقبوضات والمدفوعات المالية',
                'system_category' => 'financial',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'customer_movement',
                'display_name' => 'سيستم حركة العملاء',
                'description' => 'متابعة وإدارة حركة العملاء',
                'system_category' => 'customers',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'potential_customers',
                'display_name' => 'سيستم العملاء المحتملين',
                'description' => 'إدارة قاعدة العملاء المحتملين',
                'system_category' => 'customers',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'stopwatch_system',
                'display_name' => 'سيستم ستوب وتش',
                'description' => 'نظام توقيت الأعمال والمهام',
                'system_category' => 'operations',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'general_operations',
                'display_name' => 'سيستم التشغيل العام',
                'description' => 'إدارة العمليات التشغيلية العامة',
                'system_category' => 'operations',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'photography_booking',
                'display_name' => 'سيستم حجز وتشغيل التصوير والمونتاج',
                'description' => 'حجز وجدولة أعمال التصوير والمونتاج',
                'system_category' => 'production',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'designers_account',
                'display_name' => 'سيستم حساب المصممين',
                'description' => 'إدارة حسابات ومعاملات المصممين',
                'system_category' => 'design',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'customer_response',
                'display_name' => 'سيستم قاموس الرد علي العملاء',
                'description' => 'قاموس الردود المعيارية للعملاء',
                'system_category' => 'communication',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'task_list',
                'display_name' => 'سيستم قائمة المهام',
                'description' => 'إدارة وتتبع قوائم المهام',
                'system_category' => 'tasks',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'renewal_dates',
                'display_name' => 'سيستم مواعيد التجديد',
                'description' => 'متابعة مواعيد تجديد الخدمات',
                'system_category' => 'scheduling',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'photography_costs',
                'display_name' => 'سيستم تكاليف التصوير',
                'description' => 'حساب وإدارة تكاليف التصوير',
                'system_category' => 'production',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'customer_communication',
                'display_name' => 'سيستم التواصل مع العملاء',
                'description' => 'إدارة قنوات التواصل مع العملاء',
                'system_category' => 'communication',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'design_follow_up',
                'display_name' => 'سيستم متابعة التصميم',
                'description' => 'متابعة مراحل أعمال التصميم',
                'system_category' => 'design',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'montage_follow_up',
                'display_name' => 'سيستم متابعة المونتاج',
                'description' => 'متابعة مراحل أعمال المونتاج',
                'system_category' => 'production',
                'created_at' => '2025-07-25 22:40:53',
                'updated_at' => '2025-07-25 22:40:53',
            ),
            14 => 
            array (
                'id' => 18,
                'name' => 'project_tracking',
                'display_name' => 'متابعة المشاريع والمهام',
                'description' => 'إمكانية متابعة المشاريع واستخدام الاستوب ووتش',
                'system_category' => 'tasks',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            15 => 
            array (
                'id' => 19,
                'name' => 'attendance_tracking',
                'display_name' => 'متابعة الحضور والانصراف',
                'description' => 'إمكانية تسجيل الحضور والانصراف ومتابعة البصمة',
                'system_category' => 'operations',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            16 => 
            array (
                'id' => 20,
                'name' => 'work_reports',
                'display_name' => 'تقارير العمل',
                'description' => 'عرض وطباعة تقارير العمل اليومية والأسبوعية والشهرية',
                'system_category' => 'tasks',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            17 => 
            array (
                'id' => 21,
                'name' => 'projects_management',
                'display_name' => 'إدارة المشاريع',
                'description' => 'إدارة المشاريع والمهام من لوحة الإدارة',
                'system_category' => 'tasks',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            18 => 
            array (
                'id' => 22,
                'name' => 'attendance_management',
                'display_name' => 'إدارة الحضور',
                'description' => 'متابعة حضور الموظفين وإنشاء التقارير',
                'system_category' => 'operations',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            19 => 
            array (
                'id' => 23,
                'name' => 'time_tracking_management',
                'display_name' => 'إدارة متابعة الوقت',
                'description' => 'متابعة وإدارة أوقات عمل الموظفين',
                'system_category' => 'operations',
                'created_at' => '2025-08-12 23:44:04',
                'updated_at' => '2025-08-12 23:44:04',
            ),
            20 => 
            array (
                'id' => 24,
                'name' => 'create_projects',
                'display_name' => 'إنشاء مشاريع جديدة',
                'description' => 'إمكانية إنشاء مشاريع جديدة',
                'system_category' => 'tasks',
                'created_at' => '2025-08-13 00:34:53',
                'updated_at' => '2025-08-13 00:34:53',
            ),
            21 => 
            array (
                'id' => 25,
                'name' => 'manage_own_projects',
                'display_name' => 'إدارة المشاريع الشخصية',
                'description' => 'إدارة المشاريع التي أنشأها الموظف',
                'system_category' => 'tasks',
                'created_at' => '2025-08-13 00:34:53',
                'updated_at' => '2025-08-13 00:34:53',
            ),
            22 => 
            array (
                'id' => 26,
                'name' => 'view_assigned_projects',
                'display_name' => 'عرض المشاريع المخصصة',
                'description' => 'عرض المشاريع التي له مهام فيها فقط',
                'system_category' => 'tasks',
                'created_at' => '2025-08-13 00:34:53',
                'updated_at' => '2025-08-13 00:34:53',
            ),
            23 => 
            array (
                'id' => 27,
                'name' => 'admin_project_tracking',
                'display_name' => 'متابعة المشاريع - أدمن',
                'description' => 'إمكانية استخدام نظام متابعة المشاريع من لوحة الإدارة',
                'system_category' => 'admin',
                'created_at' => '2025-09-09 11:41:15',
                'updated_at' => '2025-09-09 11:41:15',
            ),
            24 => 
            array (
                'id' => 28,
                'name' => 'print_booking',
                'display_name' => 'حجوزات الطباعه',
                'description' => 'إذن لطباعة الحجوزات',
                'system_category' => NULL,
                'created_at' => '2025-09-26 22:30:39',
                'updated_at' => '2025-09-26 22:30:39',
            ),
            25 => 
            array (
                'id' => 29,
                'name' => 'print_costs',
                'display_name' => 'تكاليف الطباعه',
                'description' => 'إذن للاطلاع أو طباعة تكاليف الطباعة',
                'system_category' => NULL,
                'created_at' => '2025-09-26 23:43:58',
                'updated_at' => '2025-09-26 23:43:58',
            ),
        ));
        
        
    }
}