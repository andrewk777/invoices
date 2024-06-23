<?php

use App\Http\Controllers\Auth\LoginController;
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

    // Learning Logout
//    Route::get('/learning/logout', [LearningLoginController::class, 'logout']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!']);
});
