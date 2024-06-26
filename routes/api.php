<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// // ============== get token ============== //
// Route::get('/get-token', function(Request $request){
//     if(Auth::attempt(["name"=>$request->name,"password"=>$request->password])){
//         $user = Auth::user();
//         return $user->createToken("MyApp")->accessToken;
//     }
// });

Route::group([
    "middleware" => ["check", "lang"]
], function () {

    // ============== Points ============== //
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

        Route::get('/used-report', 'usedPointsReport');

        Route::post('/add-points', 'addPointsToUser');

        Route::get('/value', 'getPointsValue');
        Route::post('/set-value', 'setPointsValue');
    });

    // ============== achievements ============== //
    Route::group([
        'controller' => AchievementController::class,
        'prefix' => "achievements",
    ], function () {
        Route::get('/', 'getAllAchievements');
        Route::get('user/{user_id}', 'getUserAchievements');
        Route::get('done/user/{user_id}', 'getUserDoneAchievements');
        Route::get('not-done/user/{user_id}', 'getUserNotDoneAchievements');

        Route::post('add-achievement', 'addAchievement');

        Route::post('add', 'add');
        Route::delete('delete', 'delete');
        Route::put('update', 'update');
        Route::delete('delete-achievement', 'deleteAchievement');
    });

    // ============== Coupons ============== //
    Route::group([
        'controller' => CouponController::class,
        'prefix' => "coupons",
    ], function () {
        Route::get('/', 'getAllCoupons');
        Route::get('user/{user_id}', 'getUserCoupons');
        Route::get('used/user/{user_id}', 'getUserUsedCoupons');
        Route::get('expired/user/{user_id}', 'getUserExpiredCoupons');

        Route::get('fixed_value', 'getFixedValueCoupons');
        Route::get('percentage', 'getPercentageCoupons');
        Route::get('delivery', 'getDeliveryCoupons');

        Route::post('/add', 'addCoupon');
        Route::put('/update', 'updateCoupon');
        Route::delete('/delete', 'deleteCoupon');
        Route::delete('/bulk-delete', 'BulkDeleteCoupon');

        // to check if user can buy the given coupon
        Route::post('/canBuy', 'canBuyCoupon');

        // to buy coupon
        Route::post('/buy', 'buyCoupon');

        // to check if user can use the given coupon
        Route::post('/canUse', 'canUseCoupon');

        // to use coupon
        Route::post('/use', 'useCoupon');

        // to buy and use coupon insantly
        Route::post('/buy-and-use', 'buyAndUseCoupon');

        // to compensate users with coupons
        Route::post('/compensate', 'compensateUserCoupon');

        // to give users coupons periodicly based on thier rank
        Route::post('/give-periodic-coupons', 'givePeriodicCoupons');

        // Coupons Report About The Most Used Coupons
        Route::get('/report', 'couponsReport');

        // Coupons Report Based On Resource
        Route::get('/purchased-report', 'purchasedCouponsReport');
        Route::get('/compensation-report', 'compensationCouponsReport');
        Route::get('/periodic-report', 'periodicCouponsReport');

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

    // ============== Purchases ============== //
    Route::group([
        'controller' => PurchaseController::class,
        'prefix' => "purchases",
    ], function () {
        Route::get('/', 'getPurchases');
        Route::get('/user/{user_id}', 'getUserPurchases');
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
});
