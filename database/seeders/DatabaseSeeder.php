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

        $staffMembers = [
            ['name' => 'IT Support',       'email' => 'support@helpdesk.test'],
            ['name' => 'Alice Mensah',     'email' => 'alice@helpdesk.test'],
            ['name' => 'Bob Asante',       'email' => 'bob@helpdesk.test'],
            ['name' => 'Clara Owusu',      'email' => 'clara@helpdesk.test'],
        ];

        foreach ($staffMembers as $staff) {
            User::query()->updateOrCreate(
                ['email' => $staff['email']],
                [
                    'name'     => $staff['name'],
                    'password' => Hash::make('password'),
                    'role'     => UserRole::Staff,
                ]
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'student@helpdesk.test'],
            [
                'name'     => 'Demo Student',
                'password' => Hash::make('password'),
                'role'     => UserRole::Student,
            ]
        );
    }
}
