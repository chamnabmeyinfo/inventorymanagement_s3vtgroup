<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Truck Scale',
                'slug' => 'truck-scale',
                'sort_order' => 1,
            ],
            [
                'name' => 'Weighbridge',
                'slug' => 'weighbridge',
                'sort_order' => 2,
            ],
            [
                'name' => 'Digital Scale',
                'slug' => 'digital-scale',
                'sort_order' => 3,
            ],
            [
                'name' => 'Storage Racking',
                'slug' => 'storage-racking',
                'sort_order' => 4,
            ],
            [
                'name' => 'Lifting',
                'slug' => 'lifting',
                'sort_order' => 5,
            ],
            [
                'name' => 'Forklift & Material Handling',
                'slug' => 'forklift-material-handling',
                'sort_order' => 6,
            ],
            [
                'name' => 'Parking System',
                'slug' => 'parking-system',
                'sort_order' => 7,
            ],
            [
                'name' => 'Storage & Pallets',
                'slug' => 'storage-pallets',
                'sort_order' => 8,
            ],
            [
                'name' => 'Storage Baskets',
                'slug' => 'storage-baskets',
                'sort_order' => 9,
            ],
            [
                'name' => 'Dock & Material Lift',
                'slug' => 'dock-material-lift',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
