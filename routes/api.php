<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayTechController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
  //  return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('posts', PostController::class);

//Routes pour l'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pay', [PaymentController::class, 'pay']);
    Route::post('/paytech/callback', [PaymentController::class, 'paytechCallback'])->name('paytech.callback');
    Route::get('/transactions', [PaymentController::class, 'listTransactions']);
});

