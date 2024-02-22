<?php

namespace Database\Seeders;

use App\Models\CouponType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CouponType::factory()->count(10)->create();
    }
}
