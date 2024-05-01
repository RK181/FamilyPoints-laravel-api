<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\TaskController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
 
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

/*
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
->middleware(['signed'])->name('verification.verify')->whereNumber('id');
*/

//

// AUTH
Route::post('/auth/signup', [AuthController::class, 'signUp']);
Route::post('/auth/login', [AuthController::class, 'logIn']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
->middleware(['signed'])->name('verification.verify')->whereNumber('id');

// PROTECTED
Route::group(['middleware' => ['auth:sanctum']], function () {
    // AUTH
    Route::post('/auth/logout', [AuthController::class, 'logOut']);
    Route::get('/email/resend', [AuthController::class, 'resendVerifyEmail'])->name('verification.resend');

    Route::post('/group/create', [GroupController::class, 'createGroup']);

    //Route::resource('group', GroupController::class);
    Route::group(['middleware' => ['groupExist']], function () {
        
        Route::put('/group/update', [GroupController::class, 'updateGroup']);
        Route::delete('/group/delete', [GroupController::class, 'deleteGroup']);
        Route::get('/group', [GroupController::class, 'getGroup']);
        
        //Route::group(['middleware' => ['rewardInGroupExist']], function () {

            Route::patch('/reward/redeem/{id}', [RewardController::class, 'updateRewardRedeem'])->whereNumber('id');
            Route::patch('/reward/validate/{id}', [RewardController::class, 'updateRewardValidate'])->whereNumber('id');

            Route::get('/group/reward/list', [RewardController::class, 'getGroupRewardList']);
            Route::post('/reward/create', [RewardController::class, 'createReward']);
            Route::put('/reward/update', [RewardController::class, 'updateReward']);
            Route::get('/reward/{id}', [RewardController::class, 'getRewardById'])->whereNumber('id');
            Route::delete('/reward/delete/{id}', [RewardController::class, 'deleteReward'])->whereNumber('id');
        //});


        Route::patch('/task/approve/{id}', [TaskController::class, 'updateTaskCreationApprove'])->whereNumber('id');
        Route::patch('/task/complete/{id}', [TaskController::class, 'updateTaskComplete'])->whereNumber('id');
        Route::patch('/task/validate/{id}', [TaskController::class, 'updateTaskCompletionValidation'])->whereNumber('id');
        Route::patch('/task/invalidate/{id}', [TaskController::class, 'updateTaskCompletionInValidation'])->whereNumber('id');


        Route::post('/task/create', [TaskController::class, 'createTask']);
        Route::put('/task/update', [TaskController::class, 'updateTask']);
        Route::get('/task/{id}', [TaskController::class, 'getTaskById'])->whereNumber('id');
        Route::delete('/task/delete/{id}', [TaskController::class, 'deleteTask'])->whereNumber('id');
        Route::get('/group/task/list', [TaskController::class, 'getGroupTaskList']);

    });
});



