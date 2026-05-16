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
            ['email' => 'support@helpdesk.test'],
            [
                'name' => 'IT Support',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
            ]
        );

        User::factory()->create([
            'name' => 'Demo Student',
            'email' => 'student@helpdesk.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Student,
        ]);
    }
}
