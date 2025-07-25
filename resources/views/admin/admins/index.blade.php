@extends('admin.layouts.app')

@section('title', 'إدارة المديرين')
@section('page-title', 'إدارة المديرين')
@section('page-subtitle', 'إدارة وعرض جميع مديري النظام')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">قائمة المديرين</h2>
        </div>
        <div>
            <a href="{{ route('admin.admins.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                <i class="fas fa-plus ml-2"></i>
                إضافة مدير جديد
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($admins->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المدير
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الصلاحية
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
                            @foreach($admins as $admin)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium text-sm">
                                                        {{ mb_substr($admin->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                                                @if($admin->phone)
                                                    <div class="text-sm text-gray-500">{{ $admin->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $admin->role == 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            <i class="fas fa-user-shield ml-1"></i>
                                            {{ $admin->role == 'super_admin' ? 'مدير عام' : 'مدير' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $admin->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas {{ $admin->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>
                                            {{ $admin->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt ml-1"></i>
                                        {{ $admin->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            @if(auth('admin')->user()->isSuperAdmin() || auth('admin')->id() == $admin->id)
                                                <a href="{{ route('admin.admins.edit', $admin) }}" 
                                                   class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            
                                            @if(auth('admin')->user()->isSuperAdmin() && auth('admin')->id() != $admin->id)
                                                <button onclick="deleteAdmin({{ $admin->id }})" 
                                                        class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($admins->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $admins->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مديرين</h3>
                    <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة مدير جديد للنظام</p>
                    <a href="{{ route('admin.admins.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة مدير جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteAdmin(adminId) {
    confirmDelete('حذف المدير', 'هل أنت متأكد من حذف هذا المدير؟ لن تتمكن من التراجع عن هذا الإجراء!')
        .then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/admins/${adminId}`;
                
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