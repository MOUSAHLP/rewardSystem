<?php

namespace App\Services;

use App\Models\CouponUser;
use App\Models\Point;

class CouponService
{
    public function getUserCoupons($user_id,$is_used)
    {
        return CouponUser::where("user_id",$user_id)
        ->where("is_used",$is_used)
        ->with(["coupon","coupon.couponType"])->get()->pluck('coupon');
    }

}
