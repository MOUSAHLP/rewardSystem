<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\RankController;
use App\Models\Achievement;
use App\Models\User;
use App\Models\AchievementUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

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
        Route::get('', 'getCouponsTypes');
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
});

Route::get("/",function () {
    $achievement = Achievement::all()->random();
    $user = User::all()->random();
    $achievementsdone = AchievementUser::where("achievement_id" , $achievement->id)->where("user_id",$user->id)->count();
    // $achievementsdone = AchievementUser::where("achievement_id" ,7)->where("user_id",4)->count();
    return "achievement ". $achievement->id." <br> user ". $user->id." <br> "."achievementsdone ". $achievementsdone ." <br> ". $achievement->segments;
});

