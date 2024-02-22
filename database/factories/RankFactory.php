<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rank>
 */
class RankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'limit' => $this->faker->numberBetween(0, 5000),
            'features' => json_encode(["coupon_per_mounth" => $this->faker->numberBetween(0, 4), "discount_on_deliver" => $this->faker->numberBetween(0, 30)]),
            'description' => $this->faker->sentence,
            'color' => $this->faker->hexColor
        ];
    }
}
