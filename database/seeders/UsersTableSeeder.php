<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\TenantRole as Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrFail();

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@techlab33.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');
        $this->command->info('Super Admin created: superadmin@techlab33.com / password123');

        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@techlab33.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');
        $this->command->info('Admin created: admin@techlab33.com / password123');

        // Create HR Manager
        $hrManager = User::firstOrCreate(
            ['email' => 'hr@techlab33.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $hrManager->assignRole('hr_manager');
        $this->command->info('HR Manager created: hr@techlab33.com / password123');

        // Create Department Manager
        $deptManager = User::firstOrCreate(
            ['email' => 'manager@techlab33.com'],
            [
                'name' => 'Department Manager',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $deptManager->assignRole('department_manager');
        $this->command->info('Department Manager created: manager@techlab33.com / password123');

        // Create Finance Manager
        $financeManager = User::firstOrCreate(
            ['email' => 'finance@techlab33.com'],
            [
                'name' => 'Finance Manager',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $financeManager->assignRole('finance_manager');
        $this->command->info('Finance Manager created: finance@techlab33.com / password123');

        // Create Regular Employee
        $employee = User::firstOrCreate(
            ['email' => 'employee@techlab33.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'tenant_id' => $company->id,
                'is_active' => true,
            ]
        );
        $employee->assignRole('employee');
        $this->command->info('Employee created: employee@techlab33.com / password123');
    }
}
