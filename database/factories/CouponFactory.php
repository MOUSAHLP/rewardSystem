<?php

namespace Database\Factories;

use App\Models\CouponType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creat_date= $this->faker->dateTimeThisMonth;
        return [
            'coupon_type_id' => CouponType::all()->random()->id,
            'value'=> $this->faker->numberBetween(0, 1000),
            'coupon_code'=> $this->faker->slug,
            'description'=> $this->faker->sentence,
            'created_at'=>$creat_date,
            'expire_at'=> $this->faker->dateTimeBetween($creat_date),
        ];
    }
}
