<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayTechController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Container\Attributes\Auth;
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

//midlleware pour les transactions
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pay', [PaymentController::class, 'pay']);
    Route::post('/paytech/callback', [PaymentController::class, 'paytechCallback'])->name('paytech.callback');
    Route::get('/transactions', [PaymentController::class, 'listTransactions']);
});

// middleware for user;
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/users', [AuthController::class,'listusers']);
    Route::get('/users', [AuthController::class,'listusers'])->name('users');
    Route::get('/user/{id}', [AuthController::class,'listuser']);
    Route::put('/user/{id}', [AuthController::class,'updateUser']);
    Route::get('/demandes', [DemandeController::class, 'listDemandes']);
    Route::post('/demander', [DemandeController::class, 'store']);
    Route::get('/demande/{id}', [DemandeController::class, 'getdemande']);
    Route::put('/demander/{id}', [DemandeController::class, 'update']);
    Route::delete('/demande/{id}', [DemandeController::class, 'delete']);
    Route::get('/user', function () {
        // dd( Auth::user());
      //  return response()->json(Auth::user());
    })->middleware('Cors');

    Route::get('/profil', function () {
//return Auth::user()->profils_id;
    });

});

Route::get('/profile', function () {
    // ...
})->middleware(EnsureTokenIsValid::class);
// Route::post('/tokens/create', function (Request $request) {
//     $token = $request->user()->createToken($request->token_name);

//     return ['token' => $token->plainTextToken];
// });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//Creation de route pour Don//
Route::get('/dons', [DonController::class, 'listdons']);
Route::get('/don/{id}', [DonController::class, 'getdon']);
Route::post('/donner', [DonController::class, 'store']);
Route::put('/don/{id}', [DonController::class, 'update']);
Route::delete('/don/{id}', [DonController::class, 'delete']);
