<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('application');
})->where('any', '^(?!api|test).*$');

// Generate invoice
Route::get('/test/invoices/preview', function () {
    return view('pdf.invoice-preview');
});
