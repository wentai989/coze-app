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
            AdminRolesTableSeeder::class,
            AdminPermissionsTableSeeder::class,
            AdminMenusTableSeeder::class,
            AdminRoleUsersTableSeeder::class,
            AdminRolePermissionsTableSeeder::class,
            AdminPermissionMenuTableSeeder::class,
            AdminSettingsTableSeeder::class,
            AdminExtensionsTableSeeder::class,
            AdminApisTableSeeder::class,
            AdminPagesTableSeeder::class,
            AdminRelationshipsTableSeeder::class,
            AdminCodeGeneratorsTableSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
