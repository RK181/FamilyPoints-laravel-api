<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RewardController;
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

// AUTH
Route::post('/auth/signup', [AuthController::class, 'signUp']);
Route::post('/auth/login', [AuthController::class, 'logIn']);

// PROTECTED
Route::group(['middleware' => ['auth:sanctum']], function () {
    // AUTH
    Route::post('/auth/logout', [AuthController::class, 'logOut']);

    //Route::resource('group', GroupController::class);
    Route::post('/group/create', [GroupController::class, 'createGroup']);
    Route::put('/group/update', [GroupController::class, 'updateGroup']);
    Route::delete('/group/delete', [GroupController::class, 'deleteGroup']);
    Route::get('/group', [GroupController::class, 'getGroup']);


    Route::get('/group/reward/list', [RewardController::class, 'getGroupRewardList']);
    Route::post('/reward/create', [RewardController::class, 'createReward']);
    Route::put('/reward/update', [RewardController::class, 'updateReward']);
    Route::get('/reward/{id}', [RewardController::class, 'getRewardById'])->whereNumber('id');
    Route::delete('/reward/delete/{id}', [RewardController::class, 'deleteReward'])->whereNumber('id');

});

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        //'device_name' => 'required',
    ]);
 
    $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
 
    return $user->createToken("asd")->plainTextToken;
});*/