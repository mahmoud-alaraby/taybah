<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReceiptsPaymentsController;

Route::middleware('admin.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
});

// Admin Auth Routes (مسجل دخول)
Route::middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // إدارة المديرين
    Route::resource('admins', AdminController::class)->names([
        'index' => 'admin.admins.index',
        'create' => 'admin.admins.create',
        'store' => 'admin.admins.store',
        'show' => 'admin.admins.show',
        'edit' => 'admin.admins.edit',
        'update' => 'admin.admins.update',
        'destroy' => 'admin.admins.destroy',
    ]);
    
    // إدارة الموظفين
    Route::resource('employees', EmployeeController::class)->names([
        'index' => 'admin.employees.index',
        'create' => 'admin.employees.create',
        'store' => 'admin.employees.store',
        'show' => 'admin.employees.show',
        'edit' => 'admin.employees.edit',
        'update' => 'admin.employees.update',
        'destroy' => 'admin.employees.destroy',
    ]);
    
    // إدارة الأدوار
    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'show' => 'admin.roles.show',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);

    // إدارة المقبوضات والمدفوعات
    Route::prefix('receipts-payments')->name('admin.receipts-payments.')->group(function () {
        Route::get('/', [ReceiptsPaymentsController::class, 'index'])->name('index');
        
        Route::post('/receipt', [ReceiptsPaymentsController::class, 'storeReceipt'])->name('receipt.store');
        
        Route::post('/payment', [ReceiptsPaymentsController::class, 'storePayment'])->name('payment.store');
        
        Route::post('/target', [ReceiptsPaymentsController::class, 'updateTarget'])->name('target');
        
        Route::delete('/receipt/{receipt}', [ReceiptsPaymentsController::class, 'deleteReceipt'])->name('receipt.delete');
        
        Route::delete('/payment/{payment}', [ReceiptsPaymentsController::class, 'deletePayment'])->name('payment.delete');
        
        Route::get('/print', [ReceiptsPaymentsController::class, 'printReport'])->name('print');
    });
});