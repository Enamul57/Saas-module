<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature as Modules;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        // Create company if it doesn't exist
        $company = Company::firstOrCreate(
            ['domain' => 'central.test'],
            [
                'company_name' => 'Tech lab33',
                'domain' => 'central.test',
                'email' => 'info@techlab33.com',
            ]
        );

        $this->command->info('✅ Company ready: ' . $company->id);

        $modules = [
            ['name' => 'User Management'],
            ['name' => 'Role & Permission Management'],
            ['name' => 'Employee Management'],
            ['name' => 'Attendance Management'],
            ['name' => 'Payroll Management'],
            ['name' => 'Leave Management'],
            ['name' => 'Project Management'],
            ['name' => 'Task Management'],
            ['name' => 'Report Management'],
            ['name' => 'Dashboard Management'],
            ['name' => 'Floor Management'],
            ['name' => 'Settings'],
        ];

        $data = collect($modules)->map(fn($module) => [
            'name' => $module['name'],
            'slug' => \Str::slug(strtolower($module['name'])),
            'tenant_id' => $company->id,
        ])->toArray();

        // Use upsert to avoid duplicates
        Modules::upsert($data, ['slug', 'tenant_id'], ['name']);

        $this->command->info('✅ ' . count($data) . ' modules seeded successfully!');
    }
}
