<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('{any?}', function() {
    return view('application');
})->where('any', '^(?!api).*$');

// Generate invoice
Route::get('/invoices/receipt/{hash}', [InvoiceController::class, 'receipt']);
