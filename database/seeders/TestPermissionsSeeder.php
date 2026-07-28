<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\TenantRole as Role;
use App\Models\TenantPermission as Permission;

class TestPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('domain', 'central.test')->first();

        if (!$company) {
            $this->command->error('❌ Company not found!');
            return;
        }

        $this->command->info('🔐 Assigning permissions for testing...');

        // ============================================
        // 1. GET ALL PERMISSIONS
        // ============================================
        $allPermissions = Permission::where('tenant_id', $company->id)->get();

        $this->command->info('📋 Total permissions available: ' . $allPermissions->count());

        // ============================================
        // 2. ASSIGN PERMISSIONS TO ROLES
        // ============================================

        // SUPER ADMIN - ALL PERMISSIONS
        $superAdminRole = Role::where('name', 'super_admin')
            ->where('tenant_id', $company->id)
            ->first();

        if ($superAdminRole) {
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info('✅ Super Admin: ' . $allPermissions->count() . ' permissions');
        }

        // ADMIN - Most permissions
        $adminRole = Role::where('name', 'admin')
            ->where('tenant_id', $company->id)
            ->first();

        if ($adminRole) {
            $adminPerms = Permission::whereIn('name', [
                'view_dashboard',
                'view_analytics',
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'assign_roles',
                'view_roles',
                'create_roles',
                'edit_roles',
                'delete_roles',
                'view_employees',
                'create_employee',
                'edit_employee',
                'delete_employee',
                'view_employee_details',
                'export_employees',
                'view_jobs',
                'create_job',
                'edit_job',
                'delete_job',
                'view_job_details',
                'view_reports',
                'generate_reports',
                'export_reports',
                'manage_settings',
                'view_settings',
                'view_attendance',
                'view_attendance_reports',
                'view_leaves',
                'approve_leave',
                'view_leave_reports',
                'view_payroll',
                'view_salary_reports',
            ])->where('tenant_id', $company->id)->get();

            $adminRole->syncPermissions($adminPerms);
            $this->command->info('✅ Admin: ' . $adminPerms->count() . ' permissions');
        }

        // HR MANAGER - HR related permissions
        $hrManagerRole = Role::where('name', 'hr_manager')
            ->where('tenant_id', $company->id)
            ->first();

        if ($hrManagerRole) {
            $hrPerms = Permission::whereIn('name', [
                'view_dashboard',
                'view_users',
                'create_users',
                'edit_users',
                'view_employees',
                'create_employee',
                'edit_employee',
                'delete_employee',
                'view_employee_details',
                'export_employees',
                'view_jobs',
                'create_job',
                'edit_job',
                'delete_job',
                'view_job_details',
                'view_attendance',
                'view_attendance_reports',
                'view_leaves',
                'approve_leave',
                'view_leave_reports',
                'view_payroll',
                'view_salary_reports',
                'view_reports',
                'generate_reports',
                'export_reports',
            ])->where('tenant_id', $company->id)->get();

            $hrManagerRole->syncPermissions($hrPerms);
            $this->command->info('✅ HR Manager: ' . $hrPerms->count() . ' permissions');
        }

        // DEPARTMENT MANAGER - Limited to their department
        $deptManagerRole = Role::where('name', 'department_manager')
            ->where('tenant_id', $company->id)
            ->first();

        if ($deptManagerRole) {
            $deptPerms = Permission::whereIn('name', [
                'view_dashboard',
                'view_employees',
                'view_employee_details',
                'view_jobs',
                'view_job_details',
                'view_attendance',
                'view_attendance_reports',
                'view_leaves',
                'approve_leave',
                'view_leave_reports',
                'view_reports',
            ])->where('tenant_id', $company->id)->get();

            $deptManagerRole->syncPermissions($deptPerms);
            $this->command->info('✅ Department Manager: ' . $deptPerms->count() . ' permissions');
        }

        // EMPLOYEE - Self-service only
        $employeeRole = Role::where('name', 'employee')
            ->where('tenant_id', $company->id)
            ->first();

        if ($employeeRole) {
            $employeePerms = Permission::whereIn('name', [
                'view_employee_details', // Only their own
                'view_jobs',
                'view_job_details',
                'view_attendance',
                'view_leaves',
                'create_leave',
            ])->where('tenant_id', $company->id)->get();

            $employeeRole->syncPermissions($employeePerms);
            $this->command->info('✅ Employee: ' . $employeePerms->count() . ' permissions');
        }

        // ============================================
        // 3. ASSIGN PERMISSIONS DIRECTLY TO USERS (Optional)
        // ============================================

        // Get Super Admin user and ensure they have all permissions
        $superAdminUser = User::where('email', 'superadmin@techlab33.com')
            ->where('tenant_id', $company->id)
            ->first();

        if ($superAdminUser) {
            $superAdminUser->syncPermissions($allPermissions);
            $this->command->info('✅ Super Admin User: ' . $superAdminUser->getAllPermissions()->count() . ' permissions');
        }

        // Get Admin user
        $adminUser = User::where('email', 'admin@techlab33.com')
            ->where('tenant_id', $company->id)
            ->first();

        if ($adminUser) {
            $adminUser->syncPermissions([]); // Clear direct permissions, use role permissions
            $this->command->info('✅ Admin User: Using role permissions');
        }

        // Get HR Manager user
        $hrUser = User::where('email', 'hr@techlab33.com')
            ->where('tenant_id', $company->id)
            ->first();

        if ($hrUser) {
            $hrUser->syncPermissions([]); // Clear direct permissions, use role permissions
            $this->command->info('✅ HR User: Using role permissions');
        }

        // Get Employee user
        $employeeUser = User::where('email', 'employee@techlab33.com')
            ->where('tenant_id', $company->id)
            ->first();

        if ($employeeUser) {
            $employeeUser->syncPermissions([]); // Clear direct permissions, use role permissions
            $this->command->info('✅ Employee User: Using role permissions');
        }

        $this->command->newLine();
        $this->command->info('✅ All permissions assigned successfully!');

        // Display summary
        $this->displaySummary($company->id);
    }

    private function displaySummary($tenantId): void
    {
        $this->command->newLine();
        $this->command->info('📊 Permission Summary:');

        $roles = Role::where('tenant_id', $tenantId)->get();

        foreach ($roles as $role) {
            $count = $role->permissions()->count();
            $this->command->line("   {$role->name}: {$count} permissions");
        }

        $this->command->newLine();
        $this->command->info('🔑 Test Login Credentials:');
        $this->command->line('   Super Admin: superadmin@techlab33.com / password123');
        $this->command->line('   Admin: admin@techlab33.com / password123');
        $this->command->line('   HR Manager: hr@techlab33.com / password123');
        $this->command->line('   Department Manager: manager@techlab33.com / password123');
        $this->command->line('   Employee: employee@techlab33.com / password123');
    }
}
