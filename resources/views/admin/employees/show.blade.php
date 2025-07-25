<!-- resources/views/admin/employees/show.blade.php -->
@extends('admin.layouts.app')

@section('title', 'عرض الموظف')
@section('page-title', 'عرض الموظف')
@section('page-subtitle', 'عرض تفاصيل الموظف: ' . $employee->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- معلومات الموظف الأساسية -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-5 space-x-reverse">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-gray-600 font-medium text-2xl">
                            {{ mb_substr($employee->name, 0, 1) }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $employee->position }} - {{ $employee->department_name }}</p>
                    <p class="text-sm text-gray-500">رقم الموظف: {{ $employee->employee_id }}</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('admin.employees.edit', $employee) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.employees.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- المعلومات الشخصية -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-user ml-2 text-blue-500"></i>
                    المعلومات الشخصية
                </h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">البريد الإلكتروني</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">رقم الهاتف</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone ?: 'غير محدد' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">الحالة</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $employee->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} ml-1"></i>
                                {{ $employee->status == 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- المعلومات الوظيفية -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-briefcase ml-2 text-green-500"></i>
                    المعلومات الوظيفية
                </h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">القسم</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->department_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">المنصب</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->position }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">الراتب</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $employee->salary ? number_format($employee->salary, 2) . ' ريال' : 'غير محدد' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">تاريخ التوظيف</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- الأدوار والصلاحيات -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-user-tag ml-2 text-purple-500"></i>
                الأدوار والصلاحيات
            </h3>
            
            @if($employee->roles->count() > 0)
                <div class="space-y-4">
                    @foreach($employee->roles as $role)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center ml-3">
                                        <i class="fas fa-user-tag text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900">{{ $role->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $role->description }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $role->permissions->count() }} صلاحيات
                                </div>
                            </div>
                            
                            <!-- صلاحيات هذا الدور -->
                            @if($role->permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
                                    @foreach($role->permissions as $permission)
                                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 rounded-md p-2">
                                            <i class="fas fa-check text-green-500 ml-2"></i>
                                            {{ $permission->display_name }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- معلومات التعيين -->
                            @php
                                $employeeRole = $employee->employeeRoles->where('role_id', $role->id)->first();
                            @endphp
                            @if($employeeRole)
                                <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                                    <div class="flex justify-between">
                                        <span>تم التعيين في: {{ $employeeRole->assigned_at->format('Y-m-d H:i') }}</span>
                                        @if($employeeRole->assignedBy)
                                            <span>بواسطة: {{ $employeeRole->assignedBy->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-user-times text-4xl text-gray-400 mb-3"></i>
                    <p class="text-sm text-gray-500">لم يتم تعيين أي أدوار لهذا الموظف</p>
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
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">آخر تحديث</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">آخر دخول</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $employee->last_login_at ? $employee->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول بعد' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">رابط الصورة الشخصية</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="{{ $employee->avatar_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            عرض الصورة
                        </a>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

