<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckPermissions extends Command
{
    protected $signature = 'permissions:check {email?}';
    protected $description = 'Check user permissions';

    public function handle()
    {
        $email = $this->argument('email') ?? 'superadmin@techlab33.com';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User not found: {$email}");
            return 1;
        }

        $this->info("👤 User: {$user->name} ({$user->email})");
        $this->newLine();

        // Check roles
        $roles = $user->getRoleNames();
        $this->info("📋 Roles: " . ($roles->count() ? $roles->implode(', ') : 'None'));
        $this->newLine();

        // Check permissions
        $permissions = $user->getAllPermissions();
        $this->info("🔐 Total Permissions: " . $permissions->count());

        if ($permissions->count() > 0) {
            $this->newLine();
            $this->info("📝 Permissions List:");

            // Group permissions by module
            $groups = [];
            foreach ($permissions as $perm) {
                $parts = explode('_', $perm->name);
                $module = count($parts) > 1 ? $parts[0] : 'other';
                if (!isset($groups[$module])) {
                    $groups[$module] = [];
                }
                $groups[$module][] = $perm->name;
            }

            foreach ($groups as $module => $perms) {
                $this->line("   📁 {$module}: " . count($perms));
                foreach ($perms as $p) {
                    $this->line("      - {$p}");
                }
            }
        } else {
            $this->warn("⚠️  No permissions assigned!");
        }

        $this->newLine();

        // Check specific permissions
        $testPermissions = [
            'view_employees',
            'create_employee',
            'edit_employee',
            'delete_employee',
            'view_employee_details',
            'view_dashboard',
        ];

        $this->info("🎯 Specific Permission Checks:");
        foreach ($testPermissions as $perm) {
            $can = $user->can($perm);
            $status = $can ? '✅' : '❌';
            $this->line("   {$status} {$perm}");
        }

        return 0;
    }
}