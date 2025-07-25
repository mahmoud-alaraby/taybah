<?php
// routes/employee.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\AuthController;
use App\Http\Controllers\Employee\DashboardController;

// Employee Guest Routes (غير مسجل دخول)
Route::middleware('employee.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/login', [AuthController::class, 'login'])->name('employee.login.post');
});

// Employee Auth Routes (مسجل دخول)
Route::middleware('employee.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('employee.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('employee.logout');
    
    // الصفحات حسب الصلاحيات
    Route::middleware('employee.permission:receipts_payments')->group(function () {
        Route::get('/receipts-payments', function() {
            return view('employee.systems.receipts-payments');
        })->name('employee.receipts-payments');
    });
    
    Route::middleware('employee.permission:customer_movement')->group(function () {
        Route::get('/customer-movement', function() {
            return view('employee.systems.customer-movement');
        })->name('employee.customer-movement');
    });
    
    Route::middleware('employee.permission:potential_customers')->group(function () {
        Route::get('/potential-customers', function() {
            return view('employee.systems.potential-customers');
        })->name('employee.potential-customers');
    });
    
    Route::middleware('employee.permission:stopwatch_system')->group(function () {
        Route::get('/stopwatch', function() {
            return view('employee.systems.stopwatch');
        })->name('employee.stopwatch');
    });
    
    Route::middleware('employee.permission:general_operations')->group(function () {
        Route::get('/general-operations', function() {
            return view('employee.systems.general-operations');
        })->name('employee.general-operations');
    });
    
    Route::middleware('employee.permission:photography_booking')->group(function () {
        Route::get('/photography-booking', function() {
            return view('employee.systems.photography-booking');
        })->name('employee.photography-booking');
    });
    
    Route::middleware('employee.permission:designers_account')->group(function () {
        Route::get('/designers-account', function() {
            return view('employee.systems.designers-account');
        })->name('employee.designers-account');
    });
    
    Route::middleware('employee.permission:customer_response')->group(function () {
        Route::get('/customer-response', function() {
            return view('employee.systems.customer-response');
        })->name('employee.customer-response');
    });
    
    Route::middleware('employee.permission:task_list')->group(function () {
        Route::get('/task-list', function() {
            return view('employee.systems.task-list');
        })->name('employee.task-list');
    });
    
    Route::middleware('employee.permission:renewal_dates')->group(function () {
        Route::get('/renewal-dates', function() {
            return view('employee.systems.renewal-dates');
        })->name('employee.renewal-dates');
    });
    
    Route::middleware('employee.permission:photography_costs')->group(function () {
        Route::get('/photography-costs', function() {
            return view('employee.systems.photography-costs');
        })->name('employee.photography-costs');
    });
    
    Route::middleware('employee.permission:customer_communication')->group(function () {
        Route::get('/customer-communication', function() {
            return view('employee.systems.customer-communication');
        })->name('employee.customer-communication');
    });
    
    Route::middleware('employee.permission:design_follow_up')->group(function () {
        Route::get('/design-follow-up', function() {
            return view('employee.systems.design-follow-up');
        })->name('employee.design-follow-up');
    });
    
    Route::middleware('employee.permission:montage_follow_up')->group(function () {
        Route::get('/montage-follow-up', function() {
            return view('employee.systems.montage-follow-up');
        })->name('employee.montage-follow-up');
    });
});