<?php

namespace Modules\PIM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PIM\App\Models\EmployeeStatus;

class EmployeeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current tenant ID
        $tenantId = app('tenant')->id ?? 1;

        $statuses = [
            ['employee_status' => 'Active', 'tenant_id' => $tenantId],
            ['employee_status' => 'Intern', 'tenant_id' => $tenantId],
            ['employee_status' => 'Probation', 'tenant_id' => $tenantId],
            ['employee_status' => 'Part Time', 'tenant_id' => $tenantId],
            ['employee_status' => 'Resigned', 'tenant_id' => $tenantId],
            ['employee_status' => 'Terminated', 'tenant_id' => $tenantId],
            ['employee_status' => 'Retired', 'tenant_id' => $tenantId],
        ];

        foreach ($statuses as $status) {
            EmployeeStatus::firstOrCreate(
                ['employee_status' => $status['employee_status']],
                $status
            );
        }

        $this->command->info('✅ Employee statuses seeded successfully!');
    }
}
