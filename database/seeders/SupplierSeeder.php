<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Equipment Distributor Co.', 'contact_person' => 'Sales Team', 'sort_order' => 1],
            ['name' => 'Industrial Scale Supplies', 'contact_person' => null, 'sort_order' => 2],
            ['name' => 'Warehouse Solutions Ltd', 'contact_person' => null, 'sort_order' => 3],
        ];

        foreach ($suppliers as $data) {
            Supplier::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
