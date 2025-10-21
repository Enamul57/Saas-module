<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\App\Models\Setting;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $company = Company::firstOrFail();
        $settings = [
            // Organization
            [
                'key' => 'company_name',
                'value' => 'Tech Lab 33',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The name of the company',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'phone',
                'value' => '123-456-7890',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The phone number of the company',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'Email',
                'value' => 'info@acmecorp.com',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The email address of the company',
                'tenant_id' => $company->id,

            ],
            [
                'key' => 'address_street_1',
                'value' => '123 Main St',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The primary address of the company',
                'tenant_id' => $company->id,

            ],
            [
                'key' => 'address_street_2',
                'value' => 'Apt 4B',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The secondary address of the company',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'city',
                'value' => 'Metropolis',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The city where the company is located',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'zip_postal_code',
                'value' => '12345',
                'type' => 'string',
                'tenant_id' => $company->id,
                'group' => 'organization',
                'description' => 'The zip or postal code of the company',
            ],
            [
                'key' => 'country',
                'value' => 'Bangladesh',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The country where the company is located',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'notes',
                'value' => 'HR management system for Tech Lab 33',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'Additional notes about the company',
                'tenant_id' => $company->id,
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC+6',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'Default timezone for the company',
                'tenant_id' => $company->id,
            ],

            // Users & Access
            [
                'key' => 'default_role',
                'value' => 'Employee',
                'type' => 'string',
                'group' => 'users',
                'description' => 'Default role assigned to new users',
                'tenant_id' => $company->id,
            ],

            // Leave Management
            [
                'key' => 'max_annual_leave',
                'value' => '25',
                'type' => 'number',
                'group' => 'leave',
                'description' => 'Maximum annual leave days',
                'tenant_id' => $company->id,
            ],

            // Payroll (example)
            [
                'key' => 'salary_currency',
                'value' => 'BDT',
                'type' => 'string',
                'group' => 'payroll',
                'description' => 'Default currency for salaries',
                'tenant_id' => $company->id,
            ],

            // Notifications
            [
                'key' => 'send_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications for system events',
                'tenant_id' => $company->id,
            ],

            // System
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default date format for the system',
                'tenant_id' => $company->id,
            ],
        ];

        foreach ($settings as $setting) {
            Log::info('Seeding setting: ' . $setting['key']);
            Setting::updateOrCreate(
                ['key' => $setting['key']], // ensures no duplicates
                $setting
            );
        }
    }
}
