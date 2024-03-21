<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\RankService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private RankService $rankService)
    {
    }
    public function getPurchases(){
        $purchases = Purchase::with(["user","coupon"])->get();
        return $this->successResponse(
            $purchases,
            'dataFetchedSuccessfully'
        );
    }
    public function getUserPurchases(Request $request){
        if ($this->rankService->checkIfUserExists($request->user_id)) {
            return $this->errorResponse("users.NotFound", 400);
        }
        $user_purchases = Purchase::where("user_id",$request->user_id)->get();
        return $this->successResponse(
            $user_purchases,
            'dataFetchedSuccessfully'
        );
    }
}
