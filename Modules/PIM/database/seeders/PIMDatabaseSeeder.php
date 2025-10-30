<?php

namespace Modules\PIM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PIM\Models\EmployeeStatus;

class PIMDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EmployeeStatusSeeder::class,
            JobHierarchySeeder::class,
        ]);
    }
}
