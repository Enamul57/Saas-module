<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Example roles
        $roles = ['admin'];
        $company = Company::create([
            'company_name' => 'Tech lab33',
            'domain' => 'central.test',
        ]);
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'tenant_id' => $company->id]);
        }
    }
}
