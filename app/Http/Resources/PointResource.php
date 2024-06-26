<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    public function toArray($request)
    {
        $actionMethod = $request->route()->getActionMethod();
        return match ($actionMethod) {
            'usedPointsReport' => $this->usedPointsReport(),
            default => $this->defaultResource(),
        };
    }
    public function defaultResource()
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'points'          => (int)$this->points,
            'used_points'     => (int)$this->used_points,
            'achievement_id'  => (int)$this->achievement_id,
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

    public function usedPointsReport()
    {
        return [
            'points'     => (int)$this->points,
            'month'       => $this->month,
            'year'       => $this->year,
        ];
    }

}
