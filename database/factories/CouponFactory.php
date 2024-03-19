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
        $create_date= $this->faker->dateTimeThisMonth;
        return [
            'coupon_type_id' => CouponType::all()->random()->id,
            'value'=> $this->faker->numberBetween(0, 1000),
            'description'=> $this->faker->sentence,
            'created_at'=>$create_date,
        ];
    }
}
