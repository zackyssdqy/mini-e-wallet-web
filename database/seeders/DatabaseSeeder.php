<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->delete();

        $users = [
            ['name' => 'User A', 'email' => 'usera@example.com'],
            ['name' => 'User B', 'email' => 'userb@example.com'],
            ['name' => 'User C', 'email' => 'userc@example.com'],
        ];

        foreach ($users as $user) {
            User::factory()
                ->withWalletBalance(100000)
                ->create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                ]);
        }
    }
}
