<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }

        $employee = Auth::guard('employee')->user();
        
        if (!$employee->hasPermission($permission)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
