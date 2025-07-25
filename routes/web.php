<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->group(base_path('routes/admin.php'));

// Employee Routes
Route::prefix('employee')->group(base_path('routes/employee.php'));