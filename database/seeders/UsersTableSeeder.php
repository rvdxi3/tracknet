<?php

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
            ['name' => 'Inventory',      'description' => 'Inventory management team'],
            ['name' => 'Sales',          'description' => 'Sales and customer service'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $now = now();

        // Staff users — active by default, bypass MFA + approval
        User::create([
            'name'             => 'Admin User',
            'email'            => 'admin@example.com',
            'password'         => Hash::make('password'),
            'role'             => 'admin',
            'department_id'    => Department::where('name', 'Administration')->value('id'),
            'is_active'        => true,
            'mfa_verified_at'  => $now,
            'approved_at'      => $now,
        ]);

        User::create([
            'name'             => 'Inventory User',
            'email'            => 'inventory@example.com',
            'password'         => Hash::make('password'),
            'role'             => 'inventory',
            'department_id'    => Department::where('name', 'Inventory')->value('id'),
            'is_active'        => true,
            'mfa_verified_at'  => $now,
            'approved_at'      => $now,
        ]);

        User::create([
            'name'             => 'Sales User',
            'email'            => 'sales@example.com',
            'password'         => Hash::make('password'),
            'role'             => 'sales',
            'department_id'    => Department::where('name', 'Sales')->value('id'),
            'is_active'        => true,
            'mfa_verified_at'  => $now,
            'approved_at'      => $now,
        ]);

        // Sample approved customers for demo data
        User::factory()->count(10)->create([
            'role'            => 'customer',
            'department_id'   => null,
            'is_active'       => true,
            'mfa_verified_at' => $now,
            'approved_at'     => $now,
        ]);
    }
}
