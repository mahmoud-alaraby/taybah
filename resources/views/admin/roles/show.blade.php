<!-- resources/views/admin/roles/show.blade.php -->
@extends('admin.layouts.app')

@section('title', 'عرض الدور')
@section('page-title', 'عرض الدور')
@section('page-subtitle', 'عرض تفاصيل الدور: ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- معلومات الدور الأساسية -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-5 space-x-reverse">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-red-500 flex items-center justify-center">
                        <i class="fas fa-user-tag text-white text-3xl"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $role->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $role->description }}</p>
                    <div class="mt-2 flex items-center space-x-4 space-x-reverse text-sm text-gray-500">
                        <span>
                            <i class="fas fa-key ml-1"></i>
                            {{ $role->permissions->count() }} صلاحيات
                        </span>
                        <span>
                            <i class="fas fa-users ml-1"></i>
                            {{ $role->employees->count() }} موظفين
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $role->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>
                            {{ $role->status == 'active' ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('admin.roles.edit', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.roles.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- الصلاحيات -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                <i class="fas fa-key ml-2 text-purple-500"></i>
                الصلاحيات المعينة لهذا الدور
            </h3>
            
            @if($role->permissions->count() > 0)
                @php
                    $groupedPermissions = $role->permissions->groupBy('system_category');
                    $categoryNames = [
                        'financial' => 'الأنظمة المالية',
                        'customers' => 'أنظمة العملاء',  
                        'operations' => 'الأنظمة التشغيلية',
                        'production' => 'أنظمة الإنتاج',
                        'design' => 'أنظمة التصميم',
                        'communication' => 'أنظمة التواصل',
                        'tasks' => 'إدارة المهام',
                        'scheduling' => 'أنظمة الجدولة',
                    ];
                @endphp
                
                <div class="space-y-6">
                    @foreach($groupedPermissions as $category => $permissions)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-folder ml-2 text-red-500"></i>
                                {{ $categoryNames[$category] ?? $category }}
                                <span class="mr-2 text-sm text-gray-500">({{ $permissions->count() }} صلاحيات)</span>
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                    <div class="flex items-center bg-white rounded-md p-3 border border-gray-200">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </div>
                                        <div class="mr-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</p>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-key text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد صلاحيات</h3>
                    <p class="text-sm text-gray-500">لم يتم تعيين أي صلاحيات لهذا الدور</p>
                </div>
            @endif
        </div>
    </div>

    <!-- الموظفين المعينين لهذا الدور -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                <i class="fas fa-users ml-2 text-green-500"></i>
                الموظفين المعينين لهذا الدور
            </h3>
            
            @if($role->employees->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($role->employees as $employee)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium text-sm">
                                            {{ mb_substr($employee->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mr-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $employee->position }}</p>
                                    <p class="text-xs text-gray-500">{{ $employee->employee_id }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.employees.show', $employee) }}" 
                                       class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-external-link-alt text-sm"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- معلومات التعيين -->
                            @php
                                $employeeRole = $employee->pivot ?? null;
                            @endphp
                            @if($employeeRole)
                                <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                    <div class="flex justify-between">
                                        <span>تم التعيين: {{ \Carbon\Carbon::parse($employeeRole->assigned_at)->format('Y-m-d') }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $employee->status == 'active' ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين</h3>
                    <p class="text-sm text-gray-500">لم يتم تعيين أي موظفين لهذا الدور</p>
                </div>
            @endif
        </div>
    </div>

    <!-- معلومات النظام -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-info-circle ml-2 text-gray-500"></i>
                معلومات النظام
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">آخر تحديث</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">إجمالي الصلاحيات</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }} صلاحيات</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">إجمالي الموظفين</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $role->employees->count() }} موظفين</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    @if($role->employees->count() == 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-cogs ml-2 text-red-500"></i>
                    إجراءات الإدارة
                </h3>
                <div class="flex space-x-4 space-x-reverse">
                    <a href="{{ route('admin.roles.edit', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الدور
                    </a>
                    <button onclick="deleteRole({{ $role->id }})" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-trash ml-2"></i>
                        حذف الدور
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle ml-1"></i>
                    يمكن حذف هذا الدور لأنه غير مرتبط بأي موظفين
                </p>
            </div>
        </div>
    @endif
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