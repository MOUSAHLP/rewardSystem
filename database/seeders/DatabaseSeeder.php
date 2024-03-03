<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();
        \App\Models\Achievement::factory(10)->create();
        // \App\Models\AchievementUser::factory(100)->create();
        \App\Models\CouponType::factory(3)->create();
        \App\Models\Coupon::factory(10)->create();
        \App\Models\CouponPrice::factory(10)->create();
        \App\Models\CouponUser::factory(30)->create();
        \App\Models\Point::factory(30)->create();
        \App\Models\PointInPound::factory(1)->create();
        \App\Models\Rank::factory(6)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
