<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ProductsTableSeeder::class,
            InventoryTableSeeder::class,
            SuppliersTableSeeder::class,
            PurchaseOrdersTableSeeder::class,
            OrdersTableSeeder::class,
        ]);
    }
}
