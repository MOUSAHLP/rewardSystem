<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Http\Requests\CouponsPriceRequest;
use App\Http\Requests\CouponTypeRequest;
use App\Models\Coupon;
use App\Models\CouponPrice;
use App\Models\CouponType;
use App\Models\CouponUser;
use App\Services\CouponService;
use App\Services\PointService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(private CouponService $couponService, private PointService $pointService)
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

        if ($this->couponService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_coupons = $this->couponService->getUserCoupons($request->user_id, true);
        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserUsedCoupons(Request $request)
    {
        if ($this->couponService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_coupons = $this->couponService->getUserCoupons($request->user_id, false);
        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function getUserExpiredCoupons(Request $request)
    {
        if ($this->couponService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }

        $user_coupons = $this->couponService->getUserExpiredCoupons($request->user_id);

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }


    public function addCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        $CouponPrice = $this->couponService->createCoupon($validatedData);

        return $this->successResponse(
            $CouponPrice,
            'dataAddedSuccessfully'
        );
    }

    public function updateCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        Coupon::where("id", $validatedData["id"])->update($validatedData);

        return $this->successResponse(
            [],
            'dataUpdatedSuccessfully'
        );
    }

    public function deleteCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        Coupon::where("id", $validatedData["id"])->delete();

        return $this->successResponse(
            [],
            'dataDeletedSuccessfully'
        );
    }

    public function buyCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();

        $coupon = Coupon::where("id", $validatedData["coupon_id"])->with("price")->get()->first();

        $user_total_points = $this->pointService->getUserPointsSum($validatedData["user_id"]);
        $coupon_price = $coupon->price->coupon_price;

        // check if the coupon price is greater than user's points
        if ($user_total_points < $coupon_price) {
            return $this->errorResponse("coupons.NoEnoughPoints", 400);
        }

        // Remove user's valid points
        $user_valid_points = $this->pointService->getUserValidPoints($validatedData["user_id"]);

        foreach($user_valid_points as $point){
            if($coupon_price <= 0)break;

            if($coupon_price >= ($point->points - $point->used_points)){
                $point->used_at = Carbon::now();
                $coupon_price = $coupon_price - ($point->points - $point->used_points);
                $point->used_points = $point->points;
            }else{
                $point->used_points = $coupon_price;
                $coupon_price = 0;
            }
            $point->save();
        }

        // Add the coupon to the user
        $coupon = CouponUser::create([
            "user_id"  => $validatedData["user_id"],
            "coupon_id"=> $validatedData["coupon_id"],
            "used_at"  => null,
            "expire_at"=> Carbon::now()->addDays(90)
        ]);

        return $this->successResponse(
            $coupon,
            'dataAddedSuccessfully'
        );
    }

    // ============== Coupons Prices ============== //
    public function getCouponsPrices()
    {
        $coupon_prices = CouponPrice::all();
        return $this->successResponse(
            $coupon_prices,
            'dataFetchedSuccessfully'
        );
    }

    public function addCouponsPrice(CouponsPriceRequest $request)
    {
        $validatedData = $request->validated();
        $CouponPrice = CouponPrice::create($validatedData);

        return $this->successResponse(
            $CouponPrice,
            'dataAddedSuccessfully'
        );
    }

    public function updateCouponsPrice(CouponsPriceRequest $request)
    {
        $validatedData = $request->validated();
        CouponPrice::where("id", $validatedData["id"])->update($validatedData);

        return $this->successResponse(
            [],
            'dataUpdatedSuccessfully'
        );
    }

    public function deleteCouponsPrice(CouponsPriceRequest $request)
    {
        $validatedData = $request->validated();
        CouponPrice::where("id", $validatedData["id"])->delete();

        return $this->successResponse(
            [],
            'dataDeletedSuccessfully'
        );
    }

    // ============== Coupons Types ============== //
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
        if ($this->couponService->checkIfCouponTypeExists($request->type_id)) {
            return $this->errorResponse("dataNotFound", 400);
        }

        $coupon_type = CouponType::find($request->type_id);
        return $this->successResponse(
            $coupon_type,
            'dataFetchedSuccessfully'
        );
    }

    public function getTypeCoupons(Request $request)
    {
        if ($this->couponService->checkIfCouponTypeExists($request->type_id)) {
            return $this->errorResponse("dataNotFound", 400);
        }

        $coupons = Coupon::where("coupon_type_id", $request->type_id)->get();
        return $this->successResponse(
            $coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function addcouponsType(CouponTypeRequest $request)
    {
        $fileNameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extention = $request->file('image')->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extention;
        $request->file('image')->storeAs('public/images/couponsType', $fileNameToStore);

        $couponstype = CouponType::create([
            'name' => $request->name,
            'image' =>  $fileNameToStore,
            'is_percentage' => $request->is_percentage,
        ]);

        return $this->successResponse(
            $couponstype,
            'dataAddedSuccessfully'
        );
    }

    public function updatecouponsType(CouponTypeRequest $request)
    {
        $validatedData = $request->validated();
        $couponstype = CouponType::where("id", $validatedData["id"]);

        $fileNameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extention = $request->file('image')->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extention;
        $path = $request->file('image')->storeAs('public/images/couponsType', $fileNameToStore);
        $validatedData['image'] = $this->couponService->getImagePath($fileNameToStore);

        $couponstype->update($validatedData);

        return $this->successResponse(
            $validatedData,
            'dataUpdatedSuccessfully'
        );
    }

    public function deletecouponsType(CouponTypeRequest $request)
    {
        $validatedData = $request->validated();
        CouponType::where("id", $validatedData["id"])->delete();

        return $this->successResponse(
            [],
            'dataDeletedSuccessfully'
        );
    }
}
