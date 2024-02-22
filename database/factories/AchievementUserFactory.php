<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\AchievementUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AchievementUser>
 */
class AchievementUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $achievement = Achievement::all()->random();
        $user = User::all()->random();
        $achievementsdone = AchievementUser::where("achievement_id" , $achievement->id)->where("user_id",$user->id)->count();
        if($achievementsdone != $achievement->segments){
            return [
                'achievement_id'=> $achievement->id,
                'user_id'=> $user->id,
            ];
        }
        return $this->definition();
    }
}
