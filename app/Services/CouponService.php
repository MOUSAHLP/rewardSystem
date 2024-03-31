<?php

namespace App\Services;

use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\CouponPrice;
use App\Models\CouponType;
use App\Models\CouponUser;
use App\Models\User;
use Carbon\Carbon;

class CouponService
{
    public function __construct(private PointService $pointService)
    {
    }
    public function getUserCoupons($user_id, $is_used)
    {
        if ($is_used) {
            return CouponUser::where("user_id", $user_id)
                ->where("used_at", "!=", null)
                ->with(["coupon", "coupon.couponType", "coupon.price"])->get()
                ->filter(function ($model) {
                    return $model->coupon->price != null;
                })->values()
                ->map(function ($model) {
                    $model->coupon->setAttribute("coupon_code", $model->coupon_code);
                    $model->coupon->setAttribute("expire_at", $model->expire_at);
                    $model->coupon = new CouponResource($model->coupon);
                    return $model->coupon;
                });
        } else {
            return CouponUser::where("user_id", $user_id)
                ->where("used_at", null)
                ->with(["coupon", "coupon.couponType", "coupon.price"])->get()
                ->filter(function ($model) {
                    return $model->coupon->price != null;
                })->values()
                ->map(function ($model) {
                    $model->coupon->setAttribute("coupon_code", $model->coupon_code);
                    $model->coupon->setAttribute("used_at", $model->used_at);
                    $model->coupon->setAttribute("expire_at", $model->expire_at);
                    $model->coupon = new CouponResource($model->coupon);
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
    public static function get_Coupons_By_Type($coupon_type)
    {
        return Coupon::with(["price", "couponType"])
            ->get()
            ->filter(function ($model) use ($coupon_type) {
                if ($model->couponType != null && $coupon_type == $model->couponType->type && $model->price != null) {
                    return true;
                }
                return false;
            })->values();
    }
    public static function createCoupon($data)
    {
        $coupon = new Coupon();
        $coupon->coupon_type_id = $data["coupon_type_id"];
        $coupon->value = $data["value"];
        $coupon->description = $data["description"];
        $coupon->created_at = Carbon::now();

        $coupon->save();

        $coupon_price = new CouponPrice();
        $coupon_price->coupon_id = $coupon->id;
        $coupon_price->coupon_price = $data["price"];
        $coupon_price->save();

        return new CouponResource($coupon);
    }

    public static function checkIfUserCanBuyCoupon($request, $user_total_points)
    {
        $validatedData = $request->validated();

        $coupon = Coupon::where("id", $validatedData["coupon_id"])->with("price")->get()->first();

        // check if the coupon has a price
        if (!isset($coupon->price)) {
            return [
                false,
                "coupons.HasNOPrice"
            ];
        }

        $coupon_price = $coupon->price->coupon_price;
        // check if the coupon price is greater than user's points
        if ($user_total_points < $coupon_price) {
            return [
                false,
                "coupons.NoEnoughPoints"
            ];
        }

        return   [
            [
                "can_use_coupon" => true,
                "coupon" => new CouponResource($coupon)
            ],
            'dataFetchedSuccessfully'
        ];
    }
    public static function checkIfUserCanUseCoupon($request)
    {
        $validatedData = $request->validated();
        $coupon_user = CouponUser::where("coupon_code", $validatedData["coupon_code"])
            ->where("user_id", $validatedData["user_id"])
            ->with(["coupon", "coupon.couponType"])
            ->get()
            ->first();

        // check if user has this coupon
        if (!isset($coupon_user)) {
            return [
                false,
                "coupons.YouDontHaveThisCoupon"
            ];
        }
        // check if the coupon is used
        if ($coupon_user->used_at != null) {
            return [
                false,
                "coupons.CouponUsed"
            ];
        }
        // check if the coupon is expired
        if ($coupon_user->expire_at < Carbon::now()) {
            return [
                false,
                "coupons.CouponExpired"
            ];
        }

        return [
            [
                "can_use_coupon" => true,
                "coupon" => new CouponResource($coupon_user->coupon)
            ],
            'dataFetchedSuccessfully'
        ];
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
