<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminRolePermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_role_permissions')->delete();
        
        \DB::table('admin_role_permissions')->insert(array (
            0 => 
            array (
                'role_id' => 1,
                'permission_id' => 1,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            1 => 
            array (
                'role_id' => 1,
                'permission_id' => 2,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            2 => 
            array (
                'role_id' => 1,
                'permission_id' => 3,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            3 => 
            array (
                'role_id' => 1,
                'permission_id' => 4,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            4 => 
            array (
                'role_id' => 1,
                'permission_id' => 5,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            5 => 
            array (
                'role_id' => 1,
                'permission_id' => 6,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
            6 => 
            array (
                'role_id' => 1,
                'permission_id' => 7,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
        ));
        
        
    }
}