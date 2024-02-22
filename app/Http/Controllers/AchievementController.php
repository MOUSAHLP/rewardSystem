<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementRequest;
use App\Models\Achievement;
use App\Models\AchievementUser;
use App\Models\Point;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function getAllAchievements()
    {
        $achievements = Achievement::all();

        return $this->successResponse(
            $achievements,
            'dataFetchedSuccessfully'
        );
    }
    public function addAchievement(AchievementRequest $request){
        $validatedData = $request->validated();
        $get_user_segments = AchievementUser::where('achievement_id', $validatedData['achievement_id'])
        ->where('user_id', $validatedData['user_id'])
        ->get();

        // if the segment is greater than the achievement's segments
        $achievement=Achievement::find($validatedData['achievement_id']);
        if($achievement->segments <= $get_user_segments->count()){
            return $this->errorResponse(
                'achievementAlreadyDone'
            );
        }

        $AchievementUser = AchievementUser::create($validatedData);
        $is_done= $AchievementUser->achievement->segments == $get_user_segments->count() + 1;
        $remaining_segments= $AchievementUser->achievement->segments - ($get_user_segments->count() + 1);

        // if the Achievement is done add points to the User
        // if($is_done){
        //     $point = new Point();
        //     $point->user_id = ;
        //     $point->points;
        //     $point->achievement_id;
        //     $point->created_at;
        //     $point->expire_at;
        // }

        $data=[
            "achievement_segments"=>$AchievementUser->achievement->segments,
            "is_done"           =>$is_done,
            "remaining_segments"=>$remaining_segments,
        ];

        return $this->successResponse(
            $data,
            'dataAddedSuccessfully'
        );
    }

    public function updateAchievement(Request $request){
        return "upsate";
    }
    public function deleteAchievement(Request $request){
        return "delete";
    }

    public function getUserDoneAchievements(Request $request)
    {
        $user_points = Point::where("user_id", $request->user_id)->get("achievement_id")->toArray();
        $user_achievements = Achievement::whereIn("id", $user_points)->get();

        return $this->successResponse(
            $user_achievements,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserNotDoneAchievements(Request $request){

        $user_points = Point::where("user_id", $request->user_id)->get("achievement_id")->toArray();
        $user_achievements = Achievement::whereNotIn("id", $user_points)
        ->withCount(["achievementsDone" => function ($query) use ($request) {
            $query->where('user_id', $request->user_id);
        }])->get();

        return $this->successResponse(
            $user_achievements,
            'dataFetchedSuccessfully'
        );
    }
}
