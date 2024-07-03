<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\InvoiceController;
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
    Route::get('/clients/min', [ClientController::class, 'indexMin']);
    Route::post('/clients/store', [ClientController::class, 'store']);
    Route::get('/clients/show/{hash}', [ClientController::class, 'show']);
    Route::post('/clients/update/{hash}', [ClientController::class, 'update']);
    Route::delete('/clients/destroy/{hash}', [ClientController::class, 'destroy']);

    Route::get('/companies', [CompanyController::class, 'index']);

    Route::get('/clients/credit-cards/{hash}', [CreditCardController::class, 'index']);
    Route::post('/clients/credit-cards/store', [CreditCardController::class, 'store']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices/store', [InvoiceController::class, 'store']);
    Route::get('/invoices/show/{hash}', [InvoiceController::class, 'show']);
    Route::post('/invoices/update/{hash}', [InvoiceController::class, 'update']);
    Route::delete('/invoices/destroy/{hash}', [InvoiceController::class, 'destroy']);


    Route::get('/logout', [LoginController::class, 'logout']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!']);
});
