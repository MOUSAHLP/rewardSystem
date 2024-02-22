<?php

namespace Database\Seeders;

use App\Models\CouponPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CouponPrice::factory()->count(10)->create();
    }
}
