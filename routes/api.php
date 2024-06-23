<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyCompanyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientUserController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\InvoiceEmailLogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionItemController;

Route::post('login', 'AuthController@login');

//// MyCompanies Routes
//Route::apiResource('my-companies', MyCompanyController::class);
//
//// Clients Routes
//Route::apiResource('clients', ClientController::class);
//
//// Client Users Routes
//Route::apiResource('client-users', ClientUserController::class);
//
//// Credit Cards Routes
//Route::apiResource('credit-cards', CreditCardController::class);
//
//// Invoices Routes
//Route::apiResource('invoices', InvoiceController::class);
//
//// Invoice Items Routes
//Route::apiResource('invoice-items', InvoiceItemController::class);
//
//// Invoice Payments Routes
//Route::apiResource('invoice-payments', InvoicePaymentController::class);
//
//// Email Templates Routes
//Route::apiResource('email-templates', EmailTemplateController::class);
//
//// Invoice Email Logs Routes
//Route::apiResource('invoice-email-logs', InvoiceEmailLogController::class);
//
//// Files Routes
//Route::apiResource('files', FileController::class);
//
//// Subscriptions Routes
//Route::apiResource('subscriptions', SubscriptionController::class);
//
//// Subscription Items Routes
//Route::apiResource('subscription-items', SubscriptionItemController::class);
