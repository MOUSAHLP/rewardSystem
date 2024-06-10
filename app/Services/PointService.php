<?php

namespace App\Services;

use App\Models\Point;
use App\Models\PointInPound;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            ->orderBy("expire_at", "ASC")
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

    public function usedPointsReport()
    {

        return Purchase::select([
            DB::raw('sum(points) as points'),
            DB::raw('month(created_at) as month'),
            DB::raw('year(created_at) as year')
        ])
            ->when(request()->has('year') && request()->year != "", function ($query) {
                $query->whereYear('created_at', request()->year);
            }, function ($query) {
                $query->whereYear('created_at', Carbon::now()->format('Y'));
            })
            ->groupBy(['year', 'month'])
            ->get();
    }

    public static function createPoint($validatedData)
    {
        $point = new Point();
        $point->user_id = $validatedData['user_id'];
        $point->points = $validatedData["points"];
        $point->achievement_id = $validatedData['achievement_id'];
        $point->created_at = Carbon::now()->format("Y-m-d H:i:s");
        $point->used_at = null;
        $point->expire_at = Carbon::now()->addDays(90)->format("Y-m-d H:i:s");
        $point->save();
    }
    public static function checkIfUserExists($user_id)
    {
        return User::where("id", $user_id)->count() == 0;
    }
}
