<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SyncEmployeeUsersSeeder extends Seeder
{
    public function run()
    {
        // Ensure the 'employee' role exists
        Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        $defaultDays = [
            'monday' => ['09:00-12:00', '14:00-18:00'],
            'tuesday' => ['09:00-17:00'],
            'wednesday' => ['09:00-17:00'],
            'thursday' => ['09:00-17:00'],
            'friday' => ['09:00-17:00'],
        ];

        $employeeRoleUsers = User::role('employee')->get();

        foreach ($employeeRoleUsers as $user) {
            if (!$user->employee) {
                Employee::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'position' => 'Staff',
                    'bio' => 'Auto-created employee profile.',
                    'image' => null,
                    'days' => $defaultDays,
                    'slot_duration' => 30,
                    'break_duration' => 0,
                ]);
            }
        }
    }
} 