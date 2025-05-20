<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Service;

class EmployeeServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Get all employees and services
        $employees = Employee::all();
        $services = Service::all();

        // For each service, assign some random employees
        foreach ($services as $service) {
            // Get 1-3 random employees for each service
            $randomEmployees = $employees->random(rand(1, min(3, $employees->count())));
            
            // Attach these employees to the service
            foreach ($randomEmployees as $employee) {
                $service->employees()->attach($employee->id);
            }
        }
    }
} 