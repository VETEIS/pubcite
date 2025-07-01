<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*User::updateOrCreate(
            ['email' => 'nbmendoza00217@usep.edu.ph'],
            [
            'name' => 'Norlan Mendoza',
            'password' => bcrypt('userpassword'),
            'role' => 'user',
            ]
        );*/

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
