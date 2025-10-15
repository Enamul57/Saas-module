<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantRole as Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RolesTableSeeder::class,
            ModulesSeeder::class,
        ]);
        $company = Company::firstOrFail();
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@gmail.com'),
            'tenant_id' => $company->id,
        ]);

        // $user->assignRole('admin');
        $role = Role::firstOrFail();
        $user->roles()->attach([
            $role->id => ['tenant_id' => app('tenant')->id]
        ]);
    }
}
