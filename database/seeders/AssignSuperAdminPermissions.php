<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\TenantRole as Role;
use App\Models\TenantPermission as Permission;

class AssignSuperAdminPermissions extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Assigning all permissions to Super Admin...');

        // Get all permissions
        $allPermissions = Permission::all();

        if ($allPermissions->count() === 0) {
            $this->command->error('❌ No permissions found! Run ModulesSeeder first.');
            return;
        }

        $this->command->info('📋 Found ' . $allPermissions->count() . ' permissions');

        // Method 1: Assign to Super Admin Role
        $role = Role::where('name', 'super_admin')->first();
        if ($role) {
            $role->syncPermissions($allPermissions);
            $this->command->info('✅ Super Admin role: ' . $role->permissions()->count() . ' permissions');
        } else {
            $this->command->warn('⚠️  Super Admin role not found, creating...');
            $role = Role::create([
                'name' => 'super_admin',
                'guard_name' => 'web',
                'tenant_id' => 1,
            ]);
            $role->syncPermissions($allPermissions);
            $this->command->info('✅ Super Admin role created with ' . $role->permissions()->count() . ' permissions');
        }

        // Method 2: Assign directly to Super Admin user
        $user = User::where('email', 'superadmin@techlab33.com')->first();
        if ($user) {
            // Clear direct permissions and use role permissions
            $user->syncPermissions([]);
            $user->assignRole('super_admin');
            $this->command->info('✅ Super Admin user: ' . $user->getAllPermissions()->count() . ' permissions (from role)');
        } else {
            $this->command->error('❌ Super Admin user not found!');
        }

        // Display summary
        $this->command->newLine();
        $this->command->info('✅ All permissions assigned to Super Admin!');

        // Show some test permissions
        $testPermissions = ['view_employees', 'create_employee', 'delete_employee', 'view_dashboard'];
        $this->command->newLine();
        $this->command->info('🎯 Testing permissions:');
        foreach ($testPermissions as $perm) {
            $can = $user ? $user->can($perm) : false;
            $status = $can ? '✅' : '❌';
            $this->command->line("   {$status} {$perm}");
        }
    }
}
