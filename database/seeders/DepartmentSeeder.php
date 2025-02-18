<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['code' => 'TSSD', 'name' => 'Technical Support Services Division'],
            ['code' => 'IMSD', 'name' => 'Information Management Systems Division'],
            ['code' => 'PLANNING', 'name' => 'Planning Division'],
            ['code' => 'HRMO', 'name' => 'Human Resource Management Office'],
            ['code' => 'SUPPLY', 'name' => 'Supply Division'],
            ['code' => 'ACCOUNTING', 'name' => 'Accounting Division'],
            ['code' => 'MALSU', 'name' => 'MALSU Division'],
            ['code' => 'OARD', 'name' => 'OARD Division'],
            ['code' => 'ORD', 'name' => 'Office of the Regional Director'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
} 