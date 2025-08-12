<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        
        // Super admin يمكنه الوصول لكل شيء
        if ($admin->role === 'super_admin') {
            return $next($request);
        }
        
        // إذا كان هناك صلاحية محددة، تحقق منها
        if ($permission) {
            // يمكن إضافة نظام صلاحيات للأدمن لاحقاً
            // حالياً سنسمح لجميع الأدمن بالوصول
        }

        return $next($request);
    }
}