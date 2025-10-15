<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Models\Setting as ModelsSetting;
use Modules\Settings\Models\Setting; // Adjust namespace if different

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Organization
            [
                'key' => 'company_name',
                'value' => 'Tech Lab 33',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The name of the company',
            ],
            [
                'key' => 'phone',
                'value' => '123-456-7890',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The phone number of the company',
            ],
            [
                'key' => 'Email',
                'value' => 'info@acmecorp.com',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The email address of the company',
            ],
            [
                'key' => 'address_street_1',
                'value' => '123 Main St',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The primary address of the company',
            ],
            [
                'key' => 'address_street_2',
                'value' => 'Apt 4B',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The secondary address of the company',
            ],
            [
                'key' => 'city',
                'value' => 'Metropolis',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The city where the company is located',
            ],
            [
                'key' => 'zip_postal_code',
                'value' => '12345',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The zip or postal code of the company',
            ],
            [
                'key' => 'country',
                'value' => 'Bangladesh',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'The country where the company is located',
            ],
            [
                'key' => 'notes',
                'value' => 'HR management system for Tech Lab 33',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'Additional notes about the company',
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC+6',
                'type' => 'string',
                'group' => 'organization',
                'description' => 'Default timezone for the company',
            ],

            // Users & Access
            [
                'key' => 'default_role',
                'value' => 'Employee',
                'type' => 'string',
                'group' => 'users',
                'description' => 'Default role assigned to new users',
            ],

            // Leave Management
            [
                'key' => 'max_annual_leave',
                'value' => '25',
                'type' => 'number',
                'group' => 'leave',
                'description' => 'Maximum annual leave days',
            ],

            // Payroll (example)
            [
                'key' => 'salary_currency',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'payroll',
                'description' => 'Default currency for salaries',
            ],

            // Notifications
            [
                'key' => 'send_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications for system events',
            ],

            // System
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default date format for the system',
            ],
        ];

        foreach ($settings as $setting) {
            ModelsSetting::updateOrCreate(
                ['key' => $setting['key']], // ensures no duplicates
                $setting
            );
        }
    }
}
