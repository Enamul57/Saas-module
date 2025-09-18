<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature as Modules;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
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

        Modules::insert($modules);
    }
}
