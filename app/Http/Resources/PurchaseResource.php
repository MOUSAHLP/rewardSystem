<?php

namespace App\Http\Resources;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray($request)
    {
            return [
            'id'    => $this->id,
            'points' =>  (int) $this->points,
            'user' => User::find($this->user_id),
            'coupon' => new CouponResource(Coupon::find($this->coupon_id)),
        ];
    }
}
