<?php
// app/Http/Controllers/Employee/AuthController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('employee.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('employee')->attempt($credentials, $request->remember)) {
            $employee = Auth::guard('employee')->user();
            
            if (!$employee->isActive()) {
                Auth::guard('employee')->logout();
                return back()->withErrors(['email' => 'حسابك غير مفعل']);
            }

            // تحديث آخر دخول
            $employee->update(['last_login_at' => now()]);
            
            return redirect()->intended(route('employee.dashboard'));
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    }

    public function logout()
    {
        Auth::guard('employee')->logout();
        return redirect()->route('employee.login');
    }
}
