<!-- resources/views/admin/employees/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'إدارة الموظفين')
@section('page-title', 'إدارة الموظفين')
@section('page-subtitle', 'إدارة وعرض جميع موظفي الشركة')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">قائمة الموظفين</h2>
        </div>
        <div>
            <a href="{{ route('admin.employees.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                <i class="fas fa-plus ml-2"></i>
                إضافة موظف جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم، البريد، رقم الموظف..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">القسم</label>
                <select name="department" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الأقسام</option>
                    <option value="financial" {{ request('department') == 'financial' ? 'selected' : '' }}>الشؤون المالية</option>
                    <option value="customers" {{ request('department') == 'customers' ? 'selected' : '' }}>خدمة العملاء</option>
                    <option value="operations" {{ request('department') == 'operations' ? 'selected' : '' }}>العمليات</option>
                    <option value="production" {{ request('department') == 'production' ? 'selected' : '' }}>الإنتاج</option>
                    <option value="design" {{ request('department') == 'design' ? 'selected' : '' }}>التصميم</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الدور</label>
                <select name="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الأدوار</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end space-x-2 space-x-reverse">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    <i class="fas fa-times ml-1"></i> إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($employees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الموظف
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    القسم والمنصب
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الأدوار
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ التوظيف
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($employees as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium text-sm">
                                                        {{ mb_substr($employee->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                                <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employee->department_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->position }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($employee->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas {{ $employee->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>
                                            {{ $employee->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt ml-1"></i>
                                        {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('admin.employees.show', $employee) }}" 
                                               class="text-green-600 hover:text-green-900 p-2 rounded-full hover:bg-green-50 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.employees.edit', $employee) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteEmployee({{ $employee->id }})" 
                                                    class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($employees->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $employees->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين</h3>
                    <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة موظف جديد للنظام</p>
                    <a href="{{ route('admin.employees.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة موظف جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteEmployee(employeeId) {
    confirmDelete('حذف الموظف', 'هل أنت متأكد من حذف هذا الموظف؟ لن تتمكن من التراجع عن هذا الإجراء!')
        .then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/employees/${employeeId}`;
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfField);
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
}
</script>
@endpush
