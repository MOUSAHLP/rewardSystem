<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    public function toArray($request)
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
