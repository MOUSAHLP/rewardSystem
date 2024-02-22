<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementRequest;
use App\Models\Achievement;
use App\Models\AchievementUser;
use App\Models\Point;
use App\Models\User;
use App\Services\AchievementService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Client\HttpClientException;
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
    public function addAchievement(AchievementRequest $request)
    {
        $validatedData = $request->validated();

        $get_user_segments = AchievementUser::where('achievement_id', $validatedData['achievement_id'])
            ->where('user_id', $validatedData['user_id'])
            ->get();

        // if the segment is greater than the achievement's segments
        $achievement = Achievement::find($validatedData['achievement_id']);
        if ( $achievement->segments <= $get_user_segments->count()) {
            return $this->errorResponse(
                'achievementAlreadyDone',
                403
            );
        }

        $AchievementUser = AchievementUser::create($validatedData);

        $is_done = $AchievementUser->achievement->segments == $get_user_segments->count() + 1;
        $remaining_segments = $AchievementUser->achievement->segments - ($get_user_segments->count() + 1);

        // if the Achievement is done add points to the User
        if ($is_done) {
            $point = new Point();
            $point->user_id = $validatedData['user_id'];
            $point->points = $achievement->points;
            $point->achievement_id = $validatedData['achievement_id'];
            $point->created_at = Carbon::now()->format("Y-m-d H:i:s");
            $point->used_at = null;
            $point->expire_at = Carbon::now()->addDays(90)->format("Y-m-d H:i:s");
            $point->save();
        }

        $data = [
            "achievement_segments" => $AchievementUser->achievement->segments,
            "is_done"           => $is_done,
            "remaining_segments" => $remaining_segments,
        ];

        return $this->successResponse(
            $data,
            'dataAddedSuccessfully'
        );
    }

    public function deleteAchievement(AchievementRequest $request)
    {
        $validatedData = $request->validated();

        $user_segments = AchievementUser::where($validatedData);
        $achievement = Achievement::find($validatedData['achievement_id']);
        $remaining_segments = $achievement->segments - ($user_segments->count() - 1);

        if($user_segments->first() == null)return $this->errorResponse('dataNotFound',404);
        $user_segments->first()->delete();

        // if the points were added previously delete it
        $point = Point::where($validatedData)->first();
        if(isset($point)){
            $point->delete();
        }


        $data = [
            "achievement_segments" => $achievement->segments,
            "remaining_segments" => $remaining_segments,
        ];

        return $this->successResponse(
            $data,
            'dataDeletedSuccessfully'
        );
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

    public function getUserNotDoneAchievements(Request $request)
    {

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
