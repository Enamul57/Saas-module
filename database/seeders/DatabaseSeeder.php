<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Create modules/features
            ModulesSeeder::class,

            // 2. Create all permissions
            PermissionsSeeder::class,

            // 3. Create roles and assign permissions
            RolesTableSeeder::class,

            // 4. Create users and assign roles
            UsersTableSeeder::class,

            // 5. PIM data
            \Modules\PIM\Database\Seeders\PIMDatabaseSeeder::class,
        ]);
    }
}
