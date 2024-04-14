<?php

namespace App\Http\Resources;

use App\Models\CouponType;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'value' => (int)$this->value,
            'price' => (int) $this->price,
            'coupon_type'  => new CouponTypeResource(CouponType::find($this->coupon_type_id)),
            $this->mergeWhen(
                $this->coupon_code != null,
                [
                    "coupon_code" => $this->coupon_code
                ]
            ),
            'description'  => $this->description,
            'created_at'  => $this->created_at,

            $this->mergeWhen(
                $this->used_at != null,
                [
                    "used_at" => $this->used_at
                ]
            ),
            $this->mergeWhen(
                $this->expire_at != null,
                [
                    "expire_at" => $this->expire_at
                ]
            ),

        ];
    }
    public function usedCoupon($coupon_code, $expire_at, $used_at)
    {
        return [
            'id'    => $this->id,
            'value' => (int)$this->value,
            'price' => (int) $this->price,
            'coupon_type'  => new CouponTypeResource(CouponType::find($this->coupon_type_id)),
            'coupon_code' => $coupon_code,
            'description'  => $this->description,
            'created_at'  => $this->created_at,
            "used_at" => $used_at,
            "expire_at" => $expire_at,
        ];
    }
    public function notUsedCoupon($coupon_code, $expire_at)
    {
        return [
            'id'    => $this->id,
            'value' => (int)$this->value,
            'price' => (int) $this->price,
            'coupon_type'  => new CouponTypeResource(CouponType::find($this->coupon_type_id)),
            'coupon_code' => $coupon_code,
            'description'  => $this->description,
            'created_at'  => $this->created_at,
            "expire_at" => $expire_at,
        ];
    }
}
