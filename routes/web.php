<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Infrastructure\Controllers\InvoiceController;

Route::get('/', static function () {
    return view('task-page');
});

Route::post('/invoices/create', [InvoiceController::class, 'createInvoice'])->middleware('web');
