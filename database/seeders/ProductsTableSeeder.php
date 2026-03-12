<?php

// database/seeders/ProductsTableSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();
        
        $products = [
            [
                'name' => 'Intel Core i9-12900K',
                'description' => '16-core, 24-thread unlocked desktop processor',
                'price' => 599.99,
                'category_id' => $categories->where('name', 'Processors')->first()->id,
                'sku' => 'CPU-INT-12900K',
                'is_featured' => true
            ],
            [
                'name' => 'AMD Ryzen 9 5950X',
                'description' => '16-core, 32-thread unlocked desktop processor',
                'price' => 549.99,
                'category_id' => $categories->where('name', 'Processors')->first()->id,
                'sku' => 'CPU-AMD-5950X',
                'is_featured' => true
            ],
            [
                'name' => 'NVIDIA GeForce RTX 3090',
                'description' => '24GB GDDR6X graphics card',
                'price' => 1499.99,
                'category_id' => $categories->where('name', 'Graphics Cards')->first()->id,
                'sku' => 'GPU-NV-3090',
                'is_featured' => true
            ],
            [
                'name' => 'AMD Radeon RX 6900 XT',
                'description' => '16GB GDDR6 graphics card',
                'price' => 999.99,
                'category_id' => $categories->where('name', 'Graphics Cards')->first()->id,
                'sku' => 'GPU-AMD-6900XT',
                'is_featured' => true
            ],
            [
                'name' => 'Corsair Vengeance RGB Pro 32GB',
                'description' => 'DDR4 3600MHz memory kit (2x16GB)',
                'price' => 169.99,
                'category_id' => $categories->where('name', 'Memory')->first()->id,
                'sku' => 'MEM-COR-32RGB',
                'is_featured' => false
            ],
            [
                'name' => 'Samsung 980 Pro 1TB',
                'description' => 'PCIe 4.0 NVMe SSD',
                'price' => 149.99,
                'category_id' => $categories->where('name', 'Storage')->first()->id,
                'sku' => 'SSD-SAM-980PRO1T',
                'is_featured' => false
            ],
            [
                'name' => 'ASUS ROG Strix Z690-E',
                'description' => 'LGA 1700 ATX motherboard',
                'price' => 399.99,
                'category_id' => $categories->where('name', 'Motherboards')->first()->id,
                'sku' => 'MB-ASUS-Z690E',
                'is_featured' => false
            ],
            [
                'name' => 'Corsair RM850x',
                'description' => '850W 80+ Gold fully modular PSU',
                'price' => 129.99,
                'category_id' => $categories->where('name', 'Power Supplies')->first()->id,
                'sku' => 'PSU-COR-RM850X',
                'is_featured' => false
            ],
            [
                'name' => 'NZXT H510 Elite',
                'description' => 'Mid-tower ATX case with tempered glass',
                'price' => 149.99,
                'category_id' => $categories->where('name', 'Cases')->first()->id,
                'sku' => 'CASE-NZXT-H510E',
                'is_featured' => false
            ],
            [
                'name' => 'Noctua NH-D15',
                'description' => 'Premium CPU cooler with dual fans',
                'price' => 99.99,
                'category_id' => $categories->where('name', 'Cooling')->first()->id,
                'sku' => 'COOL-NOC-NHD15',
                'is_featured' => false
            ],
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
