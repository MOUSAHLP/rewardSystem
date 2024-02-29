<?php

namespace App\Services;

use App\Models\PointInSyrianPound;
use App\Models\Rank;
use App\Models\User;

class RankService
{
    public function getUserCurrentRank($user_points)
    {
        return Rank::where("limit", "<", $user_points)->orderBy("limit", "DESC")->first();
    }

    public static function getUserNextRank($user_points)
    {
        return Rank::where("limit", ">", $user_points)->orderBy("limit", "ASC")->first();
    }

    public static function checkIfUserExists($user_id)
    {
        return User::where("id", $user_id)->count() == 0;
    }

    public static function getRankUsers($rank_limit)
    {
        $next_rank_limit =  RankService::getRankNextRank($rank_limit)->limit;
        $rank_users = [];

        $users = User::with("points")->get();
        foreach ($users as $user) {
                if ($user->points->sum("points") > $rank_limit && $next_rank_limit >= $user->points->sum("points")) {
                    $user["total_points"] = $user->points->sum("points");
                    unset($user->points);
                    $rank_users[] = $user;
                };
        }
        return  $rank_users;
    }

    public static function getRankNextRank($rank_limit)
    {
        return Rank::where("limit", ">", $rank_limit)->orderBy("limit", "ASC")->first();
    }

}
