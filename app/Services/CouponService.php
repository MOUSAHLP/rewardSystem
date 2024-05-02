<?php

namespace App\Services;

use App\Enums\CouponResources;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
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
                ->with(["coupon", "coupon.couponType"])->get()
                ->map(function ($model) {
                    return (new CouponResource($model->coupon))->usedCoupon($model);
                });
        } else {
            return CouponUser::where("user_id", $user_id)
                ->where("used_at", null)
                ->with(["coupon", "coupon.couponType"])->get()
                ->map(function ($model) {
                    return (new CouponResource($model->coupon))->notUsedCoupon($model);
                });
        }
    }

    public static function getUserExpiredCoupons($user_id)
    {
        return CouponUser::where("user_id", $user_id)
            ->with(["coupon", "coupon.couponType"])
            ->where("used_at", null)
            ->whereDate('expire_at', '<', Carbon::now())
            ->get()
            ->map(function ($model) {
                return (new CouponResource($model->coupon))->notUsedCoupon($model);
            });
    }
    public static function get_Coupons_By_Type($coupon_type)
    {
        return Coupon::with(["couponType"])
            ->get()
            ->filter(function ($model) use ($coupon_type) {
                if ($model->couponType != null && $coupon_type == $model->couponType->type) {
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
        $coupon->price = $data["price"];
        $coupon->description = $data["description"];
        $coupon->created_at = Carbon::now();

        $coupon->save();
        return new CouponResource($coupon);
    }

    public static function checkIfUserCanBuyCoupon($request, $user_total_points)
    {
        $validatedData = $request->validated();

        $coupon = Coupon::where("id", $validatedData["coupon_id"])->get()->first();

        // check if the coupon price is greater than user's points
        if ($user_total_points < $coupon->price) {
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

    public static function createUserCoupon($data,$type=CouponResources::PURCHASED)
    {
     return CouponUser::create([
            "user_id"  => $data["user_id"],
            "coupon_id" => $data["coupon_id"],
            "coupon_code" => CouponUser::generateCode(),
            "coupon_resource" =>$type,
            "used_at"  => null,
            "expire_at" => Carbon::now()->addDays(90)
        ]);
    }

    public static function couponsReport()
    {
        $coupons = Coupon::withCount("purchases")->get();
        return  CouponResource::collection($coupons);
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
