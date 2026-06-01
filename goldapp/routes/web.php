<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SalesBillController;
use App\Http\Controllers\PurchaseBillController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DaybookController;
use App\Http\Controllers\ReportController;

// Auth
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Accounts
    Route::resource('accounts', AccountController::class);
    Route::get('/accounts-search', [AccountController::class, 'search'])->name('accounts.search');

    // Sales
    Route::resource('sales', SalesBillController::class)->except(['edit','update']);

    // Purchase
    Route::resource('purchase', PurchaseBillController::class)->except(['edit','update']);

    // Receipt
    Route::resource('receipt', ReceiptController::class)->except(['edit','update']);

    // Payment
    Route::resource('payment', PaymentController::class)->except(['edit','update']);

    // Daybook
    Route::get('/daybook', [DaybookController::class, 'index'])->name('daybook.index');

    // Reports
    Route::get('/reports/ledger',        [ReportController::class, 'ledger'])->name('reports.ledger');
    Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trial_balance');
});
