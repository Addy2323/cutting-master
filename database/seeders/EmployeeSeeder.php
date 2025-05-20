<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'position' => 'Senior Stylist',
                'bio' => 'Experienced stylist with 10 years of expertise.',
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '0987654321',
                'position' => 'Color Specialist',
                'bio' => 'Specializes in hair coloring and treatments.',
                'image' => 'default.jpg',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'phone' => '5555555555',
                'position' => 'Barber',
                'bio' => 'Expert in men\'s grooming and styling.',
                'image' => 'default.jpg',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'role' => 'employee',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'name' => $employeeData['name'],
                'phone' => $employeeData['phone'],
                'position' => $employeeData['position'],
                'bio' => $employeeData['bio'],
                'image' => $employeeData['image'],
            ]);
        }
    }
} 