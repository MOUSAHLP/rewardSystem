<?php

namespace App\Http\Controllers;

use App\Enums\CouponTypes;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\CouponTypeRequest;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CouponTypeResource;
use App\Models\Coupon;
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
        $user_coupons = $this->couponService->getUserExpiredCoupons($request->user_id);
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
        $user_coupons = CouponResource::collection($this->couponService->get_Coupons_By_Type(CouponTypes::DELIVERY));

        return $this->successResponse(
            $user_coupons,
            'dataFetchedSuccessfully'
        );
    }

    public function addCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        $Coupon = $this->couponService->createCoupon($validatedData);

        return $this->successResponse(
            $Coupon,
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
    public function BulkDeleteCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();
        foreach ($validatedData["coupon_ids"] as $coupon_id) {
            Coupon::where("id", $coupon_id)->delete();
        }
        return $this->successResponse(
            [],
            'dataDeletedSuccessfully'
        );
    }

    public function canBuyCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();

        $user_total_points = $this->pointService->getUserValidPointsSum($validatedData["user_id"]);
        [$can_use, $message] = $this->couponService->checkIfUserCanBuyCoupon($request, $user_total_points);
        if (!$can_use) {
            return $this->errorResponse($message, 400);
        }
        return $this->successResponse(
            $can_use,
            $message
        );
    }
    public function buyCoupon(CouponRequest $request)
    {
        $validatedData = $request->validated();

        $coupon = Coupon::where("id", $validatedData["coupon_id"])->get()->first();

        $user_total_points = $this->pointService->getUserValidPointsSum($validatedData["user_id"]);
        $coupon_price = $coupon->price;

        //check
        [$can_use, $message] = $this->couponService->checkIfUserCanBuyCoupon($request, $user_total_points);
        if (!$can_use) {
            return $this->errorResponse($message, 400);
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
            "points" =>  $coupon->price
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
            'dataFetchedSuccessfully'
        );
    }
    public function canUseCoupon(CouponRequest $request)
    {
        [$can_use, $message] = $this->couponService->checkIfUserCanUseCoupon($request);
        if (!$can_use) {
            return $this->errorResponse($message, 400);
        }
        return $this->successResponse(
            $can_use,
            $message
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

        //check
        [$can_use, $message] = $this->couponService->checkIfUserCanUseCoupon($request);
        if (!$can_use) {
            return $this->errorResponse($message, 400);
        }

        // make the coupon used
        $coupon_user->update([
            "used_at" => Carbon::now()
        ]);

        // prepare data
        $data = [
            "coupon"  => new CouponResource($coupon_user->coupon),
        ];

        return $this->successResponse(
            $data,
            'dataUpdatedSuccessfully'
        );
    }
    public function buyAndUseCoupon(CouponRequest $request)
    {
        $buy_coupon = $this->buyCoupon($request)->getData();
        if ($buy_coupon->statusCode >= 400) {
            return $this->errorResponse($buy_coupon->message, 400);
        }
        $coupon_user = CouponUser::where("id", $buy_coupon->data->id)
            ->with(["coupon"])
            ->get()
            ->first();

        // make the coupon used
        $coupon_user->update([
            "used_at" => Carbon::now()
        ]);

        // prepare data
        $data = [
            "coupon"  => new CouponResource($coupon_user->coupon),
        ];

        return $this->successResponse(
            $data,
            'dataUpdatedSuccessfully'
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
        $request->file('image')->storeAs('public/images/couponsType', $fileNameToStore);

        $couponstype = CouponType::create([
            'name' => $request->name,
            'image' =>  $fileNameToStore,
            'type' => $request->type,
        ]);

        return $this->successResponse(
            new CouponTypeResource($couponstype),
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
