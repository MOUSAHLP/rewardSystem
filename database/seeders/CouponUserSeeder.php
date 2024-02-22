<?php

namespace Database\Seeders;

use App\Models\CouponUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CouponUser::factory()->count(10)->create();
    }
}
