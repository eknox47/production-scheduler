<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        //Yes, I created those company names myself
        Customer::insert([
            [
                'name' => 'Sofa Soda Soft Drink Co',
                'email' => 'support@sssd.ca',
            ],
            [
                'name' => 'Got Hot Beer?',
                'email' => 'customerservicer@hotbeer.ca',
            ],
            [
                'name' => 'Crazy Yeti Energy Drink Co',
                'email' => 'contact@cyed.ca',
            ],
        ]);
    }
}