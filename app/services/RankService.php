<?php

namespace App\Services;

use App\Models\PointInSyrianPound;
use App\Models\Rank;

class RankService
{
    public function getUserCurrentRank($user_points)
    {
        return Rank::where("limit","<",$user_points)->orderBy("limit","DESC")->first();
    }

    public static function getUserNextRank($user_points){
        return Rank::where("limit",">",$user_points)->orderBy("limit","ASC")->first();
    }

}
