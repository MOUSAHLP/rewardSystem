<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Point;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{

    public static function createPoint($validatedData, $points)
    {
        $point = new Point();
        $point->user_id = $validatedData['user_id'];
        $point->points = $points;
        $point->achievement_id = $validatedData['achievement_id'];
        $point->created_at = Carbon::now()->format("Y-m-d H:i:s");
        $point->used_at = null;
        $point->expire_at = Carbon::now()->addDays(90)->format("Y-m-d H:i:s");
        $point->save();
    }


    public static function getUserDoneAchievements($user_id)
    {
        $user_points = Point::where("user_id", $user_id)->get("achievement_id")->toArray();
        return Achievement::whereIn("id", $user_points)->get();
    }
    public static function getUserNotDoneAchievements($user_id)
    {
        $user_points = Point::where("user_id", $user_id)->get("achievement_id")->toArray();
        return Achievement::whereNotIn("id", $user_points)
            ->withCount(["achievementsDone" => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])->get();
    }
    public static function checkIfUserExists($user_id)
    {
        return User::where("id", $user_id)->count() == 0;
    }
}
