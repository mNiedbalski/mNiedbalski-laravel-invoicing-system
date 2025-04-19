<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Infrastructure\Controllers\InvoiceController;

Route::get('/', [InvoiceController::class, 'showTaskPage'])->name('task-page');

Route::get('/invoices', [InvoiceController::class, 'viewInvoice'])->name('invoices.view')->middleware('web');
Route::post('/invoices/create', [InvoiceController::class, 'createInvoice'])->middleware('web');
Route::post('/invoices/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send')->middleware('web');
