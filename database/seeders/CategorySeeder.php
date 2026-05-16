<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'WiFi problem', 'slug' => 'wifi-problem'],
            ['name' => 'Portal issue', 'slug' => 'portal-issue'],
            ['name' => 'Password reset', 'slug' => 'password-reset'],
            ['name' => 'Computer lab issue', 'slug' => 'computer-lab-issue'],
        ];

        foreach ($rows as $row) {
            Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name']]
            );
        }
    }
}
