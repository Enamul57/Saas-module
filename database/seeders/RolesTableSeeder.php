<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\TenantRole as Role;
use App\Models\TenantPermission as Permission;
use Illuminate\Support\Str;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Get or create company
        $company = Company::firstOrCreate(
            ['domain' => 'central.test'],
            [
                'company_name' => 'Tech lab33',
                'domain' => 'central.test',
            ]
        );

        // ✅ CLEANUP: Fix any existing role name case issues
        Role::where('name', 'Super_admin')->update(['name' => 'super_admin']);
        Role::where('name', 'Super Admin')->update(['name' => 'super_admin']);
        Role::where('name', 'SuperAdmin')->update(['name' => 'super_admin']);

        // ✅ FIX: Ensure all permissions have correct slugs before assigning
        $this->fixPermissionSlugs($company->id);

        // Define role permission mappings
        $rolePermissions = [
            'super_admin' => [
                'all' => true,
            ],
            'admin' => [
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
                'assign_permissions_to_roles',
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
                'manage_job_categories',
                'manage_job_titles',
                'view_attendance',
                'view_attendance_reports',
                'view_leaves',
                'approve_leave',
                'view_leave_reports',
                'view_payroll',
                'create_payroll',
                'edit_payroll',
                'process_payroll',
                'view_salary_reports',
                'view_projects',
                'create_projects',
                'edit_projects',
                'delete_projects',
                'assign_projects',
                'view_tasks',
                'create_tasks',
                'edit_tasks',
                'delete_tasks',
                'assign_tasks',
                'view_reports',
                'generate_reports',
                'export_reports',
                'view_settings',
                'manage_settings',
                'view_trainings',
                'create_trainings',
                'edit_trainings',
                'delete_trainings',
                'view_recruitments',
                'create_recruitments',
                'edit_recruitments',
                'delete_recruitments',
                'view_performance',
                'create_performance_reviews',
                'edit_performance_reviews',
                'view_assets',
                'create_assets',
                'edit_assets',
                'delete_assets',
                'assign_assets',
                'view_tickets',
                'create_tickets',
                'edit_tickets',
                'delete_tickets',
                'resolve_tickets',
                'view_audit_logs',
                'view_activity_logs',
                'manage_api_keys',
                'view_api_logs',
            ],
            'hr_manager' => [
                'view_dashboard',
                'view_analytics',
                'view_users',
                'create_users',
                'edit_users',
                'view_employees',
                'create_employee',
                'edit_employee',
                'delete_employee',
                'view_employee_details',
                'export_employees',
                'import_employees',
                'manage_employee_documents',
                'view_jobs',
                'create_job',
                'edit_job',
                'delete_job',
                'view_job_details',
                'manage_job_categories',
                'manage_job_titles',
                'manage_job_units',
                'view_attendance',
                'create_attendance',
                'edit_attendance',
                'view_attendance_reports',
                'view_leaves',
                'create_leave',
                'edit_leave',
                'delete_leave',
                'approve_leave',
                'view_leave_reports',
                'view_payroll',
                'view_salary_reports',
                'view_employee_salary',
                'view_trainings',
                'create_trainings',
                'edit_trainings',
                'delete_trainings',
                'assign_trainings',
                'view_recruitments',
                'create_recruitments',
                'edit_recruitments',
                'delete_recruitments',
                'manage_job_postings',
                'manage_applicants',
                'schedule_interviews',
                'view_performance',
                'create_performance_reviews',
                'edit_performance_reviews',
                'delete_performance_reviews',
                'manage_goals',
                'manage_kpis',
                'view_reports',
                'generate_reports',
                'export_reports',
            ],
            'department_manager' => [
                'view_dashboard',
                'view_users', // ✅ Make sure this is included
                'view_employees',
                'view_employee_details',
                'view_jobs',
                'view_job_details',
                'view_attendance',
                'view_attendance_reports',
                'view_leaves',
                'approve_leave',
                'view_leave_reports',
                'view_payroll',
                'view_employee_salary',
                'view_projects',
                'create_projects',
                'edit_projects',
                'assign_projects',
                'view_tasks',
                'create_tasks',
                'edit_tasks',
                'assign_tasks',
                'view_trainings',
                'assign_trainings',
                'view_performance',
                'create_performance_reviews',
                'edit_performance_reviews',
                'manage_goals',
                'view_reports',
                'generate_reports',
                'view_assets',
                'assign_assets',
            ],
            'manager' => [
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
                'view_projects',
                'create_projects',
                'edit_projects',
                'assign_projects',
                'view_tasks',
                'create_tasks',
                'edit_tasks',
                'assign_tasks',
                'view_trainings',
                'assign_trainings',
                'view_performance',
                'create_performance_reviews',
                'edit_performance_reviews',
                'view_reports',
                'generate_reports',
            ],
            'employee' => [
                'view_employee_details',
                'view_jobs',
                'view_job_details',
                'create_attendance',
                'view_attendance',
                'create_leave',
                'view_leaves',
                'view_employee_salary',
                'view_tasks',
                'edit_tasks',
                'view_trainings',
                'view_performance',
                'create_tickets',
                'view_tickets',
            ],
            'finance_manager' => [
                'view_dashboard',
                'view_analytics',
                'view_employees',
                'view_employee_details',
                'view_payroll',
                'create_payroll',
                'edit_payroll',
                'delete_payroll',
                'process_payroll',
                'view_salary_reports',
                'view_employee_salary',
                'view_reports',
                'generate_reports',
                'export_reports',
                'view_settings',
                'view_audit_logs',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::updateOrCreate(
                [
                    'name' => $roleName,
                    'tenant_id' => $company->id,
                    'guard_name' => 'web',
                ],
                []
            );

            if ($roleName === 'super_admin') {
                $allPermissions = Permission::where('tenant_id', $company->id)
                    ->where('guard_name', 'web')
                    ->get();
                $role->syncPermissions($allPermissions);
                $this->command->info("Super Admin role created with " . $allPermissions->count() . " permissions");
            } else {
                // ✅ FIX: Get ONLY ONE permission per name (the one with correct slug)
                $permissionIds = [];
                foreach ($permissions as $permName) {
                    $permission = Permission::where('name', $permName)
                        ->where('tenant_id', $company->id)
                        ->where('guard_name', 'web')
                        ->orderBy('id') // Get the first one (oldest)
                        ->first();

                    if ($permission) {
                        $permissionIds[] = $permission->id;
                    } else {
                        // If permission doesn't exist, create it
                        $permission = Permission::create([
                            'name' => $permName,
                            'slug' => $permName,
                            'guard_name' => 'web',
                            'tenant_id' => $company->id,
                        ]);
                        $permissionIds[] = $permission->id;
                        $this->command->warn("⚠️ Created missing permission: {$permName}");
                    }
                }

                // ✅ FIX: Use sync without detaching to preserve existing permissions
                // This ensures we don't remove permissions that should stay
                $role->permissions()->sync($permissionIds);
                $this->command->info("Role '{$roleName}' synced with " . count($permissionIds) . " permissions");
            }
        }

        $this->command->info('All roles and permissions created successfully!');
    }

    /**
     * ✅ FIX: Ensure all permissions have correct slugs
     */
    private function fixPermissionSlugs($tenantId): void
    {
        $permissions = Permission::where('tenant_id', $tenantId)->get();

        foreach ($permissions as $permission) {
            // If slug doesn't match name, update it
            if ($permission->slug !== $permission->name) {
                $permission->slug = $permission->name;
                $permission->save();
                $this->command->line("🔄 Fixed slug for: {$permission->name} (was: {$permission->slug})");
            }
        }
    }
}