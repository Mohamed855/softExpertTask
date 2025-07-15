<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('role', 'manager')->first()) {
            User::create([
                'name' => 'Manager',
                'email' => 'manager@test.com',
                'password' => bcrypt('manager'),
                'role' => 'manager',
            ]);
        }

        if (! User::where('role', 'user')->first()) {
            User::create([
                'name' => 'User',
                'email' => 'user@test.com',
                'password' => bcrypt('user'),
                'role' => 'user',
            ]);
        }
    }
}
