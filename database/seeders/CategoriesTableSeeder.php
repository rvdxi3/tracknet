<?php

// database/seeders/CategoriesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Processors', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graphics Cards', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Memory', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Storage', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Motherboards', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Power Supplies', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cases', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cooling', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}