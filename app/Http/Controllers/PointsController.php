<?php

namespace App\Http\Controllers;

use App\Http\Requests\PointRequest;
use App\Models\PointInSyrianPound;
use Illuminate\Http\Request;
use App\Services\PointService;
use App\Services\RankService;

class PointsController extends Controller
{
    public function __construct(private PointService $pointService, private RankService $rankService)
    {
    }

    public function getUserPoints(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_valid_points = $this->pointService->getUserValidPoints($request->user_id);
        $user_expired_points = $this->pointService->getUserExpiredPoints($request->user_id);
        $user_used_points = $this->pointService->getUserUsedPoints($request->user_id);

        $data = [
            "valid_points" => $user_valid_points,
            "expired_points" => $user_expired_points,
            "used_points" => $user_used_points,
        ];
        return $this->successResponse(
            $data,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserStatistics(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

            $user_points = $this->pointService->getUserPointsSum($request->user_id);
            $points_value = $this->pointService->getPointsValue($user_points);
            $user_rank = $this->rankService->getUserCurrentRank($user_points);
            $user_next_rank = $this->rankService->getUserNextRank($user_points);

            $data = [
                "user_points" => $user_points,
                "points_value" => $points_value,
                "user_rank" => $user_rank,
                "user_next_rank" => $user_next_rank,
            ];
            return $this->successResponse(
                $data,
                'dataFetchedSuccessfully'
            );
    }

    public function getUserTotalPoints(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_points = $this->pointService->getUserPointsSum($request->user_id);
        return $this->successResponse(
            $user_points,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserValidPoints(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_valid_points = $this->pointService->getUserValidPoints($request->user_id);

        return $this->successResponse(
            $user_valid_points,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserExpiredPoints(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_expired_points = $this->pointService->getUserExpiredPoints($request->user_id);

        return $this->successResponse(
            $user_expired_points,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserUsedPoints(Request $request)
    {
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_used_points = $this->pointService->getUserUsedPoints($request->user_id);

        return $this->successResponse(
            $user_used_points,
            'dataFetchedSuccessfully'
        );
    }

    public function getPointsValue()
    {
        $points_value = PointInSyrianPound::point_value();
        return $this->successResponse(
            $points_value,
            'dataFetchedSuccessfully'
        );
    }

    public function setPointsValue(PointRequest $request)
    {
        $validatedData = $request->validated();
        $points_value = PointInSyrianPound::first()->update(["value"=>$request["value"]]);
        return $this->successResponse(
            $points_value,
            'dataUpdatedSuccessfully'
        );
    }


}
