<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'role_id' => 1,
            'email' => 'admin@admin.com',
            'password' => 'password',
            'updated_by' => 1,
        ]);

        User::create([
            'name' => 'User',
            'role_id' => 2,
            'email' => 'user@user.com',
            'password' => 'password',
            'updated_by' => 1,
        ]);
    }
}
