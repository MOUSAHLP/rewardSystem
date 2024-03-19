<?php

namespace App\Services;

use App\Models\Point;
use App\Models\PointInPound;
use App\Models\User;
use Carbon\Carbon;

class PointService
{
    public function getUserPoints($user_id)
    {
        return Point::where("user_id", $user_id)->get();
    }
    public function getUserPointsSum($user_id)
    {
        return intval(Point::where("user_id", $user_id)
        ->sum("points"));
    }
    public function getUserValidPointsSum($user_id)
    {
        return intval(Point::where("user_id", $user_id)
        ->where("used_at", null)
        ->whereDate('expire_at', '>', Carbon::now())
        ->selectRaw('SUM(points - used_points) as total_points')->first()->total_points);
    }

    public function getPointsValue($user_points)
    {
        return  $user_points * PointInPound::point_value();
    }

    public function getUserValidPoints($user_id)
    {
        return Point::where("user_id", $user_id)
            ->where("used_at", NULL)
            ->whereDate('expire_at', '>', Carbon::now())
            ->orderBy("expire_at" , "ASC")
            ->get();
    }

    public function getUserExpiredPoints($user_id)
    {
        return Point::where("user_id", $user_id)
            ->where("used_at", NULL)
            ->whereDate('expire_at', '<', Carbon::now())->get();
    }

    public function getUserUsedPoints($user_id)
    {
        return Point::where("user_id", $user_id)
            ->where("used_at", "!=", NULL)->get();
    }

    public static function checkIfUserExists($user_id)
    {
        return User::where("id", $user_id)->count() == 0;
    }
}
