<?php
// app/Http/Controllers/Admin/RoleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount(['employees', 'permissions']);
        
        // البحث
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $roles = $query->latest()->paginate(10);
        
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('system_category');
        return view('admin.roles.addedit', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'required|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور مستخدم من قبل',
            'description.required' => 'وصف الدور مطلوب',
            'permissions.required' => 'يجب اختيار صلاحية واحدة على الأقل',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
        ]);

        // إضافة الصلاحيات
        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles.index')
                       ->with('success', 'تم إضافة الدور بنجاح');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'employees']);
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('system_category');
        $role->load('permissions');
        
        return view('admin.roles.addedit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'required|string|max:500',
            'status' => 'required|in:active,inactive',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور مستخدم من قبل',
            'description.required' => 'وصف الدور مطلوب',
            'status.required' => 'الحالة مطلوبة',
            'permissions.required' => 'يجب اختيار صلاحية واحدة على الأقل',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // تحديث الصلاحيات
        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles.index')
                       ->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        // التحقق من عدم وجود موظفين مرتبطين بهذا الدور
        if ($role->employees()->count() > 0) {
            return redirect()->route('admin.roles.index')
                           ->with('error', 'لا يمكن حذف هذا الدور لأنه مرتبط بموظفين');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')
                       ->with('success', 'تم حذف الدور بنجاح');
    }
}