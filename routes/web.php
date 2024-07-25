<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('application');
})->where('any', '^(?!api|web).*$');

// Generate invoice
Route::get('/web/invoices/preview', function () {
    return view('pdf.invoice-preview');
});

Route::get('/web/invoices/stream/{hash}', [InvoiceController::class, 'receipt']);
