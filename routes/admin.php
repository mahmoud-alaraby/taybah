<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReceiptsPaymentsController;
use App\Http\Controllers\Admin\CustomerMovementController;
use App\Http\Controllers\Admin\OperationSystemController;
use App\Http\Controllers\Admin\PhotoGraphyBookingController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController ;
use App\Http\Controllers\Admin\RenewalDateController as AdminRenewalDateController;
use App\Http\Controllers\Admin\PhotographyCostController;
use App\Http\Controllers\Admin\CustomerResponseController as AdminCustomerResponseController;
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

    // إدارة حركة العملاء
    Route::prefix('customer-movement')->name('admin.customer-movement.')->group(function () {
        Route::get('/', [CustomerMovementController::class, 'index'])->name('index');
        
        Route::get('/create', [CustomerMovementController::class, 'create'])->name('create');
        
        Route::post('/', [CustomerMovementController::class, 'store'])->name('store');
        
        Route::get('/{customerMovement}/edit', [CustomerMovementController::class, 'edit'])->name('edit');
        
        Route::put('/{customerMovement}', [CustomerMovementController::class, 'update'])->name('update');
        
        Route::delete('/{customerMovement}', [CustomerMovementController::class, 'destroy'])->name('destroy');
        
        Route::post('/target', [CustomerMovementController::class, 'updateTarget'])->name('target');
        
        Route::get('/print', [CustomerMovementController::class, 'printReport'])->name('print');
    });



    // إدارة العملاء المحتملين
    Route::prefix('potential-customers')->name('admin.potential-customers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'index'])->name('index');
        
        Route::get('/create', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'create'])->name('create');
        
        Route::post('/', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'store'])->name('store');
        
        Route::get('/{potentialCustomer}/edit', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'edit'])->name('edit');
        
        Route::put('/{potentialCustomer}', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'update'])->name('update');
        
        Route::delete('/{potentialCustomer}', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'destroy'])->name('destroy');
        
        Route::put('/{potentialCustomer}/classifications', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'updateClassifications'])->name('classifications');
        
        Route::post('/bulk-classifications', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'bulkUpdateClassifications'])->name('bulk-classifications');
        
        Route::get('/stats', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'getClassificationStats'])->name('stats');
        
        Route::get('/print', [App\Http\Controllers\Admin\PotentialCustomerController::class, 'printReport'])->name('print');
    });

      
    // مسارات نظام التشغيل العام
  
    Route::prefix('operation-system')->name('admin.operation-system.')->group(function () {
        Route::get('/', [OperationSystemController::class, 'index'])->name('index');
        Route::get('/create', [OperationSystemController::class, 'create'])->name('create');
        Route::post('/', [OperationSystemController::class, 'store'])->name('store');
        Route::get('/{id}', [OperationSystemController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OperationSystemController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OperationSystemController::class, 'update'])->name('update');
        Route::delete('/{id}', [OperationSystemController::class, 'destroy'])->name('destroy');
    });

    
   
      
     
      // حجوزات التصوير والمونتاج - ROUTES المُصححة
Route::prefix('photography-booking')->name('admin.photography-booking.')->group(function () {
    Route::get('/', [PhotoGraphyBookingController::class, 'index'])->name('index');
    Route::get('/create', [PhotoGraphyBookingController::class, 'create'])->name('create');
    Route::post('/', [PhotoGraphyBookingController::class, 'store'])->name('store');
    Route::get('/{photographyBooking}', [PhotoGraphyBookingController::class, 'show'])->name('show');
    Route::get('/{photographyBooking}/edit', [PhotoGraphyBookingController::class, 'edit'])->name('edit');
    Route::put('/{photographyBooking}', [PhotoGraphyBookingController::class, 'update'])->name('update');
    Route::delete('/{photographyBooking}', [PhotoGraphyBookingController::class, 'destroy'])->name('destroy');
    
    // Routes الإشعارات
    Route::get('/notifications', [PhotoGraphyBookingController::class, 'notifications'])->name('notifications');
    
    Route::post('/notifications/{notification}/read', [PhotoGraphyBookingController::class, 'markNotificationRead'])->name('notification.read');
});

// سيستم قائمة المهام 

// مسارات الأدمن
Route::prefix('admin')->name('admin.')->group(function () {


    Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [AdminTaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [AdminTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');

});

// نظام مواعيد التجديد

Route::prefix('renewal-dates')->name('admin.renewal-dates.')->group(function () {
    Route::get('/', [AdminRenewalDateController::class, 'index'])->name('index');
    Route::get('/create', [AdminRenewalDateController::class, 'create'])->name('create');
    Route::post('/', [AdminRenewalDateController::class, 'store'])->name('store');
    Route::get('/{renewalDate}', [AdminRenewalDateController::class, 'show'])->name('show');
    Route::get('/{renewalDate}/edit', [AdminRenewalDateController::class, 'edit'])->name('edit');
    Route::put('/{renewalDate}', [AdminRenewalDateController::class, 'update'])->name('update');
    Route::delete('/{renewalDate}', [AdminRenewalDateController::class, 'destroy'])->name('destroy');
    Route::get('/calendar/view', [AdminRenewalDateController::class, 'calendar'])->name('calendar');
    Route::post('/{renewalDate}/complete', [AdminRenewalDateController::class, 'markCompleted'])->name('complete');
    Route::post('/{renewalDate}/renew', [AdminRenewalDateController::class, 'renew'])->name('renew');
});

// نظام تكاليف التصوير
// الأدمن
Route::prefix('photography-costs')->name('admin.photography-costs.')->group(function () {
    Route::get('/', [PhotographyCostController::class, 'index'])->name('index');
    Route::get('/create', [PhotographyCostController::class, 'create'])->name('create');
    Route::post('/', [PhotographyCostController::class, 'store'])->name('store');
    Route::get('/{photographyCost}', [PhotographyCostController::class, 'show'])->name('show');
    Route::get('/{photographyCost}/edit', [PhotographyCostController::class, 'edit'])->name('edit');
    Route::put('/{photographyCost}', [PhotographyCostController::class, 'update'])->name('update');
    Route::delete('/{photographyCost}', [PhotographyCostController::class, 'destroy'])->name('destroy');
});

// سيستم الرد على العملاء 
Route::prefix('customer-response')->name('admin.customer-response.')->group(function () {
    Route::get('/', [AdminCustomerResponseController::class, 'index'])->name('index');
    Route::get('/create', [AdminCustomerResponseController::class, 'create'])->name('create');
    Route::post('/', [AdminCustomerResponseController::class, 'store'])->name('store');
    Route::get('/{customerResponse}', [AdminCustomerResponseController::class, 'show'])->name('show');
    Route::get('/{customerResponse}/edit', [AdminCustomerResponseController::class, 'edit'])->name('edit');
    Route::put('/{customerResponse}', [AdminCustomerResponseController::class, 'update'])->name('update');
    Route::delete('/{customerResponse}', [AdminCustomerResponseController::class, 'destroy'])->name('destroy');
});


});