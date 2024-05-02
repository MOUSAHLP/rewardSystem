<?php

namespace App\Http\Resources;

use App\Enums\CouponResources;
use App\Enums\CouponTypes;
use App\Models\CouponType;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{

    public function toArray($request)
    {
        $actionMethod = $request->route()->getActionMethod();
        return match ($actionMethod) {
            'couponsReport' => $this->CouponReportResource(),
            default => $this->defaultResource(),
        };
    }
    public function defaultResource()
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
            $this->mergeWhen(
                $this->coupon_resource != null,
                [
                    "coupon_resource" => $this->coupon_resource
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
    public function usedCoupon($user_coupon)
    {
        return [
            'id'    => $this->id,
            'value' => (int)$this->value,
            'price' => (int) $this->price,
            'coupon_type'  => new CouponTypeResource(CouponType::find($this->coupon_type_id)),
            'coupon_code' => $user_coupon->coupon_code,
            'coupon_resource' => CouponResources::getName($user_coupon->coupon_resource),
            'description'  => $this->description,
            'created_at'  => $this->created_at,
            "used_at" => $user_coupon->used_at,
            "expire_at" => $user_coupon->expire_at,
        ];
    }
    public function notUsedCoupon($user_coupon)
    {
        return [
            'id'    => $this->id,
            'value' => (int)$this->value,
            'price' => (int) $this->price,
            'coupon_type'  => new CouponTypeResource(CouponType::find($this->coupon_type_id)),
            'coupon_code' => $user_coupon->coupon_code,
            'coupon_resource' => CouponResources::getName($user_coupon->coupon_resource),
            'description'  => $this->description,
            'created_at'  => $this->created_at,
            "expire_at" => $user_coupon->expire_at,
        ];
    }

    public function CouponReportResource()
    {
        return [
            'id'    => $this->id,
            'label' => $this->getCouponLabel($this),
            'count'  => $this->purchases_count,
        ];
    }

    public function getCouponLabel($coupon)
    {
        $type = CouponType::find($coupon->coupon_type_id)->type;
        if ($type == CouponTypes::FIXED_VALUE) {
            return __("messages.coupons.discount").$coupon->value;
        }
        if ($type == CouponTypes::PERCENTAGE) {
            return __("messages.coupons.discountOnProducts").$coupon->value;
        }
        if ($type == CouponTypes::DELIVERY) {
            return __("messages.coupons.discountOnDelivery").$coupon->value;
        }
        return __("messages.coupons.discount").$coupon->value;
    }
}
