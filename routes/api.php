<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SubscriptionController;
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
    Route::get('/clients/search', [ClientController::class, 'search']);
    Route::get('/clients/min', [ClientController::class, 'indexMin']);
    Route::post('/clients/store', [ClientController::class, 'store']);
    Route::get('/clients/show/{hash}', [ClientController::class, 'show']);
    Route::get('/clients/{hash}/users', [ClientController::class, 'users']);
    Route::post('/clients/update/{hash}', [ClientController::class, 'update']);
    Route::delete('/clients/destroy/{hash}', [ClientController::class, 'destroy']);

    Route::get('/clients/credit-cards/{hash}', [CreditCardController::class, 'index']);
    Route::post('/clients/credit-cards/store', [CreditCardController::class, 'store']);

    Route::get('/clients/{client_hash}/users', [ClientUserController::class, 'index']);
    Route::post('/clients/{client_hash}/user/store', [ClientUserController::class, 'store']);
    Route::put('/clients/{client_hash}/user/{client_user_hash}/update', [ClientUserController::class, 'update']);
    Route::get('/clients/user/{client_user_hash}/access', [ClientUserController::class, 'access']);

    Route::get('/companies', [CompanyController::class, 'index']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/search', [InvoiceController::class, 'search']);
    Route::post('/invoices/store', [InvoiceController::class, 'store']);
    Route::get('/invoices/show/{hash}', [InvoiceController::class, 'show']);
    Route::get('/invoices/receipt/{hash}/download', [InvoiceController::class, 'receipt']);
    Route::post('/invoices/update/{hash}', [InvoiceController::class, 'update']);
    Route::delete('/invoices/destroy/{hash}', [InvoiceController::class, 'destroy']);

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::get('/subscriptions/search', [SubscriptionController::class, 'search']);
    Route::post('/subscriptions/store', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions/show/{hash}', [SubscriptionController::class, 'show']);
    Route::post('/subscriptions/update/{hash}', [SubscriptionController::class, 'update']);
    Route::delete('/subscriptions/destroy/{hash}', [SubscriptionController::class, 'destroy']);

    Route::get('/logout', [LoginController::class, 'logout']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!']);
});
