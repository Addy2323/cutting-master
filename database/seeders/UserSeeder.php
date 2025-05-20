<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $timestamp = time();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => "admin{$timestamp}@example.com",
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create subscriber user
        User::create([
            'name' => 'Subscriber User',
            'email' => "subscriber{$timestamp}@example.com",
            'password' => Hash::make('password'),
            'role' => 'subscriber',
        ]);
    }
} 