<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

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
    
    // Records Module
    Route::get('records', [RecordsController::class, 'index'])->name('records.index');
    Route::get('records/create', [RecordsController::class, 'create'])->name('records.create');
    Route::post('records', [RecordsController::class, 'store'])->name('records.store');
    Route::get('records/{document}', [RecordsController::class, 'show'])->name('records.show');
    Route::post('records/{document}/route', [RecordsController::class, 'route'])->name('records.route');
    Route::post('records/{document}/receive', [RecordsController::class, 'receive'])->name('records.receive');
    Route::get('records/search', [RecordsController::class, 'search'])->name('records.search');
    
    // Budget Module
    Route::get('budget', [BudgetController::class, 'index'])->name('budget.index');
    
    // Accounting Module
    Route::get('accounting', [AccountingController::class, 'index'])->name('accounting.index');
    
    // Cashier Module
    Route::get('cashier', [CashierController::class, 'index'])->name('cashier.index');
});

// Logout Route (available for authenticated users)
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth'); 