<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    public function toArray($request)
    {
        $actionMethod = $request->route()->getActionMethod();
        return match ($actionMethod) {
            'getUserAchievements' => $this->getUserAchievementsResource(),
            default => $this->defaultResource(),
        };
    }
    public function getUserAchievementsResource()
    {
        return [
            'id'    => $this->id,
            'achievement' => $this->achievement,
            'points' =>  (int) $this->points,
            'description' => $this->description,
            'segments' =>  (int) $this->segments,
            "achievements_done_count" => (int)$this->achievements_done_count
        ];
    }
    public function defaultResource()
    {
        return [
            'id'    => $this->id,
            'achievement' => $this->achievement,
            'points' =>  (int) $this->points,
            'description' => $this->description,
            'segments' =>  (int) $this->segments,
            $this->mergeWhen(
                $this->achievements_done_count != null,
                [
                    "achievements_done_count" => (int)$this->achievements_done_count
                ]
            ),
        ];
    }
}
