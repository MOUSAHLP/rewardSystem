<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use App\Models\AchievementUser;
use App\Models\Point;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct(private AchievementService $achievementService)
    {
    }
    public function getAllAchievements()
    {
        $achievements =AchievementResource::collection(Achievement::all());

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
            $this->achievementService->createPoint($validatedData,$achievement->points);
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

    // AA


    public function add(AchievementRequest $request)
    {

        $data = $request->validated();
        $achievement = new Achievement();

        // Set achievement data
        $achievement->achievement = $data['achievement'];
        $achievement->points = $data['points'];
        $achievement->description = $data['description'];
        $achievement->segments = $data['segments'];

        $achievement->save();

        return $this->successResponse(
            $achievement,
            'dataAddedSuccessfully'
        );
    }

    public function delete(AchievementRequest $request)
    {
        $data = $request->validated();
        // Get the ID of the achievement to delete
        $achievementId = $data['id'];

        // Find the achievement by its ID
        $achievement = Achievement::find($achievementId);

        // Delete the achievement
        $achievement->delete();

        return $this->successResponse(
            null,
            'dataDeletedSuccessfully'
        );
    }

    public function update(AchievementRequest $request)
    {
        $data = $request->validated();

        $achievement = Achievement::find($data['id']);

        if (!$achievement) {
            return response()->json(['message' => 'Achievement not found'], 404);
        }

        $achievement->update([
            'achievement' => $data['achievement'],
            'points' => $data['points'],
            'description' => $data['description'],
            'segments' => $data['segments']
        ]);

        return $this->successResponse(
            $achievement,
            'dataUpdatedSuccessfully'
        );
    }



       // AA

    public function deleteAchievement(AchievementRequest $request)
    {
        $validatedData = $request->validated();

        $user_segments = AchievementUser::where($validatedData);
        $achievement = Achievement::find($validatedData['achievement_id']);
        $remaining_segments = $achievement->segments - ($user_segments->count() - 1);

        if($user_segments->first() == null)return $this->errorResponse('dataNotFound',404);
        $user_segments->first()->delete();

        // if the points were added previously delete them
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
        if ($this->achievementService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_achievements = AchievementResource::collection($this->achievementService->getUserDoneAchievements($request->user_id));

        return $this->successResponse(
            $user_achievements,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserNotDoneAchievements(Request $request)
    {
        if ($this->achievementService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_achievements = AchievementResource::collection($this->achievementService->getUserNotDoneAchievements($request->user_id));

        return $this->successResponse(
            $user_achievements,
            'dataFetchedSuccessfully'
        );
    }
}
