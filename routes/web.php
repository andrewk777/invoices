<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;


Route::get('/view-invoice/{hash}', [InvoiceController::class, 'stream']);

Route::get('/{any?}', function () {
    return view('application');
})->where('any', '^(?!api|web).*$');


