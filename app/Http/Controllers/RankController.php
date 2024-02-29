<?php

namespace App\Http\Controllers;

use App\Http\Requests\RankRequest;
use App\Models\Rank;
use App\Models\User;
use App\Services\PointService;
use App\Services\RankService;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function __construct(private PointService $pointService, private RankService $rankService)
    {
    }
    public function getRanks()
    {
        $ranks = Rank::orderBy("limit", "ASC")->get();

        return $this->successResponse(
            $ranks,
            'dataFetchedSuccessfully'
        );
    }

    public function getRank(Request $request)
    {
        $rank = Rank::where("id", $request->rank_id)->get()->first();
        if (isset($rank) == 0) {
            return $this->errorResponse("Ranks.NotFound", 400);
        }

        return $this->successResponse(
            $rank,
            'dataFetchedSuccessfully'
        );
    }


    public function getRankUsers(Request $request){

        $rank = Rank::where("id", $request->rank_id)->get()->first();
        if (isset($rank) == 0) {
            return $this->errorResponse("Ranks.NotFound", 400);
        }

        $rank_users = $this->rankService->getRankUsers($rank->limit);

        $data = [
            "users"=> $rank_users,
            "rank"=> $rank,
        ];

        return $this->successResponse(
            $data,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserCurrentRank(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_points = $this->pointService->getUserPointsSum($request->user_id);
        $user_rank = $this->rankService->getUserCurrentRank($user_points);

        return $this->successResponse(
            $user_rank,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserNextRank(Request $request)
    {

        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_points = $this->pointService->getUserPointsSum($request->user_id);
        $user_next_rank = $this->rankService->getUserNextRank($user_points);

        return $this->successResponse(
            $user_next_rank,
            'dataFetchedSuccessfully'
        );
    }

    public function addRank(RankRequest $request)
    {

        $validatedData = $request->validated();

        $rank = Rank::create($validatedData);

        return $this->successResponse(
            $rank,
            'dataAddedSuccessfully'
        );
    }
    public function updateRank(RankRequest $request)
    {

        $validatedData = $request->validated();

        Rank::where("id", $validatedData["id"])->update($validatedData);

        return $this->successResponse(
            [],
            'dataUpdatedSuccessfully'
        );
    }

    public function deleteRank(RankRequest $request)
    {

        $validatedData = $request->validated();

        Rank::where("id", $validatedData["id"])->delete($validatedData);

        return $this->successResponse(
            [],
            'dataDeletedSuccessfully'
        );
    }
}
