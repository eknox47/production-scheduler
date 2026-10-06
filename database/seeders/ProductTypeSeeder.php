<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductType::insert([
            [
                'production_speed' => 715,
            ],
            [
                'production_speed' => 770,
            ],
            [
                'production_speed' => 1000,
            ],
        ]);
    }
}
