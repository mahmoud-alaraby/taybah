<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PotentialCustomerClassificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('potential_customer_classifications')->delete();
        
        \DB::table('potential_customer_classifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'replied_only',
                'display_name' => 'قام بالرد فقط',
                'description' => 'عميل قام بالرد على الرسالة الأولى فقط',
                'color_class' => 'bg-blue-100 text-blue-800',
                'status' => 'active',
                'sort_order' => 1,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'requested_quote',
                'display_name' => 'طلب عرض أسعار',
                'description' => 'عميل طلب الحصول على عرض أسعار',
                'color_class' => 'bg-yellow-100 text-yellow-800',
                'status' => 'active',
                'sort_order' => 2,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'quote_sent',
                'display_name' => 'ارسلنا له عرض أسعار',
                'description' => 'تم إرسال عرض أسعار للعميل',
                'color_class' => 'bg-green-100 text-green-800',
                'status' => 'active',
                'sort_order' => 3,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'quote_rejected',
                'display_name' => 'رفض عرض الأسعار',
                'description' => 'عميل رفض عرض الأسعار المرسل',
                'color_class' => 'bg-red-100 text-red-800',
                'status' => 'active',
                'sort_order' => 4,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'requested_call',
                'display_name' => 'طلب مكالمة هاتفية',
                'description' => 'عميل طلب التواصل عبر مكالمة هاتفية',
                'color_class' => 'bg-purple-100 text-purple-800',
                'status' => 'active',
                'sort_order' => 5,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'call_completed',
                'display_name' => 'تم التواصل هاتفيا',
                'description' => 'تم إجراء مكالمة هاتفية مع العميل',
                'color_class' => 'bg-indigo-100 text-indigo-800',
                'status' => 'active',
                'sort_order' => 6,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'requested_visit',
                'display_name' => 'طلب زيارة المكتب',
                'description' => 'عميل طلب زيارة المكتب',
                'color_class' => 'bg-pink-100 text-pink-800',
                'status' => 'active',
                'sort_order' => 7,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'visited_office',
                'display_name' => 'تم زيارتنا في المكتب',
                'description' => 'عميل قام بزيارة المكتب',
                'color_class' => 'bg-teal-100 text-teal-800',
                'status' => 'active',
                'sort_order' => 8,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'requested_representative',
                'display_name' => 'طلب ان نرسل له مندوب',
                'description' => 'عميل طلب إرسال مندوب إليه',
                'color_class' => 'bg-orange-100 text-orange-800',
                'status' => 'active',
                'sort_order' => 9,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'difficult_customer',
                'display_name' => 'عميل متعب',
                'description' => 'عميل صعب التعامل معه',
                'color_class' => 'bg-gray-100 text-gray-800',
                'status' => 'active',
                'sort_order' => 10,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'bank_details_sent',
                'display_name' => 'ارسلنا له الحساب البنكي',
                'description' => 'تم إرسال تفاصيل الحساب البنكي',
                'color_class' => 'bg-emerald-100 text-emerald-800',
                'status' => 'active',
                'sort_order' => 11,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'samples_sent',
                'display_name' => 'ارسلنا له نماذج',
                'description' => 'تم إرسال نماذج من الأعمال',
                'color_class' => 'bg-cyan-100 text-cyan-800',
                'status' => 'active',
                'sort_order' => 12,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'samples_viewed',
                'display_name' => 'شاهد النماذج',
                'description' => 'عميل شاهد النماذج المرسلة',
                'color_class' => 'bg-lime-100 text-lime-800',
                'status' => 'active',
                'sort_order' => 13,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'samples_rejected',
                'display_name' => 'رفض النماذج',
                'description' => 'عميل رفض النماذج المرسلة',
                'color_class' => 'bg-rose-100 text-rose-800',
                'status' => 'active',
                'sort_order' => 14,
                'created_at' => '2025-07-27 01:01:39',
                'updated_at' => '2025-07-27 01:01:39',
            ),
        ));
        
        
    }
}