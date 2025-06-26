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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@usep.edu.ph',
            'password' => bcrypt('userpassword'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@usep.edu.ph',
            'password' => bcrypt('adminpassword'),
            'role' => 'admin',
        ]);
    }
}
