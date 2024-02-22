<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use App\Services\PointService;
use App\Services\RankService;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function __construct(private PointService $pointService ,private RankService $rankService )
    {
    }
    public function getRanks(){
        $ranks = Rank::orderBy("limit","ASC")->get();

        return $this->successResponse(
            $ranks,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserCurrentRank(Request $request){
        $user_points =$this->pointService->getUserPointsSum($request->user_id);
        $user_rank =$this->rankService->getUserCurrentRank($user_points);

        return $this->successResponse(
            $user_rank,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserNextRank(Request $request){
        $user_points =$this->pointService->getUserPointsSum($request->user_id);
        $user_next_rank =$this->rankService->getUserNextRank($user_points);

        return $this->successResponse(
            $user_next_rank,
            'dataFetchedSuccessfully'
        );
    }


}
