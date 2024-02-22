<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\RankController;
use App\Models\Achievement;
use App\Models\User;
use App\Models\AchievementUser;
use Illuminate\Support\Facades\Route;


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

    Route::get('/prices', 'getCouponsPrices');

    Route::group([
        'prefix' => "types",
    ], function () {
        Route::get('/', 'getCouponsTypes');
        Route::get('/{type_id}', 'getCouponType');
        Route::get('/coupons/{type_id}', 'getTypeCoupons');
    });

});

// ============== ranks ============== //
Route::group([
    'controller' => RankController::class,
    'prefix' => "ranks",
], function () {
    Route::get('/', 'getRanks');
    Route::get('/current_rank/{user_id}', 'getUserCurrentRank');
    Route::get('/next_rank/{user_id}', 'getUserNextRank');

    // Route::post('/add_rank', 'addRank');

});
