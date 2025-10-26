<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'role_name' => 'Admin',
            'updated_by' => 1,
        ]);
        Role::create([
            'role_name' => 'User',
            'updated_by' => 1,
        ]);
    }
}
