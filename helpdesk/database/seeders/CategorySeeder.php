<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'IT Support', 'slug' => 'it-support'],
            ['name' => 'Facilities Maintenance', 'slug' => 'facilities-maintenance'],
            ['name' => 'Grades & Records', 'slug' => 'grades-records'],
            ['name' => 'Attendance', 'slug' => 'attendance'],
            ['name' => 'Transportation', 'slug' => 'transportation'],
            ['name' => 'Health Services', 'slug' => 'health-services'],
            ['name' => 'Financial/Billing', 'slug' => 'financial-billing'],
            ['name' => 'Security & Safety', 'slug' => 'security-safety'],
            ['name' => 'General Inquiry', 'slug' => 'general-inquiry'],
        ];

        foreach ($rows as $row) {
            Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name']]
            );
        }
    }
}
