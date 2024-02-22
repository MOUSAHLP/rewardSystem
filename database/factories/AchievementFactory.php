<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'achievement'=>$this->faker->sentence,
            'points'=>$this->faker->numberBetween(0, 1000),
            'description'=>$this->faker->sentence,
            'segments'=>$this->faker->numberBetween(1, 3),
        ];
    }
}
