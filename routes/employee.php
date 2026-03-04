<?php
// routes/employee.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\AuthController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\CustomerMovementController;
use App\Http\Controllers\Employee\PhotoGraphyBookingController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\Employee\RenewalDateController as EmployeeRenewalDateController;
use App\Http\Controllers\Employee\PhotographyCostController as EmployeePhotographyCostController;
use App\Http\Controllers\Employee\CustomerResponseController;
use App\Http\Controllers\Employee\EmployeeDesignerTaskAccountEmployeeController;
use App\Http\Controllers\Employee\CustomerCommunicationController;
use App\Http\Controllers\Employee\CustomerResponseCategoryInlineController as EmployeeCategoryInlineController;

// Employee Guest Routes (غير مسجل دخول)
Route::middleware('employee.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/login', [AuthController::class, 'login'])->name('employee.login.post');
});

// Employee Auth Routes (مسجل دخول)
Route::middleware('employee.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('employee.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('employee.logout');

    // المحادثات (Musonza Chat - مشترك بين المديرين والموظفين)
    Route::get('conversations', [App\Http\Controllers\ConversationController::class, 'index'])->name('employee.conversations.index');
    Route::post('conversations', [App\Http\Controllers\ConversationController::class, 'store'])->name('employee.conversations.store');
    Route::get('conversations/{id}', [App\Http\Controllers\ConversationController::class, 'show'])->name('employee.conversations.show');
    Route::post('conversations/{id}', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('employee.conversations.send');
    Route::delete('conversations/{id}', [App\Http\Controllers\ConversationController::class, 'destroy'])->name('employee.conversations.destroy');

    Route::middleware('employee.permission:receipts_payments')->group(function () {
        Route::get('/receipts-payments', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'index'])
            ->name('employee.receipts-payments');

        Route::post('/receipts-payments/receipt', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'storeReceipt'])
            ->name('employee.receipts-payments.receipt.store');

        Route::post('/receipts-payments/payment', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'storePayment'])
            ->name('employee.receipts-payments.payment.store');

        Route::post('/receipts-payments/target', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'updateTarget'])
            ->name('employee.receipts-payments.target');
 // الروتس الجديدة للتعديل
    Route::get('/receipts-payments/receipt/{receipt}/edit', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'editReceipt'])
        ->name('employee.receipts-payments.receipt.edit');

    Route::put('/receipts-payments/receipt/{receipt}', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'updateReceipt'])
        ->name('employee.receipts-payments.receipt.update');

    Route::get('/receipts-payments/payment/{payment}/edit', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'editPayment'])
        ->name('employee.receipts-payments.payment.edit');

    Route::put('/receipts-payments/payment/{payment}', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'updatePayment'])
        ->name('employee.receipts-payments.payment.update');

        Route::delete('/receipts-payments/receipt/{receipt}', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'deleteReceipt'])
            ->name('employee.receipts-payments.receipt.delete');

        Route::delete('/receipts-payments/payment/{payment}', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'deletePayment'])
            ->name('employee.receipts-payments.payment.delete');

        Route::get('/receipts-payments/print', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'printReport'])
            ->name('employee.receipts-payments.print');
    });

    Route::middleware('employee.permission:customer_movement')->group(function () {
        Route::get('/customer-movement', [CustomerMovementController::class, 'index'])
            ->name('employee.customer-movement');

        Route::post('/customer-movement', [CustomerMovementController::class, 'store'])
            ->name('employee.customer-movement.store');

        Route::get('/customer-movement/edit/{customerMovement}', [CustomerMovementController::class, 'edit'])
            ->name('employee.customer-movement.edit');

        Route::put('/customer-movement/{customerMovement}', [CustomerMovementController::class, 'update'])
            ->name('employee.customer-movement.update');

        Route::delete('/customer-movement/{customerMovement}', [CustomerMovementController::class, 'destroy'])
            ->name('employee.customer-movement.destroy');

        Route::post('/customer-movement/target', [CustomerMovementController::class, 'updateTarget'])
            ->name('employee.customer-movement.target');

        Route::get('/customer-movement/print', [CustomerMovementController::class, 'printReport'])
            ->name('employee.customer-movement.print');
    });

    Route::middleware('employee.permission:potential_customers')->group(function () {
        Route::get('/potential-customers', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'index'])
            ->name('employee.potential-customers');

        Route::post('/potential-customers', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'store'])
            ->name('employee.potential-customers.store');

        Route::get('/potential-customers/edit/{potentialCustomer}', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'edit'])
            ->name('employee.potential-customers.edit');

        Route::put('/potential-customers/{potentialCustomer}', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'update'])
            ->name('employee.potential-customers.update');

        Route::delete('/potential-customers/{potentialCustomer}', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'destroy'])
            ->name('employee.potential-customers.destroy');

        Route::put('/potential-customers/{potentialCustomer}/classifications', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'updateClassifications'])
            ->name('employee.potential-customers.classifications');

        Route::post('/potential-customers/{potentialCustomer}/quick-classification', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'quickClassification'])
            ->name('employee.potential-customers.quick-classification');

        Route::get('/potential-customers/stats', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'getClassificationStats'])
            ->name('employee.potential-customers.stats');

        Route::get('/potential-customers/print', [App\Http\Controllers\Employee\PotentialCustomerController::class, 'printReport'])
            ->name('employee.potential-customers.print');
    });

    Route::middleware('employee.permission:stopwatch_system')->group(function () {
        Route::get('/stopwatch', function () {
            return view('employee.systems.stopwatch');
        })->name('employee.stopwatch');
    });

    Route::middleware('employee.permission:general_operations')->group(function () {
        Route::get('/general-operations', function () {
            return view('employee.systems.general-operations');
        })->name('employee.general-operations');
    });

    // نظام حجز التصوير والمونتاج
    Route::middleware(['employee.permission:photography_booking'])->group(function () {
        Route::prefix('photography-booking')->name('employee.photography-booking.')->group(function () {
            Route::get('/', [PhotoGraphyBookingController::class, 'index'])->name('index');
            Route::get('/create', [PhotoGraphyBookingController::class, 'create'])->name('create');
            Route::post('/', [PhotoGraphyBookingController::class, 'store'])->name('store');
            Route::get('/{photographyBooking}', [PhotoGraphyBookingController::class, 'show'])->name('show');
            Route::get('/{photographyBooking}/edit', [PhotoGraphyBookingController::class, 'edit'])->name('edit');
            Route::put('/{photographyBooking}', [PhotoGraphyBookingController::class, 'update'])->name('update');
            Route::delete('/{photographyBooking}', [PhotoGraphyBookingController::class, 'destroy'])->name('destroy');
        });
    });

    Route::middleware(['employee.permission:print_booking'])->group(function () {
    Route::prefix('print-booking')->name('employee.print-booking.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employee\PrintBookingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Employee\PrintBookingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Employee\PrintBookingController::class, 'store'])->name('store');
        Route::get('/{printBooking}', [App\Http\Controllers\Employee\PrintBookingController::class, 'show'])->name('show');
        Route::get('/{printBooking}/edit', [App\Http\Controllers\Employee\PrintBookingController::class, 'edit'])->name('edit');
        Route::put('/{printBooking}', [App\Http\Controllers\Employee\PrintBookingController::class, 'update'])->name('update');
        Route::delete('/{printBooking}', [App\Http\Controllers\Employee\PrintBookingController::class, 'destroy'])->name('destroy');
    });
});

    // سيستم تكاليف التصميم
    Route::middleware('employee.permission:designers_account')
        ->prefix('designer-task-accounts')
        ->name('employee.designer-task-accounts.')
        ->group(function () {
            Route::get('/', [EmployeeDesignerTaskAccountEmployeeController::class, 'index'])->name('index');
            Route::get('/{id}', [EmployeeDesignerTaskAccountEmployeeController::class, 'show'])->name('show');
        });

        // / تكاليف الطباعة للموظفين
Route::middleware('employee.permission:print_costs')->group(function () {
    Route::prefix('print-costs')->name('employee.print-costs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Employee\PrintCostsController::class, 'index'])->name('index');
        Route::post('/receipt', [App\Http\Controllers\Employee\PrintCostsController::class, 'storeReceipt'])->name('receipt.store');
        Route::post('/payment', [App\Http\Controllers\Employee\PrintCostsController::class, 'storePayment'])->name('payment.store');
        
        // Routes للتعديل
        Route::get('/receipt/{receipt}/edit', [App\Http\Controllers\Employee\PrintCostsController::class, 'editReceipt'])->name('receipt.edit');
        Route::put('/receipt/{receipt}', [App\Http\Controllers\Employee\PrintCostsController::class, 'updateReceipt'])->name('receipt.update');
        
        Route::get('/payment/{payment}/edit', [App\Http\Controllers\Employee\PrintCostsController::class, 'editPayment'])->name('payment.edit');
        Route::put('/payment/{payment}', [App\Http\Controllers\Employee\PrintCostsController::class, 'updatePayment'])->name('payment.update');
        
        Route::delete('/receipt/{receipt}', [App\Http\Controllers\Employee\PrintCostsController::class, 'deleteReceipt'])->name('receipt.delete');
        Route::delete('/payment/{payment}', [App\Http\Controllers\Employee\PrintCostsController::class, 'deletePayment'])->name('payment.delete');
        
        Route::get('/print', [App\Http\Controllers\Employee\PrintCostsController::class, 'printReport'])->name('print');
    });
});

    Route::middleware('employee.permission:customer_response')->group(function () {
        Route::prefix('customer-response')->name('employee.customer-response.')->group(function () {
            // الردود
            Route::get('/', [CustomerResponseController::class, 'index'])->name('index');
            Route::get('/create', [CustomerResponseController::class, 'create'])->name('create');
            Route::post('/', [CustomerResponseController::class, 'store'])->name('store');
            Route::get('/{customerResponse}', [CustomerResponseController::class, 'show'])->name('show');
            Route::get('/{customerResponse}/edit', [CustomerResponseController::class, 'edit'])->name('edit');
            Route::put('/{customerResponse}', [CustomerResponseController::class, 'update'])->name('update');
            Route::delete('/{customerResponse}', [CustomerResponseController::class, 'destroy'])->name('destroy');

            // إدارة التصنيفات من داخل index
            Route::post('/categories', [EmployeeCategoryInlineController::class, 'store'])->name('categories.store');
            Route::put('/categories/{category}', [EmployeeCategoryInlineController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [EmployeeCategoryInlineController::class, 'destroy'])->name('categories.destroy');
            Route::get('/categories/icons', [EmployeeCategoryInlineController::class, 'icons'])->name('categories.icons');
        });
    });

    Route::middleware('employee.permission:task_list')->group(function () {
        Route::get('/task-list', function () {
            return view('employee.systems.task-list');
        })->name('employee.task-list');
    });

    Route::middleware('employee.permission:renewal_dates')->group(function () {
        Route::prefix('renewal-dates')->name('employee.renewal-dates.')->group(function () {
            Route::get('/', [EmployeeRenewalDateController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeRenewalDateController::class, 'create'])->name('create');
            Route::post('/', [EmployeeRenewalDateController::class, 'store'])->name('store');
            Route::get('/{renewalDate}', [EmployeeRenewalDateController::class, 'show'])->name('show');
            Route::get('/{renewalDate}/edit', [EmployeeRenewalDateController::class, 'edit'])->name('edit');
            Route::put('/{renewalDate}', [EmployeeRenewalDateController::class, 'update'])->name('update');
            Route::delete('/{renewalDate}', [EmployeeRenewalDateController::class, 'destroy'])->name('destroy');
            Route::get('/calendar/view', [EmployeeRenewalDateController::class, 'calendar'])->name('calendar');
            Route::post('/{renewalDate}/complete', [EmployeeRenewalDateController::class, 'markCompleted'])->name('complete');
            Route::post('/{renewalDate}/renew', [EmployeeRenewalDateController::class, 'renew'])->name('renew');
        });
    });

    // سيستم تكاليف التصوير 
    Route::middleware('employee.permission:photography_costs')->group(function () {
        Route::prefix('photography-costs')->name('employee.photography-costs.')->group(function () {
            Route::get('/', [EmployeePhotographyCostController::class, 'index'])->name('index');

            Route::post('/receipt', [EmployeePhotographyCostController::class, 'storeReceipt'])->name('receipt.store');
            Route::post('/payment', [EmployeePhotographyCostController::class, 'storePayment'])->name('payment.store');
            Route::delete('/receipt/{id}', [EmployeePhotographyCostController::class, 'deleteReceipt'])->name('receipt.delete');
            Route::delete('/payment/{id}', [EmployeePhotographyCostController::class, 'deletePayment'])->name('payment.delete');
            Route::get('/print', [EmployeePhotographyCostController::class, 'printReport'])->name('print');

            Route::get('/create', [EmployeePhotographyCostController::class, 'create'])->name('create');
            Route::post('/', [EmployeePhotographyCostController::class, 'store'])->name('store');
            Route::get('/{photographyCost}', [EmployeePhotographyCostController::class, 'show'])->name('show');
            Route::get('/{photographyCost}/edit', [EmployeePhotographyCostController::class, 'edit'])->name('edit');
            Route::put('/{photographyCost}', [EmployeePhotographyCostController::class, 'update'])->name('update');
            Route::delete('/{photographyCost}', [EmployeePhotographyCostController::class, 'destroy'])->name('destroy');
        });
    });

    // ============== النظام الجديد للتواصل مع العملاء ==============
    Route::middleware(['employee.permission:customer_communication'])
        ->prefix('customer-communication')
        ->name('employee.customer-communication.')
        ->group(function () {
            // الصفحة الرئيسية - قائمة العملاء
            Route::get('/', [CustomerCommunicationController::class, 'index'])->name('index');

            // صفحة الشات مع الإدارة بخصوص عميل معين
            Route::get('/{potentialCustomerId}', [CustomerCommunicationController::class, 'show'])->name('show');

            // إرسال رسالة في الشات
            Route::post('/{chatId}/send', [CustomerCommunicationController::class, 'sendMessage'])->name('send-message');

            // تحميل الرسائل الجديدة فقط (محسن)
            Route::get('/{chatId}/new-messages', [CustomerCommunicationController::class, 'getNewMessages'])->name('new-messages');

            // تحميل جميع الرسائل
            Route::get('/{chatId}/messages', [CustomerCommunicationController::class, 'getMessages'])->name('get-messages');

            // تحديد حالة العميل كـ "تم التواصل"
            Route::post('/{chatId}/completed', [CustomerCommunicationController::class, 'markAsCompleted'])->name('completed');

            // حذف رسالة معينة (خلال 5 دقائق من الإرسال)
            Route::delete('/message/{messageId}', [CustomerCommunicationController::class, 'deleteMessage'])->name('delete-message');

            // إحصائيات سريعة للتحديث التلقائي
            Route::get('/unread-count', function () {
                $employee = auth('employee')->user();
                $unreadCount = \App\Models\CustomerChatMessage::whereHas('chat', function ($query) use ($employee) {
                    $query->where('employee_id', $employee->id);
                })->where('sender_type', 'admin')
                    ->where('is_read', false)
                    ->count();

                return response()->json(['count' => $unreadCount]);
            })->name('unread-count');
        });

    Route::middleware('employee.permission:montage_follow_up')->group(function () {
        Route::get('/montage-follow-up', function () {
            return view('employee.systems.montage-follow-up');
        })->name('employee.montage-follow-up');
    });

    // سيستم المهام - المحدث مع إضافة routes التعديل

Route::get('/tasks', [EmployeeTaskController::class, 'index'])->name('employee.tasks.index');
Route::post('/tasks', [EmployeeTaskController::class, 'store'])->name('employee.tasks.store');
Route::get('/tasks/{task}', [EmployeeTaskController::class, 'show'])->name('employee.tasks.show');
Route::post('/tasks/{task}/edit', [EmployeeTaskController::class, 'edit'])->name('employee.tasks.edit');
Route::patch('/tasks/{task}', [EmployeeTaskController::class, 'update'])->name('employee.tasks.update');
Route::delete('/tasks/{task}', [EmployeeTaskController::class, 'destroy'])->name('employee.tasks.destroy');
Route::get('/tasks/print', [EmployeeTaskController::class, 'print'])->name('employee.tasks.print');

    // باقي الـ routes...
    // [باقي الكود كما هو دون تغيير]
    
    // نظام متابعة المشاريع والمهام مع الاستوب ووتش
    Route::middleware('employee.permission:project_tracking')->group(function () {
        Route::prefix('project-tracking')->name('employee.project-tracking.')->group(function () {
            Route::get('/', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'index'])->name('index');

            // البصمة المحسنة
            Route::post('/check-in', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'checkOut'])->name('check-out');
            Route::post('/temp-check-out', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'tempCheckOut'])->name('temp-check-out');
            Route::post('/temp-check-in', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'tempCheckIn'])->name('temp-check-in');

            // الاستوب ووتش المحسن - نفس الأدمن بالضبط
            Route::post('/start-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'startTimer'])->name('start-timer');
            Route::post('/stop-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'stopTimer'])->name('stop-timer');
            Route::post('/pause-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'pauseTimer'])->name('pause-timer');
            Route::post('/resume-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'resumeTimer'])->name('resume-timer');
            Route::post('/restart-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'restartTimer'])->name('restart-timer');
            Route::post('/edit-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'editTimer'])->name('edit-timer');

            // معلومات التايمر المحسنة
            Route::get('/active-timer', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'getActiveTimer'])->name('active-timer');
            Route::post('/timer-details', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'getTimerDetails'])->name('timer-details');
            Route::get('/today-entries', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'todayTimeEntries'])->name('today-entries');

            // إنشاء مشاريع ومهام
            Route::post('/create-project', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'createProject'])->name('create-project');
            Route::post('/add-task', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'addTaskToProject'])->name('add-task');

            // التقارير
            Route::get('/reports', [App\Http\Controllers\Employee\ProjectTrackingController::class, 'reports'])->name('reports');
        });
    });

    // نظام الحضور والانصراف
    Route::middleware('employee.permission:attendance_tracking')->group(function () {
        Route::prefix('attendance')->name('employee.attendance.')->group(function () {
            Route::get('/', [App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('index');
            Route::get('/today-status', [App\Http\Controllers\Employee\AttendanceController::class, 'todayStatus'])->name('today-status');
            Route::get('/print', [App\Http\Controllers\Employee\AttendanceController::class, 'printReport'])->name('print');
        });
    });

    // تقارير العمل
    Route::middleware('employee.permission:work_reports')->group(function () {
        Route::prefix('work-reports')->name('employee.work-reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Employee\WorkReportsController::class, 'index'])->name('index');
            Route::get('/print', [App\Http\Controllers\Employee\WorkReportsController::class, 'printReport'])->name('print');
        });
    });

    // نظام شات متابعة التصميم
    Route::middleware('employee.permission:design_follow_up')->group(function () {
        Route::get('/design-follow-up', [App\Http\Controllers\Employee\WorkChatController::class, 'index'])
            ->defaults('type', 'design')
            ->name('employee.design-follow-up');
    });

    // نظام شات متابعة المونتاج  
    Route::middleware('employee.permission:montage_follow_up')->group(function () {
        Route::get('/montage-follow-up', [App\Http\Controllers\Employee\WorkChatController::class, 'index'])
            ->defaults('type', 'montage')
            ->name('employee.montage-follow-up');
    });

    // Routes مشتركة للشاتات (التحقق من الصلاحية داخل الكونترولر)
    Route::prefix('work-chat')->name('employee.work-chat.')->group(function () {
        Route::get('/{workChat}', [App\Http\Controllers\Employee\WorkChatController::class, 'show'])->name('show');
        Route::post('/{workChat}/send', [App\Http\Controllers\Employee\WorkChatController::class, 'sendMessage'])->name('send-message');
        Route::get('/{workChat}/messages', [App\Http\Controllers\Employee\WorkChatController::class, 'getMessages'])->name('get-messages');
    });

    // في routes/employee.php - أضف هذا للاختبار
    Route::get('/test-file/{path}', function ($path) {
        $fullPath = 'customer-chat/' . $path;
        if (Storage::disk('public')->exists($fullPath)) {
            return response()->json([
                'exists' => true,
                'url' => asset('storage/' . $fullPath),
                'size' => Storage::disk('public')->size($fullPath)
            ]);
        }
        return response()->json(['exists' => false]);
    })->where('path', '.*');
});