<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $create_date = $this->faker->dateTimeThisMonth;
        $used_date =  $this->faker->boolean ? null : $this->faker->dateTimeBetween($create_date);
        $expire_date = $used_date == null ?  $this->faker->dateTimeBetween($create_date,'+1 week') : $this->faker->dateTimeBetween($used_date,'+1 months');
        return [
            'user_id' => User::all()->random()->id,
            'points' => $this->faker->numberBetween(0, 1000),
            'achievement_id' => Achievement::all()->random()->id,
            'created_at' => $create_date,
            'used_at' => $used_date,
            'expire_at' => $expire_date,
        ];
    }
}
