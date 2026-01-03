<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUsersTableSeeder::class,
            AdminMenusTableSeeder::class,
            AdminPermissionsTableSeeder::class,
            AdminRoleUsersTableSeeder::class,
            AdminRolePermissionsTableSeeder::class,
            AdminSettingsTableSeeder::class,
        ]);
    }
}
