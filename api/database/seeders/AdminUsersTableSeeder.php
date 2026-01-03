<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_users')->delete();
        
        \DB::table('admin_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$12$tKCYObiRniM.7olrHFTjc.23JqbWB7na9wLO4i.vRTEU7z/KZ6EBO',
                'enabled' => 1,
                'name' => 'Administrator',
                'avatar' => NULL,
                'remember_token' => NULL,
                'created_at' => '2025-11-04 00:47:12',
                'updated_at' => '2025-11-04 00:47:12',
            ),
        ));
        
        
    }
}