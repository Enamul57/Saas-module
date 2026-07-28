<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Use only columns that exist in your companies table
        $company = Company::firstOrCreate(
            ['domain' => 'central.test'],
            [
                'company_name' => 'Tech lab33',
                'domain' => 'central.test',
                'email' => 'info@techlab33.com',
                'password' => bcrypt('password123'), // Add password if needed
                'slug' => 'tech-lab33',
            ]
        );

        $this->command->info('✅ Company seeded successfully!');
        $this->command->info('   ID: ' . $company->id);
        $this->command->info('   Name: ' . $company->company_name);
        $this->command->info('   Domain: ' . $company->domain);
    }
}
