<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Infrastructure\Controllers\InvoiceController;

Route::get('/', [InvoiceController::class, 'showTaskPage'])->name('task-page');

Route::post('/invoices/create', [InvoiceController::class, 'createInvoice'])->middleware('web');
Route::post('/invoices/send', [InvoiceController::class, 'sendInvoice'])->middleware('web');
