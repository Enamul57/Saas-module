<?php

namespace Modules\PIM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PIM\App\Models\JobCategory;
use Modules\PIM\App\Models\JobTitle;
use Modules\PIM\App\Models\JobUnit;

class JobHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $data = [
            'Management' => [
                'Executive Office' => ['Chief Executive Officer (CEO)', 'Chief Operating Officer (COO)', 'General Manager', 'Operations Manager'],
            ],
            'Administration' => [
                'Administration Department' => ['Office Administrator', 'Administrative Assistant', 'Executive Assistant'],
            ],
            'Human Resources' => [
                'Human Resources Department' => ['HR Manager', 'HR Officer', 'Recruitment Specialist', 'HR Assistant'],
            ],
            'Finance' => [
                'Finance Department' => ['Finance Manager', 'Financial Analyst', 'Payroll Officer'],
            ],
            'Accountant' => [
                'Accounting Department' => ['Accountant', 'Junior Accountant', 'Accounts Payable Clerk', 'Accounts Receivable Clerk'],
            ],
            'IT & Software Development' => [
                'Information Technology Department' => ['Software Engineer', 'Frontend Developer', 'Backend Developer', 'Full Stack Developer', 'System Administrator', 'DevOps Engineer', 'IT Support Specialist'],
            ],
            'Sales & Marketing' => [
                'Sales Department' => ['Sales Executive', 'Sales Manager', 'Business Development Officer'],
                'Marketing Department' => ['Marketing Executive', 'Brand Manager'],
            ],
            'Customer Support' => [
                'Customer Service Department' => ['Customer Support Representative', 'Customer Success Manager', 'Call Center Agent'],
            ],
            'Operations' => [
                'Operations Department' => ['Operations Coordinator', 'Operations Supervisor', 'Logistics Officer'],
            ],
            'Engineering' => [
                'Engineering Department' => ['Mechanical Engineer', 'Electrical Engineer', 'Civil Engineer', 'Quality Assurance Engineer'],
            ],
            'Digital Marketer' => [
                'Digital Marketing Department' => ['Digital Marketing Specialist', 'SEO Specialist', 'Social Media Manager', 'Content Strategist'],
            ],
            'Other' => [
                'Procurement Department' => ['Procurement Officer'],
                'Quality Assurance Department' => ['Quality Assurance Specialist'],
                'Maintenance Unit' => ['Maintenance Technician'],
            ],
        ];

        foreach ($data as $categoryName => $units) {
            $category = JobCategory::firstOrCreate(['job_category_name' => $categoryName]);

            foreach ($units as $unitName => $titles) {
                $unit = JobUnit::firstOrCreate([
                    'job_unit_name' => $unitName,
                    'job_category_id' => $category->id,
                ]);

                foreach ($titles as $titleName) {
                    JobTitle::firstOrCreate([
                        'job_title_name' => $titleName,
                        'job_unit_id' => $unit->id,
                    ]);
                }
            }
        }
    }
}
