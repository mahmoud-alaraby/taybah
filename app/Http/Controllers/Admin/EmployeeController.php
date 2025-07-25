<?php
// app/Http/Controllers/Admin/EmployeeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['roles']);
        
        // البحث
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // فلترة بالقسم
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // فلترة بالدور
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('role_id', $request->role);
            });
        }
        
        $employees = $query->latest()->paginate(10);
        $roles = Role::active()->get();
        
        return view('admin.employees.index', compact('employees', 'roles'));
    }

    public function create()
    {
        $roles = Role::active()->get();
        $departments = $this->getDepartments();
        
        return view('admin.employees.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'required|string|max:50|unique:employees',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'employee_id.required' => 'رقم الموظف مطلوب',
            'employee_id.unique' => 'رقم الموظف مستخدم من قبل',
            'department.required' => 'القسم مطلوب',
            'position.required' => 'المنصب مطلوب',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'status' => 'active',
        ]);

        // تعيين الأدوار
        foreach ($request->roles as $roleId) {
            $employee->employeeRoles()->create([
                'role_id' => $roleId,
                'assigned_by' => auth('admin')->id(),
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('admin.employees.index')
                       ->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function show(Employee $employee)
    {
        $employee->load(['roles.permissions', 'employeeRoles.assignedBy']);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $roles = Role::active()->get();
        $departments = $this->getDepartments();
        $employee->load('roles');
        
        return view('admin.employees.edit', compact('employee', 'roles', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('employees')->ignore($employee->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('employees')->ignore($employee->id)],
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'employee_id.required' => 'رقم الموظف مطلوب',
            'employee_id.unique' => 'رقم الموظف مستخدم من قبل',
            'department.required' => 'القسم مطلوب',
            'position.required' => 'المنصب مطلوب',
            'hire_date.required' => 'تاريخ التوظيف مطلوب',
            'status.required' => 'الحالة مطلوبة',
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        // تحديث الأدوار
        $employee->employeeRoles()->delete();
        foreach ($request->roles as $roleId) {
            $employee->employeeRoles()->create([
                'role_id' => $roleId,
                'assigned_by' => auth('admin')->id(),
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('admin.employees.index')
                       ->with('success', 'تم تحديث الموظف بنجاح');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
                       ->with('success', 'تم حذف الموظف بنجاح');
    }

    private function getDepartments(): array
    {
        return [
            'financial' => 'الشؤون المالية',
            'customers' => 'خدمة العملاء',
            'operations' => 'العمليات',
            'production' => 'الإنتاج',
            'design' => 'التصميم',
            'communication' => 'التواصل',
            'tasks' => 'إدارة المهام',
            'scheduling' => 'الجدولة',
        ];
    }
}

