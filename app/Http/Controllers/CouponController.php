<?php

namespace App\Http\Controllers;

use App\Enums\CouponTypes;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\CouponsPriceRequest;
use App\Http\Requests\CouponTypeRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CouponTypeResource;
use App\Models\Coupon;
use App\Models\CouponPrice;
use App\Models\CouponType;
use App\Models\CouponUser;
use App\Models\Purchase;
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
        $coupons = CouponResource::collection(Coupon::all());
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

        $user_coupons = $this->couponService->getUserCoupons($request->user_id, false);
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

        $user_coupons = $this->couponService->getUserCoupons($request->user_id, true);
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

        $user_coupons = CouponResource::collection($this->couponService->getUserExpiredCoupons($request->user_id));

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }
    public function getFixedValueCoupons()
    {
        $user_coupons = CouponResource::collection($this->couponService->get_Coupons_By_Type(CouponTypes::FIXED_VALUE));

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function getPercentageCoupons()
    {
        $user_coupons = CouponResource::collection($this->couponService->get_Coupons_By_Type(CouponTypes::PERCENTAGE));

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }
    public function getDeliveryCoupons()
    {
        $user_coupons = CouponResource::collection($this->couponService->get_Coupons_By_Type(CouponTypes::FREE_DELIVERY));

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

        $user_total_points = $this->pointService->getUserValidPointsSum($validatedData["user_id"]);

        // check if the coupon has a price
        if (!isset($coupon->price)) {
            return $this->errorResponse("coupons.HasNOPrice", 400);
        }

        $coupon_price = $coupon->price->coupon_price;
        // check if the coupon price is greater than user's points
        if ($user_total_points < $coupon_price) {
            return $this->errorResponse("coupons.NoEnoughPoints", 400);
        }

        // Remove user's valid points
        $user_valid_points = $this->pointService->getUserValidPoints($validatedData["user_id"]);

        foreach ($user_valid_points as $point) {
            if ($coupon_price >= ($point->points - $point->used_points)) {
                $point->used_at = Carbon::now();
                $coupon_price = $coupon_price - ($point->points - $point->used_points);
                $point->used_points = $point->points;
                $point->save();

            } else {
                $point->used_points += $coupon_price;
                $coupon_price = 0;
                $point->save();
                break;
            }
        }
        //add the purchase to the user
        Purchase::create([
            "user_id"  => $validatedData["user_id"],
            "coupon_id" => $validatedData["coupon_id"],
            "points" =>  $coupon->price->coupon_price
        ]);

        // Add the coupon to the user
        $coupon = CouponUser::create([
            "user_id"  => $validatedData["user_id"],
            "coupon_id" => $validatedData["coupon_id"],
            "coupon_code" => CouponUser::generateCode(),
            "used_at"  => null,
            "expire_at" => Carbon::now()->addDays(90)
        ]);

        return $this->successResponse(
            $coupon,
            'dataAddedSuccessfully'
        );
    }

    public function useCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        $coupon_user = CouponUser::where("coupon_code", $validatedData["coupon_code"])
            ->where("user_id", $validatedData["user_id"])
            ->with(["coupon", "coupon.couponType"])
            ->get()
            ->first();

        // check if user has this coupon
        if (!isset($coupon_user)) {
            return $this->errorResponse("coupons.YouDontHaveThisCoupon", 400);
        }
        // check if the coupon is used
        if ($coupon_user->used_at != null) {
            return $this->errorResponse("coupons.CouponUsed", 400);
        }
        // check if the coupon is expired
        if ($coupon_user->expire_at < Carbon::now()) {
            return $this->errorResponse("coupons.CouponExpired", 400);
        }

        // get coupon value and coupon type
        $coupon_value = $coupon_user->coupon->value;
        $CouponType = CouponTypes::getName($coupon_user->coupon->couponType->type);

        // make the coupon used
        $coupon_user->update([
            "used_at" => Carbon::now()
        ]);

        // prepare data
        $data = [
            "coupon_value"  => $coupon_value,
            "CouponType" => $CouponType,
        ];
        return $this->successResponse(
            $data,
            'dataUpdatedSuccessfully'
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
        $coupon_types = CouponTypeResource::collection(CouponType::all());
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

        $coupon_type = new CouponTypeResource(CouponType::find($request->type_id));
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

        $coupons = CouponResource::collection(Coupon::where("coupon_type_id", $request->type_id)->get());
        return $this->successResponse(
            $coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function addcouponsType(CouponTypeRequest $request)
    {
        $fileNameToStore = $request->file('image')->hashName();
        $request->file('image')->storeAs('public/images/couponsType',$fileNameToStore);

        $couponstype = CouponType::create([
            'name' => $request->name,
            'image' =>  $fileNameToStore,
            'type' => $request->type,
        ]);

        return $this->successResponse(
            new CouponTypeResource  ($couponstype),
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
