<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerResponseCategory;
use Illuminate\Http\Request;

class AdminCustomerResponseCategoryController extends Controller
{
    // عرض قائمة التصنيفات
    public function index()
    {
        $categories = CustomerResponseCategory::all();
        return view('admin.customer-response.categories.index', compact('categories'));
    }

    // نموذج إنشاء تصنيف جديد
    public function create()
    {
        return view('admin.customer-response.categories.create');
    }

    // تخزين تصنيف جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:customer_response_categories,name',
            'icon' => 'nullable|string|max:100',
        ]);

        CustomerResponseCategory::create($request->only('name', 'icon'));

        return redirect()->route('admin.customer-response.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    // نموذج تعديل تصنيف موجود
    public function edit(CustomerResponseCategory $category)
    {
        return view('admin.customer-response.categories.edit', compact('category'));
    }

    // تحديث تصنيف
    public function update(Request $request, CustomerResponseCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:customer_response_categories,name,'.$category->id,
            'icon' => 'nullable|string|max:100',
        ]);

        $category->update($request->only('name', 'icon'));

        return redirect()->route('admin.customer-response.categories.index')
            ->with('success', 'تم تعديل التصنيف بنجاح');
    }

    // حذف تصنيف
    public function destroy(CustomerResponseCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.customer-response.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
