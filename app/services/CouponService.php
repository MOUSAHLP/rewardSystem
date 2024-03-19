<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponType;
use App\Models\CouponUser;
use App\Models\Point;
use App\Models\User;
use Carbon\Carbon;

class CouponService
{
    public function getUserCoupons($user_id, $is_used)
    {
        if ($is_used) {
            return CouponUser::where("user_id", $user_id)
                ->where("used_at", null)
                ->with(["coupon", "coupon.couponType", "coupon.price"])->get()->map(function ($model) {
                    $model->coupon->expire_at = $model->expire_at;
                    if ($model->coupon->price != null) {
                        $price = $model->coupon->price->coupon_price;
                        $model->coupon->unsetRelation('price');
                        $model->coupon->price = $price;
                    }
                    return $model->coupon;
                });
        } else {
            return CouponUser::where("user_id", $user_id)
                ->where("used_at", "!=", null)
                ->with(["coupon", "coupon.couponType", "coupon.price"])->get()->map(function ($model) {
                    $model->coupon->expire_at = $model->expire_at;
                    if ($model->coupon->price != null) {
                        $price = $model->coupon->price->coupon_price;
                        $model->coupon->unsetRelation('price');
                        $model->coupon->price = $price;
                    }
                    return $model->coupon;
                });
        }
    }

    public static function getUserExpiredCoupons($user_id)
    {
        return CouponUser::where("user_id", $user_id)
            ->with(["coupon", "coupon.couponType"])
            ->where("used_at", null)
            ->whereDate('expire_at', '<', Carbon::now())
            ->get()->pluck('coupon');
    }
    public static function get_FixedValue_percentage_Coupons($is_percentage)
    {
        return Coupon::with(["price", "couponType"])
            ->get()
            ->map(function ($model) {
                if ($model->price != null) {
                    $price = $model->price->coupon_price;
                    $model->unsetRelation('price');
                    $model->price = $price;
                }
                return $model;
            })
            ->filter(function ($model) use($is_percentage){
                if($is_percentage){
                    return $model->couponType->is_percentage;
                }
                return !$model->couponType->is_percentage;
            });
    }

    public static function createCoupon($data)
    {
        $coupon = new Coupon();
        $coupon->coupon_type_id = $data["coupon_type_id"];
        $coupon->value = $data["value"];
        $coupon->description = $data["description"];
        $coupon->created_at = Carbon::now();

        $coupon->save();
        return $coupon;
    }

    public static function checkIfUserExists($user_id)
    {
        return User::where("id", $user_id)->count() == 0;
    }

    public static function checkIfCouponTypeExists($type_id)
    {
        return CouponType::where("id", $type_id)->count() == 0;
    }

    public function getImagePath($value)
    {
        return env('APP_URL') . '/storage/images/couponsType/' . $value;
    }
}
