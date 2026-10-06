<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'Product A',
                'product_type_id' => 1,
            ],
            [
                'name' => 'Product B',
                'product_type_id' => 1,
            ],
            [
                'name' => 'Product C',
                'product_type_id' => 2,
            ],
            [
                'name' => 'Product D',
                'product_type_id' => 3,
            ],
            [
                'name' => 'Product E',
                'product_type_id' => 3,
            ],
            [
                'name' => 'Product F',
                'product_type_id' => 1,
            ],
        ]);
    }
}