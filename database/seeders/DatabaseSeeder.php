<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'user@usep.edu.ph'],
            [
                'name' => 'Test User',
                'password' => bcrypt('userpassword'),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@usep.edu.ph'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('adminpassword'),
                'role' => 'admin',
            ]
        );
    }
}
