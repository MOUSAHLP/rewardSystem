<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponUser>
 */
class CouponUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $create_date = $this->faker->dateTimeThisMonth;
        $used_date = $this->faker->boolean ? null : $this->faker->dateTimeBetween($create_date, '+1 months');
        $expire_date = $this->faker->boolean ?  $this->faker->dateTimeBetween($create_date, '+1 week') : $this->faker->dateTimeBetween($create_date, '+1 months');
        return [
            'user_id' => User::all()->random()->id,
            'coupon_id' => Coupon::all()->random()->id,
            'coupon_code' => $this->faker->uuid,
            'coupon_resource' => $this->faker->numberBetween(1, 3),
            'used_at' => $used_date,
            'expire_at' => $expire_date,
        ];
    }
}
