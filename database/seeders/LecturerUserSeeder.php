<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LecturerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
        ['email' => 'jamila@gmail.com'],
        [
            'name' => 'Jamila',
            'password' => bcrypt('Jamila@1234'),
            'role' => 'lecturer',
            'email_verified_at' => now(),
        ]
    );
    }
}
