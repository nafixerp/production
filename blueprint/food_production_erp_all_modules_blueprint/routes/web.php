<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/dashboard'));
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

Route::prefix('masters')->group(function () {
    Route::resource('raw-materials', App\Http\Controllers\RawMaterialMasterController::class);
    Route::resource('finished-goods', App\Http\Controllers\FinishedGoodsMasterController::class);
    Route::resource('recipes', App\Http\Controllers\RecipeBOMMasterController::class);
    Route::resource('suppliers', App\Http\Controllers\VendorSupplierMasterController::class);
    Route::resource('customers', App\Http\Controllers\CustomerMasterController::class);
});

Route::prefix('purchase')->group(function () {
    Route::resource('orders', App\Http\Controllers\PurchaseOrderController::class);
    Route::resource('grn', App\Http\Controllers\GoodsReceiptNoteController::class);
    Route::resource('invoices', App\Http\Controllers\PurchaseInvoiceController::class);
});

Route::prefix('production')->group(function () {
    Route::resource('plans', App\Http\Controllers\ProductionPlanningController::class);
    Route::resource('orders', App\Http\Controllers\ProductionOrderController::class);
    Route::resource('material-issue', App\Http\Controllers\MaterialIssueToProductionController::class);
    Route::resource('fg-receipt', App\Http\Controllers\FinishedGoodsReceiptController::class);
});

Route::prefix('accounts')->group(function () {
    Route::resource('receipts', App\Http\Controllers\ReceiptVoucherController::class);
    Route::resource('payments', App\Http\Controllers\PaymentVoucherController::class);
    Route::resource('journals', App\Http\Controllers\JournalVoucherController::class);
});
