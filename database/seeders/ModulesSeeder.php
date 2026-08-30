<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature as Modules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        try {
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

            // Define all modules/features
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

            // Prepare data for upsert
            $data = collect($modules)->map(fn($module) => [
                'name' => $module['name'],
                'slug' => Str::slug(strtolower($module['name'])),
                'tenant_id' => $company->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            // Use upsert to avoid duplicates
            $inserted = Modules::upsert(
                $data,
                ['slug', 'tenant_id'], // Unique constraints
                ['name', 'updated_at'] // Columns to update if exists
            );

            $this->command->info('✅ ' . count($data) . ' modules seeded successfully!');

            // Display all modules
            $this->command->newLine();
            $this->command->info('📋 Available Modules:');
            $allModules = Modules::where('tenant_id', $company->id)
                ->orderBy('name')
                ->get();

            if ($allModules->isEmpty()) {
                $this->command->warn('⚠️ No modules found!');
            } else {
                foreach ($allModules as $module) {
                    $this->command->line("   - {$module->name} (Slug: {$module->slug}, ID: {$module->id})");
                }
            }

            // Optional: Assign features to specific roles
            $this->assignFeaturesToRoles($company->id);

            // Show summary
            $this->displaySummary($company->id);
        } catch (\Exception $e) {
            $this->command->error('❌ Error seeding modules: ' . $e->getMessage());
            Log::error('ModulesSeeder error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function assignFeaturesToRoles($tenantId): void
    {
        $this->command->newLine();
        $this->command->info('🔗 Assigning features to roles...');

        try {
            // Get all features
            $features = Modules::where('tenant_id', $tenantId)->get();

            if ($features->isEmpty()) {
                $this->command->warn('⚠️ No features found to assign!');
                return;
            }

            // Define feature assignments for each role
            $roleFeatures = [
                'super_admin' => [
                    'all' => true, // All features
                ],
                'admin' => [
                    'all' => true, // All features
                ],
                'hr_manager' => [
                    'User Management',
                    'Employee Management',
                    'Attendance Management',
                    'Leave Management',
                    'Payroll Management',
                    'Report Management',
                    'Dashboard Management',
                    'Settings'
                ],
                'department_manager' => [
                    'Employee Management',
                    'Attendance Management',
                    'Leave Management',
                    'Project Management',
                    'Task Management',
                    'Report Management',
                    'Dashboard Management'
                ],
                'manager' => [
                    'Employee Management',
                    'Attendance Management',
                    'Leave Management',
                    'Project Management',
                    'Task Management',
                    'Report Management',
                    'Dashboard Management'
                ],
                'employee' => [
                    'Employee Management',
                    'Attendance Management',
                    'Leave Management',
                    'Task Management'
                ],
                'finance_manager' => [
                    'Payroll Management',
                    'Report Management',
                    'Dashboard Management',
                    'Settings'
                ],
            ];

            $assignedCount = 0;

            foreach ($roleFeatures as $roleName => $featureNames) {
                $role = \App\Models\TenantRole::where('name', $roleName)
                    ->where('tenant_id', $tenantId)
                    ->first();

                if (!$role) {
                    $this->command->line("   ⚠️ Role '{$roleName}' not found - skipping");
                    continue;
                }

                // Determine which features to assign
                if ($featureNames === ['all'] || $featureNames['all'] ?? false) {
                    // Assign ALL features
                    $featureIds = $features->pluck('id')->toArray();
                } else {
                    // Assign specific features
                    $featureIds = $features->whereIn('name', $featureNames)->pluck('id')->toArray();
                }

                if (empty($featureIds)) {
                    $this->command->line("   ⚠️ No features found for role '{$roleName}'");
                    continue;
                }

                // Sync features to role
                $role->features()->sync($featureIds);
                $assignedCount++;
                $this->command->line("   ✅ {$roleName}: " . count($featureIds) . " features assigned");
            }

            $this->command->info("✅ Features assigned to {$assignedCount} roles successfully!");
        } catch (\Exception $e) {
            $this->command->error('❌ Error assigning features to roles: ' . $e->getMessage());
            Log::error('assignFeaturesToRoles error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function displaySummary($tenantId): void
    {
        $this->command->newLine();
        $this->command->info('📊 Summary:');

        // Count features by role
        $roles = \App\Models\TenantRole::where('tenant_id', $tenantId)->get();

        if ($roles->isEmpty()) {
            $this->command->line('   No roles found');
            return;
        }

        $this->command->line('   Features per role:');
        foreach ($roles as $role) {
            $count = $role->features()->count();
            $this->command->line("      - {$role->name}: {$count} features");
        }

        // Total features
        $totalFeatures = Modules::where('tenant_id', $tenantId)->count();
        $this->command->line("   Total features: {$totalFeatures}");
    }
}