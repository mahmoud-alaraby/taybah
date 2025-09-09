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
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\RenewalDateController as AdminRenewalDateController;
use App\Http\Controllers\Admin\PhotographyCostController;
use App\Http\Controllers\Admin\CustomerResponseController as AdminCustomerResponseController;
use App\Http\Controllers\Admin\AdminCustomerResponseCategoryController;
use App\Http\Controllers\Admin\DesignerTaskAccountAdminController;
use App\Http\Controllers\Admin\CustomerCommunicationController  as AdminCustomerCommunicationController;
use App\Http\Controllers\Admin\CustomerResponseCategoryInlineController as  AdminCategoryInlineController;
use App\Http\Controllers\Admin\AdminWorkReportsController;





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

  // مسارات نظام التشغيل العام
    // مسارات نظام التشغيل العام
    Route::prefix('operation-system')->name('admin.operation-system.')->group(function () {
        Route::get('/', [OperationSystemController::class, 'index'])->name('index');
        Route::get('/print', [OperationSystemController::class, 'printReport'])->name('print');
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
        // إضافة راوت خاص بطباعة المهام المفلترة أو الحالية
Route::get('/tasks/print', [AdminTaskController::class, 'print'])->name('tasks.print');
// الراوت الأساسي مهمش، لن يتغير (وهو لمسار index الحالي)

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
    // Route::prefix('photography-costs')->name('admin.photography-costs.')->group(function () {
    //     Route::get('/', [PhotographyCostController::class, 'index'])->name('index');
    //     Route::get('/create', [PhotographyCostController::class, 'create'])->name('create');
    //     Route::post('/', [PhotographyCostController::class, 'store'])->name('store');
    //     Route::get('/{photographyCost}', [PhotographyCostController::class, 'show'])->name('show');
    //     Route::get('/{photographyCost}/edit', [PhotographyCostController::class, 'edit'])->name('edit');
    //     Route::put('/{photographyCost}', [PhotographyCostController::class, 'update'])->name('update');
    //     Route::delete('/{photographyCost}', [PhotographyCostController::class, 'destroy'])->name('destroy');
    // });
// استبدل routes photography-costs الحالية بهذه
Route::prefix('photography-costs')->name('admin.photography-costs.')->group(function () {
    Route::get('/', [PhotographyCostController::class, 'index'])->name('index');
    Route::post('/receipt', [PhotographyCostController::class, 'storeReceipt'])->name('receipt.store');
    Route::post('/payment', [PhotographyCostController::class, 'storePayment'])->name('payment.store');
    Route::post('/target', [PhotographyCostController::class, 'updateTarget'])->name('target');
    Route::delete('/receipt/{id}', [PhotographyCostController::class, 'deleteReceipt'])->name('receipt.delete');
    Route::delete('/payment/{id}', [PhotographyCostController::class, 'deletePayment'])->name('payment.delete');
    Route::get('/print', [PhotographyCostController::class, 'printReport'])->name('print');
    
    // Routes القديمة للتوافق
    Route::get('/create', [PhotographyCostController::class, 'create'])->name('create');
    Route::post('/', [PhotographyCostController::class, 'store'])->name('store');
    Route::get('/{photographyCost}', [PhotographyCostController::class, 'show'])->name('show');
    Route::get('/{photographyCost}/edit', [PhotographyCostController::class, 'edit'])->name('edit');
    Route::put('/{photographyCost}', [PhotographyCostController::class, 'update'])->name('update');
    Route::delete('/{photographyCost}', [PhotographyCostController::class, 'destroy'])->name('destroy');
});
    // سيستم الرد على العملاء 
    Route::prefix('customer-response')->name('admin.customer-response.')->group(function () {
        // الردود
        Route::get('/', [AdminCustomerResponseController::class, 'index'])->name('index');
        Route::get('/create', [AdminCustomerResponseController::class, 'create'])->name('create');
        Route::post('/', [AdminCustomerResponseController::class, 'store'])->name('store');
        Route::get('/{customerResponse}', [AdminCustomerResponseController::class, 'show'])->name('show');
        Route::get('/{customerResponse}/edit', [AdminCustomerResponseController::class, 'edit'])->name('edit');
        Route::put('/{customerResponse}', [AdminCustomerResponseController::class, 'update'])->name('update');
        Route::delete('/{customerResponse}', [AdminCustomerResponseController::class, 'destroy'])->name('destroy');

        // التصنيفات
        // Route::get('/categories', [AdminCustomerResponseCategoryController::class, 'index'])->name('categories.index');
        // Route::get('/categories/create', [AdminCustomerResponseCategoryController::class, 'create'])->name('categories.create');
        // Route::post('/categories', [AdminCustomerResponseCategoryController::class, 'store'])->name('categories.store');
        // Route::get('/categories/{category}/edit', [AdminCustomerResponseCategoryController::class, 'edit'])->name('categories.edit');
        // Route::put('/categories/{category}', [AdminCustomerResponseCategoryController::class, 'update'])->name('categories.update');
        // Route::delete('/categories/{category}', [AdminCustomerResponseCategoryController::class, 'destroy'])->name('categories.destroy');

        // إدارة التصنيفات من داخل index
        Route::post('/categories', [AdminCategoryInlineController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryInlineController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryInlineController::class, 'destroy'])->name('categories.destroy');
        // إن رغبت بإرجاع قائمة الأيقونات
        Route::get('/categories/icons', [AdminCategoryInlineController::class, 'icons'])->name('categories.icons');
    });





    Route::prefix('designer-task-accounts')->name('admin.designer-task-accounts.')->group(function () {
        Route::get('/', [DesignerTaskAccountAdminController::class, 'index'])->name('index');
        Route::get('/create', [DesignerTaskAccountAdminController::class, 'create'])->name('create');
        Route::post('/', [DesignerTaskAccountAdminController::class, 'store'])->name('store');
        Route::get('/{id}', [DesignerTaskAccountAdminController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DesignerTaskAccountAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DesignerTaskAccountAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [DesignerTaskAccountAdminController::class, 'destroy'])->name('destroy');

        // حذف جميع المهام ليوم معين
        Route::post('/destroy-all', [DesignerTaskAccountAdminController::class, 'destroyAllTasksForDay'])->name('destroyAllTasksForDay');
    });


Route::prefix('customer-communication')->name('admin.customer-communication.')->group(function () {
    Route::get('/', [AdminCustomerCommunicationController::class, 'index'])->name('index');
    Route::get('/{id}', [AdminCustomerCommunicationController::class, 'show'])->name('show');
    Route::post('/{id}/reply', [AdminCustomerCommunicationController::class, 'reply'])->name('reply');
    Route::post('/{id}/mark-read', [AdminCustomerCommunicationController::class, 'markRead'])->name('markRead');
    
    Route::delete('note/{note_id}', [AdminCustomerCommunicationController::class, 'destroyNote'])
        ->name('note.destroy');
    Route::delete('all-notes/{potential_customer_id}', [AdminCustomerCommunicationController::class, 'destroyAllNotes'])
        ->name('all-notes.destroy');
});




    // نظام متابعة المشاريع والمهام
    Route::prefix('projects')->name('admin.projects.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\ProjectController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [App\Http\Controllers\Admin\ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('destroy');
        Route::post('/{project}/tasks', [App\Http\Controllers\Admin\ProjectController::class, 'addTask'])->name('add-task');
        Route::get('/reports/overview', [App\Http\Controllers\Admin\ProjectController::class, 'reports'])->name('reports');
    });

    // إدارة المهام
    Route::prefix('project-tasks')->name('admin.project-tasks.')->group(function () {
        Route::put('/{task}', [App\Http\Controllers\Admin\ProjectTaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [App\Http\Controllers\Admin\ProjectTaskController::class, 'destroy'])->name('destroy');
    });

    // متابعة الحضور والانصراف
    Route::prefix('attendance')->name('admin.attendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/reports', [App\Http\Controllers\Admin\AttendanceController::class, 'reports'])->name('reports');
    });

    // متابعة الوقت والساعات
    Route::prefix('time-tracking')->name('admin.time-tracking.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TimeTrackingController::class, 'index'])->name('index');
        Route::get('/reports', [App\Http\Controllers\Admin\TimeTrackingController::class, 'reports'])->name('reports');
        Route::get('/daily-summary', [App\Http\Controllers\Admin\TimeTrackingController::class, 'dailySummary'])->name('daily-summary');
    });
    // تقارير العمل 
    Route::prefix('work-reports')->name('admin.work-reports.')->group(function () {
        Route::get('/', [AdminWorkReportsController::class, 'index'])->name('index');
        Route::get('/print', [AdminWorkReportsController::class, 'print'])->name('print');
    });

    Route::prefix('work-chat')->name('admin.work-chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WorkChatController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\WorkChatController::class, 'create'])->name('create');

        // Storage Management Routes - يجب أن تكون قبل {workChat}
        Route::get('/storage-info', [App\Http\Controllers\Admin\WorkChatController::class, 'getStorageInfo'])->name('storage-info');
        Route::get('/storage-management', [App\Http\Controllers\Admin\WorkChatController::class, 'storageManagement'])->name('storage-management');
        Route::post('/clear-old-files', [App\Http\Controllers\Admin\WorkChatController::class, 'clearOldFiles'])->name('clear-old-files');
        Route::get('/backup-files', [App\Http\Controllers\Admin\WorkChatController::class, 'backupFiles'])->name('backup-files');

        // Routes with parameters - يجب أن تكون في النهاية
        Route::post('/', [App\Http\Controllers\Admin\WorkChatController::class, 'store'])->name('store');
        Route::get('/{workChat}', [App\Http\Controllers\Admin\WorkChatController::class, 'show'])->name('show');
        Route::delete('/{workChat}', [App\Http\Controllers\Admin\WorkChatController::class, 'destroy'])->name('destroy');

        // API Routes for chat functionality
        Route::post('/{workChat}/send', [App\Http\Controllers\Admin\WorkChatController::class, 'sendMessage'])->name('send-message');
        Route::get('/{workChat}/messages', [App\Http\Controllers\Admin\WorkChatController::class, 'getMessages'])->name('get-messages');
        Route::post('/{workChat}/clear-files', [App\Http\Controllers\Admin\WorkChatController::class, 'clearFiles'])->name('clear-files');

        // تحميل ملفات شات محدد - Route جديد
        Route::get('/{workChat}/backup-files', [App\Http\Controllers\Admin\WorkChatController::class, 'backupChatFiles'])->name('backup-chat-files');
    });
    
});