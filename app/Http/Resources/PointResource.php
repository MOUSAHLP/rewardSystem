<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'points'          => $this->points,
            'used_points'     => $this->used_points,
            'achievement_id'  => $this->achievement_id,
            'created_at'      => $this->created_at,
            'used_at'         => $this->used_at,
            'expire_at'       => $this->expire_at,
            'is_expired'       => $this->IsExpired($this->expire_at),
        ];
    }
    public function IsExpired($expire_at)
    {
        return  $expire_at < Carbon::now();
    }
}
