<?php
// app/Http/Middleware/EmployeeAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }

        $employee = Auth::guard('employee')->user();
        if (!$employee->isActive()) {
            Auth::guard('employee')->logout();
            return redirect()->route('employee.login')
                           ->withErrors(['error' => 'حسابك غير مفعل']);
        }

        return $next($request);
    }
}
