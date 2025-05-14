<?php

// database/seeders/UsersTableSeeder.php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create departments
        $departments = [
            ['name' => 'Administration', 'description' => 'System administrators'],
            ['name' => 'Inventory', 'description' => 'Inventory management team'],
            ['name' => 'Sales', 'description' => 'Sales and customer service'],
        ];
        
        foreach ($departments as $department) {
            Department::create($department);
        }
        
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => Department::where('name', 'Administration')->first()->id
        ]);
        
        // Create inventory user
        User::create([
            'name' => 'Inventory User',
            'email' => 'inventory@example.com',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'department_id' => Department::where('name', 'Inventory')->first()->id
        ]);
        
        // Create sales user
        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'department_id' => Department::where('name', 'Sales')->first()->id
        ]);
        
        // Create some customers
        User::factory()->count(10)->create([
            'role' => 'customer',
            'department_id' => null
        ]);
    }
}
