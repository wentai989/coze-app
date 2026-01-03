<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminPermissionMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_permission_menu')->delete();
        
        \DB::table('admin_permission_menu')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'menu_id' => 1,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            1 => 
            array (
                'permission_id' => 2,
                'menu_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            2 => 
            array (
                'permission_id' => 3,
                'menu_id' => 3,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            3 => 
            array (
                'permission_id' => 2,
                'menu_id' => 3,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            4 => 
            array (
                'permission_id' => 4,
                'menu_id' => 4,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            5 => 
            array (
                'permission_id' => 2,
                'menu_id' => 4,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            6 => 
            array (
                'permission_id' => 5,
                'menu_id' => 5,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            7 => 
            array (
                'permission_id' => 2,
                'menu_id' => 5,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            8 => 
            array (
                'permission_id' => 6,
                'menu_id' => 6,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            9 => 
            array (
                'permission_id' => 2,
                'menu_id' => 6,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            10 => 
            array (
                'permission_id' => 7,
                'menu_id' => 7,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
            11 => 
            array (
                'permission_id' => 2,
                'menu_id' => 7,
                'created_at' => '2025-11-04 00:47:13',
                'updated_at' => '2025-11-04 00:47:13',
            ),
        ));
        
        
    }
}