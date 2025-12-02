<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin in admins table
        Admin::firstOrCreate(
            ['email' => 'admin1@mail.com'],
            [
                'name' => 'admin1',
                'password' => 'password123',
                'is_active' => true,
            ]
        );

        // Also create in users table for compatibility (optional)
        User::firstOrCreate(
            ['email' => 'admin1@mail.com'],
            [
                'name' => 'admin1',
                'password' => 'password123',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create sample customer
        User::firstOrCreate(
            ['email' => 'customer@mail.com'],
            [
                'name' => 'Customer Demo',
                'password' => 'password123',
                'role' => 'customer',
                'is_active' => true,
            ]
        );
    }
}
