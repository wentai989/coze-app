<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminApisTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_apis')->delete();
        
        \DB::table('admin_apis')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '分类列表',
                'path' => 'select_categorie_options',
                'template' => 'App\\ApiTemplates\\OptionsApi',
                'enabled' => 1,
                'args' => '{"model":"App\\\\Models\\\\Categorie","value_field":"id","label_field":"name"}',
                'created_at' => '2025-11-05 03:21:18',
                'updated_at' => '2025-11-05 03:21:18',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '商户列表',
                'path' => 'select_mch_options',
                'template' => 'App\\ApiTemplates\\OptionsApi',
                'enabled' => 1,
                'args' => '{"value_field":"id","label_field":"name","model":"App\\\\Models\\\\Mch"}',
                'created_at' => '2025-11-21 13:17:41',
                'updated_at' => '2025-11-21 13:17:41',
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '扣子空间',
                'path' => 'select_kont_options',
                'template' => 'App\\ApiTemplates\\OptionsApi',
                'enabled' => 1,
                'args' => '{"model":"App\\\\Models\\\\Kont","value_field":"id","label_field":"name"}',
                'created_at' => '2025-11-28 01:07:47',
                'updated_at' => '2025-11-28 01:07:47',
            ),
        ));
        
        
    }
}