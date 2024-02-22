<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponPrice>
 */
class CouponPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coupon_points'=> $this->faker->numberBetween(0, 1000),
            'coupon_value'=> $this->faker->numberBetween(0, 1000),
        ];
    }
}
