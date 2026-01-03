<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_permissions')->delete();
        
        \DB::table('admin_permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '首页',
                'slug' => 'home',
                'http_method' => NULL,
                'http_path' => '[\'/home*\']',
                'custom_order' => 0,
                'parent_id' => 0,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '系统',
                'slug' => 'system',
                'http_method' => NULL,
                'http_path' => '',
                'custom_order' => 0,
                'parent_id' => 0,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '管理员',
                'slug' => 'admin_users',
                'http_method' => NULL,
                'http_path' => '[\'/admin_users*\']',
                'custom_order' => 0,
                'parent_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '角色',
                'slug' => 'roles',
                'http_method' => NULL,
                'http_path' => '[\'/roles*\']',
                'custom_order' => 0,
                'parent_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '权限',
                'slug' => 'permissions',
                'http_method' => NULL,
                'http_path' => '[\'/permissions*\']',
                'custom_order' => 0,
                'parent_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '菜单',
                'slug' => 'menus',
                'http_method' => NULL,
                'http_path' => '[\'/menus*\']',
                'custom_order' => 0,
                'parent_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '设置',
                'slug' => 'settings',
                'http_method' => NULL,
                'http_path' => '[\'/settings*\']',
                'custom_order' => 0,
                'parent_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
        ));
        
        
    }
}