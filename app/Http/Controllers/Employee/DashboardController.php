<?php

// app/Http/Controllers/Employee/DashboardController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth('employee')->user();
        $permissions = $employee->permissions();
        
        return view('employee.dashboard', compact('employee', 'permissions'));
    }
}
