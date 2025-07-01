<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'vetescoton@usep.edu.ph'],
            [
            'name' => 'Vincent Escoton',
            'password' => bcrypt('adminpassword'),
            'role' => 'admin',
            ]
        );
    }
}
