<?php

namespace App\Services;

use App\Models\Point;
use App\Models\PointInSyrianPound;
use Carbon\Carbon;

class PointService
{
    public function getUserPoints($user_id)
    {
        return Point::where("user_id",$user_id)->get();
    }
    public function getUserPointsSum($user_id)
    {
        return intval(Point::where("user_id",$user_id)->Sum("points"));
    }
    public function getPointsValue($user_points)
    {
       return  $user_points * PointInSyrianPound::point_value();
    }

    public function getUserValidPoints($user_id)
    {
        return Point::where("user_id",$user_id)
                    ->where("used_at",NULL)
                    ->whereDate('expire_at', '>', Carbon::now())->get();
    }

    public function getUserExpiredPoints($user_id)
    {
        return Point::where("user_id",$user_id)
                    ->where("used_at",NULL)
                    ->whereDate('expire_at', '<', Carbon::now())->get();
    }

    public function getUserUsedPoints($user_id)
    {
        return Point::where("user_id",$user_id)
                    ->where("used_at","!=",NULL)->get();
    }

}
