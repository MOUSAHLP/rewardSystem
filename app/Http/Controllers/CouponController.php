<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponPrice;
use App\Models\CouponType;
use App\Models\CouponUser;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(private CouponService $couponService)
    {
    }

    public function getAllCoupons()
    {
        $coupons = Coupon::with("couponType")->get();
        return $this->successResponse(
            $coupons,
            'dataFetchedSuccessfully'
        );
    }
    public function getUserCoupons(Request $request)
    {
        $user_coupons = $this->couponService->getUserCoupons($request->user_id, 0);
        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserUsedCoupons(Request $request)
    {
        $user_coupons = $this->couponService->getUserCoupons($request->user_id, 1);
        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserExpiredCoupons(Request $request)
    {
        $user_coupons = $this->couponService->getUserCoupons($request->user_id, 0);
        CouponUser::where("user_id", $request->user_id)
            ->where("is_used", 0)
            ->with(["coupon" => function ($query) use ($request) {
                $query ->whereDate('expire_at', '<', Carbon::now());
            }, "coupon.couponType"])
            ->get()->pluck('coupon');

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }
    public function getCouponsPrices()
    {
        $coupon_prices = CouponPrice::all();
        return $this->successResponse(
            $coupon_prices,
            'dataFetchedSuccessfully'
        );
    }

    public function getCouponsTypes()
    {
        $coupon_types = CouponType::all();
        return $this->successResponse(
            $coupon_types,
            'dataFetchedSuccessfully'
        );
    }
    public function getCouponType(Request $request)
    {
        $coupon_type = CouponType::find($request->type_id);
        return $this->successResponse(
            $coupon_type,
            'dataFetchedSuccessfully'
        );
    }

    public function getTypeCoupons(Request $request)
    {
        $coupons = Coupon::where("coupon_type_id", $request->type_id)->get();
        return $this->successResponse(
            $coupons,
            'dataFetchedSuccessfully'
        );
    }
}
