<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Engineering' => [
                'Software Engineer',
                'DevOps Engineer',
                'QA Engineer',
                'Mobile App Developer',
                'Data Engineer',
                'Engineering Manager',
                'Technical Architect',
            ],
            'Product' => [
                'Product Manager',
                'Product Owner',
                'UX/UI Designer',
                'Business Analyst',
                'Scrum Master',
            ],
            'Design' => [
                'UX Designer',
                'UI Designer',
                'Graphic Designer',
                'Motion Designer',
                'Design Lead',
            ],
            'IT & Infrastructure' => [
                'System Administrator',
                'IT Support Specialist',
                'Network Engineer',
                'Security Analyst',
                'Cloud Engineer',
            ],
            'Data & AI' => [
                'Data Scientist',
                'Machine Learning Engineer',
                'Data Analyst',
                'AI Researcher',
            ],
            'Sales & Marketing' => [
                'Sales Executive',
                'Digital Marketing Specialist',
                'SEO Specialist',
                'Marketing Manager',
                'Content Strategist',
                'Social Media Manager',
            ],
            'Customer Success & Support' => [
                'Customer Support Specialist',
                'Technical Support Engineer',
                'Customer Success Manager',
                'Onboarding Specialist',
            ],
            'Human Resources' => [
                'HR Manager',
                'Talent Acquisition Specialist',
                'People Operations Specialist',
                'L&D Coordinator',
            ],
            'Finance & Legal' => [
                'Financial Analyst',
                'Accountant',
                'Legal Counsel',
                'Compliance Officer',
            ],
            'Executive & Strategy' => [
                'Chief Executive Officer',
                'Chief Technology Officer',
                'Chief Product Officer',
                'Chief Financial Officer',
                'Strategy Analyst',
            ],
        ];

        foreach ($departments as $deptName => $positions) {
            $department = Department::create([
                'name' => $deptName,
                'description' => "$deptName Department",
            ]);

            foreach ($positions as $position) {
                Position::create([
                    'name' => $position,
                    'description' => "$position in the $deptName department",
                    'department_id' => $department->id,
                ]);
            }
        }
    }
}
