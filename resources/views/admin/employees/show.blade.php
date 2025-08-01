@extends('admin.layouts.app')

@section('title', 'عرض الموظف')
@section('page-title', 'عرض الموظف')
@section('page-subtitle', 'عرض تفاصيل الموظف: ' . $employee->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 flex items-center">
                <svg class="w-10 h-10 mr-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                تفاصيل الموظف
            </h1>
            <div class="text-slate-600 mt-2 text-lg">{{ $employee->position }} - {{ $employee->department_name }}</div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-5 sm:mt-0">
            <a href="{{ route('admin.employees.edit', $employee) }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
            <a href="{{ route('admin.employees.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

        <!-- الكارت الشخصي -->
        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-xl font-bold">المعلومات الشخصية</h3>
            </div>
            <div class="space-y-3 font-medium">
                <div><span class="opacity-80">الاسم الكامل:</span> <span class="ml-2 text-lg font-bold">{{ $employee->name }}</span></div>
                <div><span class="opacity-80">البريد الإلكتروني:</span> <span class="ml-2">{{ $employee->email }}</span></div>
                <div><span class="opacity-80">رقم الهاتف:</span> <span class="ml-2">{{ $employee->phone ?: 'غير محدد' }}</span></div>
                <div><span class="opacity-80">رقم الموظف:</span> <span class="ml-2">{{ $employee->employee_id }}</span></div>
                <div><span class="opacity-80">الحالة:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ml-1
                        {{ $employee->status == 'active'
                            ? 'bg-green-50 text-green-700'
                            : 'bg-red-100 text-red-700' }}">
                        @if($employee->status == 'active')
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"/></svg>
                            نشط
                        @else
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11V5a1 1 0 10-2 0v2a1 1 0 002 0zm-1 2a1 1 0 00-.993.883L9 10v4a1 1 0 001.993.117L11 14v-4a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            غير نشط
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- الكارت الوظيفي -->
        <div class="bg-gradient-to-br from-sky-600 to-blue-500 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 21v.01"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v.01"/>
                </svg>
                <h3 class="text-xl font-bold">المعلومات الوظيفية</h3>
            </div>
            <div class="space-y-3 font-medium">
                <div><span class="opacity-80">القسم:</span> <span class="ml-2">{{ $employee->department_name }}</span></div>
                <div><span class="opacity-80">المنصب:</span> <span class="ml-2">{{ $employee->position }}</span></div>
                <div><span class="opacity-80">الراتب:</span> <span class="ml-2">{{ $employee->salary ? number_format($employee->salary, 2) . ' ريال' : 'غير محدد' }}</span></div>
                <div><span class="opacity-80">تاريخ التوظيف:</span> <span class="ml-2">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}</span></div>
            </div>
        </div>
    </div>

    <!-- كارت الأدوار والصلاحيات -->
    <div class="bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl p-8 text-white shadow-xl mb-8">
        <div class="flex items-center mb-6">
            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2l4-4"/><path d="M12 20v-6"/><path d="M4 12a8 8 0 1016 0a8 8 0 00-16 0z" stroke-width="2" stroke="currentColor" fill="none"/>
            </svg>
            <h3 class="text-xl font-bold">الأدوار والصلاحيات</h3>
        </div>
        @if($employee->roles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($employee->roles as $role)
                    <div class="bg-white bg-opacity-20 border border-white border-opacity-40 rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 text-blue-200 ml-2" fill="currentColor" viewBox="0 0 20 20"><path d="M7 10V8a3 3 0 016 0v2a2 2 0 002 2v1a2 2 0 01-2 2H7a2 2 0 01-2-2v-1a2 2 0 002-2z"/></svg>
                            <div class="font-bold">{{ $role->name }}</div>
                            <span class="px-2 py-0.5 rounded bg-pink-100 text-xs text-pink-800 ml-auto">{{ $role->permissions->count() }} صلاحية</span>
                        </div>
                        <div class="text-xs text-white opacity-80 mb-3">{{ $role->description }}</div>
                        @if($role->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($role->permissions as $permission)
                                    <div class="flex items-center text-xs bg-white bg-opacity-30 rounded-md p-2">
                                        <svg class="w-4 h-4 text-green-200 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"/>
                                        </svg>
                                        {{ $permission->display_name }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center text-sm opacity-70">لا توجد أدوار لهذا الموظف</div>
        @endif
    </div>

    <!-- كارت معلومات النظام -->
    <div class="bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center mb-6">
            <svg class="w-8 h-8 mr-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-bold">معلومات النظام</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white bg-opacity-10 rounded-xl p-4 text-center">
                <label class="text-sm opacity-80 block mb-2">معرّف الموظف</label>
                <p class="text-3xl font-bold">#{{ $employee->id }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">تاريخ الإنشاء</label>
                <p class="text-lg font-semibold">{{ $employee->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">آخر تحديث</label>
                <p class="text-lg font-semibold">{{ $employee->updated_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">آخر دخول</label>
                <p class="text-lg font-semibold">
                    {{ $employee->last_login_at ? $employee->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول بعد' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
