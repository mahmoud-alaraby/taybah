<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DesignerTaskAccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('designer_task_accounts')->delete();
        
        \DB::table('designer_task_accounts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'designer_id' => 15,
                'task_date' => '2025-09-02',
                'task1' => 'تصميم شعار',
                'price1' => '500.00',
                'desc1' => NULL,
                'task2' => NULL,
                'price2' => NULL,
                'desc2' => NULL,
                'task3' => NULL,
                'price3' => NULL,
                'desc3' => NULL,
                'task4' => NULL,
                'price4' => NULL,
                'desc4' => NULL,
                'task5' => NULL,
                'price5' => NULL,
                'desc5' => NULL,
                'created_by_admin' => 9,
                'created_at' => '2025-09-03 00:42:07',
                'updated_at' => '2025-09-03 00:42:07',
            ),
            1 => 
            array (
                'id' => 2,
                'designer_id' => 15,
                'task_date' => '2025-09-10',
                'task1' => 'مهمه4',
                'price1' => '800.00',
                'desc1' => 'وصصصفف',
                'task2' => NULL,
                'price2' => NULL,
                'desc2' => NULL,
                'task3' => NULL,
                'price3' => NULL,
                'desc3' => NULL,
                'task4' => NULL,
                'price4' => NULL,
                'desc4' => NULL,
                'task5' => NULL,
                'price5' => NULL,
                'desc5' => NULL,
                'created_by_admin' => 1,
                'created_at' => '2025-09-11 05:15:42',
                'updated_at' => '2025-09-11 05:15:42',
            ),
            2 => 
            array (
                'id' => 3,
                'designer_id' => 15,
                'task_date' => '2025-09-22',
                'task1' => 'تصميم هوية',
                'price1' => '600.00',
                'desc1' => NULL,
                'task2' => NULL,
                'price2' => NULL,
                'desc2' => NULL,
                'task3' => NULL,
                'price3' => NULL,
                'desc3' => NULL,
                'task4' => NULL,
                'price4' => NULL,
                'desc4' => NULL,
                'task5' => NULL,
                'price5' => NULL,
                'desc5' => NULL,
                'created_by_admin' => 9,
                'created_at' => '2025-09-22 18:44:43',
                'updated_at' => '2025-09-22 18:44:43',
            ),
            3 => 
            array (
                'id' => 4,
                'designer_id' => 15,
                'task_date' => '2025-09-22',
                'task1' => 'تصميم هوية',
                'price1' => '2000.00',
                'desc1' => NULL,
                'task2' => NULL,
                'price2' => NULL,
                'desc2' => NULL,
                'task3' => NULL,
                'price3' => NULL,
                'desc3' => NULL,
                'task4' => NULL,
                'price4' => NULL,
                'desc4' => NULL,
                'task5' => NULL,
                'price5' => NULL,
                'desc5' => NULL,
                'created_by_admin' => 9,
                'created_at' => '2025-09-22 22:06:14',
                'updated_at' => '2025-09-22 22:06:14',
            ),
            4 => 
            array (
                'id' => 5,
                'designer_id' => 15,
                'task_date' => '2025-09-22',
                'task1' => 'تصميم لوجوو',
                'price1' => '750.00',
                'desc1' => NULL,
                'task2' => NULL,
                'price2' => NULL,
                'desc2' => NULL,
                'task3' => NULL,
                'price3' => NULL,
                'desc3' => NULL,
                'task4' => NULL,
                'price4' => NULL,
                'desc4' => NULL,
                'task5' => NULL,
                'price5' => NULL,
                'desc5' => NULL,
                'created_by_admin' => 9,
                'created_at' => '2025-09-22 22:07:52',
                'updated_at' => '2025-09-22 22:07:52',
            ),
        ));
        
        
    }
}