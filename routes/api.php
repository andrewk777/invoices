<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'login']);

// Custom sanctum admin guard authentication for Learning Portal
Route::middleware('auth:sanctum')->group(static function (){
    // Learning Dashboard
    Route::get('/authenticate', [LoginController::class, 'authenticate']);

    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/clients/store', [ClientController::class, 'store']);
    Route::get('/clients/show/{hash}', [ClientController::class, 'show']);
    Route::post('/clients/update/{hash}', [ClientController::class, 'update']);
    Route::delete('/clients/destroy/{hash}', [ClientController::class, 'destroy']);

    Route::get('/clients/credit-cards/{hash}', [CreditCardController::class, 'index']);
    Route::post('/clients/credit-cards/store', [CreditCardController::class, 'store']);

    Route::get('/logout', [LoginController::class, 'logout']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!']);
});
