<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTrackingAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }

        $employee = Auth::guard('employee')->user();
        
        // التحقق من صلاحية الوصول لنظام متابعة المشاريع
        if (!$employee->hasPermission('project_tracking')) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول إلى نظام متابعة المشاريع'
            ], 403);
        }

        return $next($request);
    }
}