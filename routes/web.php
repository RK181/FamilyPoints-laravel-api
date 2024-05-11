<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// AUTH
Route::get('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
->middleware(['signed'])->name('verification.verify')->whereNumber('id');

// GROUP
Route::get('/group/invitation/{id}/{token}', [GroupController::class, 'acceptInvitation'])
->middleware(['signed'])->name('invitation.accept')->whereNumber('id');