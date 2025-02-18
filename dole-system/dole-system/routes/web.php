<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Root route - show login page
Route::get('/', [LoginController::class, 'showLoginForm'])->middleware('guest');

// Public Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Purchase Requests
    Route::get('/records', [PurchaseRequestController::class, 'index'])->name('records.index');
    Route::get('/records/create', [PurchaseRequestController::class, 'create'])->name('records.create');
    Route::post('/records', [PurchaseRequestController::class, 'store'])->name('records.store');
    Route::get('/records/{record}', [PurchaseRequestController::class, 'show'])->name('records.show');
    Route::get('/records/{record}/edit', [PurchaseRequestController::class, 'edit'])->name('records.edit');
    Route::put('/records/{record}', [PurchaseRequestController::class, 'update'])->name('records.update');
    Route::delete('/records/{record}', [PurchaseRequestController::class, 'destroy'])->name('records.destroy');
    Route::get('/records/{id}/for-payment', [PurchaseRequestController::class, 'markForPayment'])->name('records.for-payment');
    
    // Budget Module
    Route::prefix('budget')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('budget.index');
        Route::get('/acknowledge/{id}', [BudgetController::class, 'acknowledge'])->name('budget.acknowledge');
        Route::post('/store', [BudgetController::class, 'store'])->name('budget.store');
        Route::get('/{budget}', [BudgetController::class, 'show'])->name('budget.show');
    });
    
    // Accounting Module
    Route::prefix('accounting')->group(function () {
        Route::get('/', [AccountingController::class, 'index'])->name('accounting.index');
        Route::get('/acknowledge/{id}', [AccountingController::class, 'acknowledge'])->name('accounting.acknowledge');
        Route::post('/store', [AccountingController::class, 'store'])->name('accounting.store');
        Route::get('/process-dv/{id}', [AccountingController::class, 'processDv'])->name('accounting.process-dv');
        Route::post('/process-dv/{id}', [AccountingController::class, 'storeDv'])->name('accounting.store-dv');
        Route::get('/{accounting}', [AccountingController::class, 'show'])->name('accounting.show');
    });
    
    // DV Processing Routes
    Route::get('/accounting/process-dv/{id}', [AccountingController::class, 'processDV'])->name('accounting.process-dv');
    Route::post('/accounting/store-dv/{id}', [AccountingController::class, 'storeDV'])->name('accounting.store-dv');
    
    // Cashier Module
    Route::prefix('cashier')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
        Route::get('/process/{id}', [CashierController::class, 'process'])->name('cashier.process');
        Route::post('/process/{id}', [CashierController::class, 'store'])->name('cashier.store');
    });
});

// Logout Route (available for authenticated users)
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');
