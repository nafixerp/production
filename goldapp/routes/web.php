<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DaybookController;
use App\Http\Controllers\AccountController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::resource('sales', SalesInvoiceController::class);
    Route::resource('purchase', PurchaseInvoiceController::class);
    Route::resource('receipts', ReceiptController::class);
    Route::resource('payments', PaymentController::class);

    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    Route::get('daybook', [DaybookController::class, 'index'])->name('daybook.index');
    Route::get('ledger', [DaybookController::class, 'ledger'])->name('daybook.ledger');
    Route::get('trial-balance', [DaybookController::class, 'trialBalance'])->name('daybook.trial');
});
