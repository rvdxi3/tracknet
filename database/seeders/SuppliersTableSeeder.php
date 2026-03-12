<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'name' => 'Intel Corporation',
                'contact_person' => 'John Smith',
                'email' => 'john.smith@intel.com',
                'phone' => '800-123-4567',
                'address' => '2200 Mission College Blvd, Santa Clara, CA 95054'
            ],
            [
                'name' => 'AMD Inc.',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah.johnson@amd.com',
                'phone' => '800-234-5678',
                'address' => '2485 Augustine Dr, Santa Clara, CA 95054'
            ],
            [
                'name' => 'NVIDIA Corporation',
                'contact_person' => 'Mike Brown',
                'email' => 'mike.brown@nvidia.com',
                'phone' => '800-345-6789',
                'address' => '2788 San Tomas Expy, Santa Clara, CA 95051'
            ],
            [
                'name' => 'Corsair Components',
                'contact_person' => 'Lisa Wong',
                'email' => 'lisa.wong@corsair.com',
                'phone' => '800-456-7890',
                'address' => '47100 Bayside Pkwy, Fremont, CA 94538'
            ],
            [
                'name' => 'Samsung Semiconductor',
                'contact_person' => 'David Kim',
                'email' => 'david.kim@samsung.com',
                'phone' => '800-567-8901',
                'address' => '3655 N 1st St, San Jose, CA 95134'
            ],
        ];
        
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
