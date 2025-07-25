<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::query();
        
        // البحث
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // فلترة بالصلاحية
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $admins = $query->latest()->paginate(10);
        
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.addedit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,super_admin',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'role.required' => 'الصلاحية مطلوبة',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'تم إضافة المدير بنجاح');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.addedit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,super_admin',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'role.required' => 'الصلاحية مطلوبة',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')->with('success', 'تم تحديث المدير بنجاح');
    }

    public function destroy(Admin $admin)
    {
        // منع حذف الحساب الحالي
        if ($admin->id == auth('admin')->id()) {
            return redirect()->route('admin.admins.index')->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'تم حذف المدير بنجاح');
    }
}