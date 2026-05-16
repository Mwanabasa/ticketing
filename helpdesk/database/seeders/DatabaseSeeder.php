<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(CategorySeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'admin@helpdesk.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        $staffAccounts = [
            ['name' => 'IT Support', 'email' => 'it-support@helpdesk.test'],
            ['name' => 'Facilities Maintenance', 'email' => 'facilities@helpdesk.test'],
            ['name' => 'Grades & Records', 'email' => 'grades-records@helpdesk.test'],
            ['name' => 'Attendance', 'email' => 'attendance@helpdesk.test'],
            ['name' => 'Transportation', 'email' => 'transportation@helpdesk.test'],
            ['name' => 'Health Services', 'email' => 'health-services@helpdesk.test'],
            ['name' => 'Financial/Billing', 'email' => 'financial-billing@helpdesk.test'],
            ['name' => 'Security & Safety', 'email' => 'security-safety@helpdesk.test'],
            ['name' => 'General Inquiry', 'email' => 'general-inquiry@helpdesk.test'],
        ];

        foreach ($staffAccounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Staff,
                ]
            );
        }

        User::factory()->create([
            'name' => 'Demo Student',
            'email' => 'student@helpdesk.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
        ]);
    }
}
