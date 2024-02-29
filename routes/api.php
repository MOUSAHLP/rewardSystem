<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\RankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============== get token ============== //
Route::get('/get-token', function(Request $request){
    if(Auth::attempt(["name"=>$request->name,"password"=>$request->password])){
        $user = Auth::user();
        return $user->createToken("MyApp")->accessToken;
    }
});

// Route::group([
//     "middleware"=>["auth:api","lang"]
// ], function () {

// ============== points ============== //
Route::group([
    'controller' => PointsController::class,
    'prefix' => "points",
], function () {
    Route::get('user-statistics/{user_id}', 'getUserStatistics');
    Route::get('user-total/{user_id}', 'getUserTotalPoints');

    Route::get('user/{user_id}', 'getUserPoints');
    Route::get('valid/user/{user_id}', 'getUserValidPoints');
    Route::get('expired/user/{user_id}', 'getUserExpiredPoints');
    Route::get('used/user/{user_id}', 'getUserUsedPoints');

    Route::get('/value', 'getPointsValue');
    Route::post('/set-value', 'setPointsValue');
});

// ============== achievements ============== //
Route::group([
    'controller' => AchievementController::class,
    'prefix' => "achievements",
], function () {
    Route::get('/', 'getAllAchievements');
    Route::get('done/user/{user_id}', 'getUserDoneAchievements');
    Route::get('not-done/user/{user_id}', 'getUserNotDoneAchievements');

    Route::post('add-achievement', 'addAchievement');
    Route::delete('delete-achievement', 'deleteAchievement');
});

// ============== coupons ============== //
Route::group([
    'controller' => CouponController::class,
    'prefix' => "coupons",
], function () {
    Route::get('/', 'getAllCoupons');
    Route::get('user/{user_id}', 'getUserCoupons');
    Route::get('used/user/{user_id}', 'getUserUsedCoupons');
    Route::get('expired/user/{user_id}', 'getUserExpiredCoupons');

    Route::post('/add', 'addCoupon');
    Route::put('/update', 'updateCoupon');
    Route::delete('/delete', 'deleteCoupon');

    Route::post('/buy', 'buyCoupon');

    // Coupons Prices
    Route::get('/prices', 'getCouponsPrices');
    Route::post('/add-price', 'addCouponsPrice');
    Route::put('/update-price', 'updateCouponsPrice');
    Route::delete('/delete-price', 'deleteCouponsPrice');

    // Coupons Types
    Route::group([
        'prefix' => "types",
    ], function () {
        Route::get('/', 'getCouponsTypes');
        Route::get('/{type_id}', 'getCouponType');
        Route::get('/coupons/{type_id}', 'getTypeCoupons');

        Route::post('/add-couponsType', 'addcouponsType');
        Route::post('/update-couponsType', 'updatecouponsType');
        Route::delete('/delete-couponsType', 'deletecouponsType');

    });

});

// ============== ranks ============== //
Route::group([
    'controller' => RankController::class,
    'prefix' => "ranks",
], function () {
    Route::get('/', 'getRanks');
    Route::get('/{rank_id}', 'getRank');
    Route::get('/{rank_id}/users', 'getRankUsers');
    Route::get('/current_rank/{user_id}', 'getUserCurrentRank');
    Route::get('/next_rank/{user_id}', 'getUserNextRank');

    Route::post('/add_rank', 'addRank');
    Route::put('/update_rank', 'updateRank');
    Route::delete('/delete_rank', 'deleteRank');

});

// });
