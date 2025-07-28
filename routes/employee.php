<?php
// routes/employee.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\AuthController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\CustomerMovementController;
use App\Http\Controllers\Employee\PhotoGraphyBookingController;
// Employee Guest Routes (غير مسجل دخول)
Route::middleware('employee.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('employee.login');
    Route::post('/login', [AuthController::class, 'login'])->name('employee.login.post');
});

// Employee Auth Routes (مسجل دخول)
Route::middleware('employee.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('employee.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('employee.logout');

    Route::middleware('employee.permission:receipts_payments')->group(function () {
        Route::get('/receipts-payments', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'index'])
            ->name('employee.receipts-payments');

        Route::post('/receipts-payments/receipt', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'storeReceipt'])
            ->name('employee.receipts-payments.receipt.store');

        Route::post('/receipts-payments/payment', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'storePayment'])
            ->name('employee.receipts-payments.payment.store');

        Route::post('/receipts-payments/target', [App\Http\Controllers\Employee\ReceiptsPaymentsController::class, 'updateTarget'])
            ->name('employee.receipts-payments.target');

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

    Route::middleware('employee.permission:photography_booking')->group(function () {
        Route::get('/photography-booking', function () {
            return view('employee.systems.photography-booking');
        })->name('employee.photography-booking');
    });

    Route::middleware('employee.permission:designers_account')->group(function () {
        Route::get('/designers-account', function () {
            return view('employee.systems.designers-account');
        })->name('employee.designers-account');
    });

    Route::middleware('employee.permission:customer_response')->group(function () {
        Route::get('/customer-response', function () {
            return view('employee.systems.customer-response');
        })->name('employee.customer-response');
    });

    Route::middleware('employee.permission:task_list')->group(function () {
        Route::get('/task-list', function () {
            return view('employee.systems.task-list');
        })->name('employee.task-list');
    });

    Route::middleware('employee.permission:renewal_dates')->group(function () {
        Route::get('/renewal-dates', function () {
            return view('employee.systems.renewal-dates');
        })->name('employee.renewal-dates');
    });

    Route::middleware('employee.permission:photography_costs')->group(function () {
        Route::get('/photography-costs', function () {
            return view('employee.systems.photography-costs');
        })->name('employee.photography-costs');
    });

    Route::middleware('employee.permission:customer_communication')->group(function () {
        Route::get('/customer-communication', function () {
            return view('employee.systems.customer-communication');
        })->name('employee.customer-communication');
    });

    Route::middleware('employee.permission:design_follow_up')->group(function () {
        Route::get('/design-follow-up', function () {
            return view('employee.systems.design-follow-up');
        })->name('employee.design-follow-up');
    });

    Route::middleware('employee.permission:montage_follow_up')->group(function () {
        Route::get('/montage-follow-up', function () {
            return view('employee.systems.montage-follow-up');
        })->name('employee.montage-follow-up');
    });

    // الكود المُصحح:

Route::group(['middleware' => 'employee.auth'], function () {
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

});



   