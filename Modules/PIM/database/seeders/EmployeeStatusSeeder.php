<?php

namespace Modules\PIM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PIM\Models\EmployeeStatus;

class EmployeeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $statuses = [
            ['employee_status' => 'Active'],
            ['employee_status' => 'Intern'],
            ['employee_status' => 'Probation'],
            ['employee_status' => 'Part Time'],
            ['employee_status' => 'Resigned'],
            ['employee_status' => 'Terminated'],
            ['employee_status' => 'Retired'],
        ];
        foreach ($statuses as $status) {
            EmployeeStatus::firstOrCreate($status);
        }
    }
}
