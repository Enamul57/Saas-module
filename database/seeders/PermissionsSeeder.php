<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\TenantPermission as Permission;
use Illuminate\Support\Str;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('domain', 'central.test')->first();

        if (!$company) {
            $this->command->error('❌ Company not found!');
            return;
        }

        $this->command->info('🔐 Creating all permissions...');

        // All permissions in seeder format
        $permissions = [
            // Dashboard
            'view_dashboard',
            'view_analytics',

            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',

            // Role & Permission Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_permissions_to_roles',

            // Employee Management (PIM)
            'view_employees',
            'create_employee',
            'edit_employee',
            'delete_employee',
            'view_employee_details',
            'export_employees',
            'import_employees',
            'manage_employee_documents',

            // Job Management
            'view_jobs',
            'create_job',
            'edit_job',
            'delete_job',
            'view_job_details',
            'manage_job_categories',
            'manage_job_titles',
            'manage_job_units',

            // Attendance
            'view_attendance',
            'create_attendance',
            'edit_attendance',
            'delete_attendance',
            'view_attendance_reports',

            // Leave
            'view_leaves',
            'create_leave',
            'edit_leave',
            'delete_leave',
            'approve_leave',
            'view_leave_reports',

            // Payroll
            'view_payroll',
            'create_payroll',
            'edit_payroll',
            'delete_payroll',
            'process_payroll',
            'view_salary_reports',
            'view_employee_salary',

            // Reports
            'view_reports',
            'create_reports',
            'generate_reports',
            'export_reports',
            'schedule_reports',

            // Settings
            'view_settings',
            'manage_settings',
            'manage_company_settings',
            'manage_email_settings',
            'manage_security_settings',

            // Projects
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'assign_projects',
            'view_project_reports',

            // Tasks
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',
            'view_task_reports',

            // Floor Management
            'view_floors',
            'create_floors',
            'edit_floors',
            'delete_floors',

            // Trainings
            'view_trainings',
            'create_trainings',
            'edit_trainings',
            'delete_trainings',
            'assign_trainings',
            'view_training_reports',

            // Recruitment
            'view_recruitments',
            'create_recruitments',
            'edit_recruitments',
            'delete_recruitments',
            'manage_job_postings',
            'manage_applicants',
            'schedule_interviews',

            // Performance
            'view_performance',
            'create_performance_reviews',
            'edit_performance_reviews',
            'delete_performance_reviews',
            'manage_goals',
            'manage_kpis',
            'view_performance_reports',

            // Assets
            'view_assets',
            'create_assets',
            'edit_assets',
            'delete_assets',
            'assign_assets',
            'view_asset_reports',

            // Tickets
            'view_tickets',
            'create_tickets',
            'edit_tickets',
            'delete_tickets',
            'assign_tickets',
            'resolve_tickets',
            'view_ticket_reports',

            // Audit
            'view_audit_logs',
            'view_activity_logs',
            'export_audit_logs',
            'manage_audit_settings',

            // API
            'manage_api_keys',
            'view_api_logs',
            'manage_api_settings',
        ];

        $created = 0;
        foreach ($permissions as $permName) {
            Permission::updateOrCreate(
                [
                    'name' => $permName,
                    'guard_name' => 'web',
                    'tenant_id' => $company->id,
                ],
                [
                    'slug' => $permName,
                ]
            );
            $created++;
        }

        $this->command->info('✅ ' . $created . ' permissions created successfully!');
    }
}
