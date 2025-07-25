<!-- resources/views/admin/roles/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'إدارة الأدوار')
@section('page-title', 'إدارة الأدوار')
@section('page-subtitle', 'إدارة وعرض جميع أدوار النظام')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">قائمة الأدوار</h2>
        </div>
        <div>
            <a href="{{ route('admin.roles.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                <i class="fas fa-plus ml-2"></i>
                إضافة دور جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو الوصف..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div class="flex justify-end items-end space-x-2 space-x-reverse">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    <i class="fas fa-times ml-1"></i> إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($roles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الدور
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الصلاحيات
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الموظفين
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الإنشاء
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($roles as $role)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                    <i class="fas fa-user-tag text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $role->description }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-key text-blue-500 ml-2"></i>
                                            <span class="text-sm font-medium text-gray-900">{{ $role->permissions_count }}</span>
                                            <span class="text-sm text-gray-500 mr-1">صلاحيات</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-users text-green-500 ml-2"></i>
                                            <span class="text-sm font-medium text-gray-900">{{ $role->employees_count }}</span>
                                            <span class="text-sm text-gray-500 mr-1">موظفين</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas {{ $role->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>
                                            {{ $role->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt ml-1"></i>
                                        {{ $role->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('admin.roles.show', $role) }}" 
                                               class="text-green-600 hover:text-green-900 p-2 rounded-full hover:bg-green-50 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($role->employees_count == 0)
                                                <button onclick="deleteRole({{ $role->id }})" 
                                                        class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-gray-400 p-2 cursor-not-allowed" title="لا يمكن حذف دور مرتبط بموظفين">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($roles->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $roles->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-user-tag text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد أدوار</h3>
                    <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة دور جديد للنظام</p>
                    <a href="{{ route('admin.roles.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة دور جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteRole(roleId) {
    confirmDelete('حذف الدور', 'هل أنت متأكد من حذف هذا الدور؟ لن تتمكن من التراجع عن هذا الإجراء!')
        .then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/roles/${roleId}`;
                
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
