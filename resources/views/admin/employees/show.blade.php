{{-- resources/views/admin/employees/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'عرض الموظف')
@section('page-title', 'عرض الموظف')
@section('page-subtitle', 'عرض تفاصيل الموظف: ' . $employee->name)

@section('content')
<div class="bg-gray-50 min-h-screen py-6" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7"/>
                        </svg>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white">تفاصيل الموظف</h1>
                            <p class="text-red-100 text-sm mt-1">{{ $employee->position }} - {{ $employee->department_name }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-orange-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-10 5l5-5"/>
                            </svg>
                            تعديل
                        </a>
                        <a href="{{ route('admin.employees.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-orange-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Info Card --}}
        <div class="bg-gradient-to-r from-red-50 to-red-50 rounded-xl p-8 border border-red-200 mb-6 text-gray-900">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7"/>
                </svg>
                <h3 class="text-xl font-semibold">المعلومات الشخصية</h3>
            </div>
            <div class="space-y-3 font-medium">
                <div><span class="opacity-80">الاسم الكامل:</span> <span class="ml-2 font-bold text-lg">{{ $employee->name }}</span></div>
                <div><span class="opacity-80">البريد الإلكتروني:</span> <span class="ml-2">{{ $employee->email }}</span></div>
                <div><span class="opacity-80">رقم الهاتف:</span> <span class="ml-2">{{ $employee->phone ?? 'غير محدد' }}</span></div>
                <div><span class="opacity-80">رقم الموظف:</span> <span class="ml-2">{{ $employee->employee_id }}</span></div>
                <div>
                    <span class="opacity-80">الحالة:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ml-2 {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        @if($employee->status == 'active')
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16 5l-6 6-2-2"/></svg>
                            نشط
                        @else
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11v2h-2v-2h2zm-1 3h1v4h-1v-4z" clip-rule="evenodd"/>
                            </svg>
                            غير نشط
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Job Info Card --}}
        <div class="bg-gradient-to-r from-sky-50 to-sky-50 rounded-xl p-8 border border-sky-200 mb-6 text-gray-900">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 ml-2 text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 17v-3a4 4 0 00-4-4H8a4 4 0 00-4 4v3"/>
                    <circle cx="12" cy="7" r="4" stroke-width="2"/>
                </svg>
                <h3 class="text-xl font-semibold">المعلومات الوظيفية</h3>
            </div>
            <div class="space-y-3 font-medium">
                <div><span class="opacity-80">القسم:</span> <span class="ml-2">{{ $employee->department_name }}</span></div>
                <div><span class="opacity-80">المنصب:</span> <span class="ml-2">{{ $employee->position }}</span></div>
                <div><span class="opacity-80">الراتب:</span> <span class="ml-2">{{ $employee->salary ? number_format($employee->salary, 2) . ' ريال' : 'غير محدد' }}</span></div>
                <div><span class="opacity-80">تاريخ التوظيف:</span> <span class="ml-2">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}</span></div>
            </div>
        </div>

        {{-- Roles and Permissions Card --}}
        <div class="bg-gradient-to-r from-purple-50 to-purple-50 rounded-xl p-8 border border-purple-200 mb-6 text-gray-900">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 ml-2 text-purple-700" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4"/>
                    <path d="M4 12a8 8 0 1116 0 8 8 0 01-16 0z"/>
                </svg>
                <h3 class="text-xl font-semibold">الأدوار والصلاحيات</h3>
            </div>
            @if ($employee->roles->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach ($employee->roles as $role)
                    <div class="bg-white bg-opacity-20 border border-white border-opacity-40 rounded-xl p-4 overflow-hidden">
                        <div class="flex items-center mb-2">
                            <svg class="w-6 h-6 ml-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 10V8a3 3 0 016 0v2"/>
                                <path d="M17 18h-6v-6"/>
                            </svg>
                            <div class="font-semibold truncate">{{ $role->name }}</div>
                            <span class="px-2 py-0.5 ml-auto rounded bg-pink-200 text-pink-800 font-medium text-xs">
                                {{ $role->permissions->count() }} صلاحية
                            </span>
                        </div>
                        <div class="text-xs text-gray-800 mb-3 opacity-90">
                            {{ $role->description }}
                        </div>
                        @if ($role->permissions->count())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($role->permissions as $permission)
                            <div class="flex items-center bg-white rounded-md p-2 text-xs cursor-default select-none text-green-700">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16 5l-6 6-2-2"/>
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
                <div class="py-8 text-center opacity-70">لا توجد أدوار لهذا الموظف</div>
            @endif
        </div>

        {{-- System Info Card --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-50 rounded-xl p-8 border border-gray-200 mb-6 text-gray-900">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 ml-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m2 0h-1"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-semibold">معلومات النظام</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white bg-opacity-10 rounded-xl p-4 text-center">
                    <label class="block text-sm opacity-80 mb-2">معرّف الموظف</label>
                    <p class="text-3xl font-bold">#{{ $employee->id }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-xl p-4">
                    <label class="block text-sm opacity-80 mb-2">تاريخ الإنشاء</label>
                    <p class="text-gray-900">{{ $employee->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-xl p-4">
                    <label class="block text-sm opacity-80 mb-2">آخر تحديث</label>
                    <p class="text-gray-900">{{ $employee->updated_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-xl p-4">
                    <label class="block text-sm opacity-80 mb-2">آخر دخول</label>
                    <p class="text-gray-900">{{ $employee->last_login_at ? $employee->last_login_at->format('Y-m-d H:i') : 'لم يسجل دخول بعد' }}</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 flex flex-wrap gap-4 justify-center">
            <a href="{{ route('admin.employees.edit', $employee) }}"
               class="inline-flex items-center px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-transform transform hover:scale-105">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل الموظف
            </a>

            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center px-6 py-4 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white rounded-xl shadow-lg transition-transform transform hover:scale-105"
                        onclick="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
                    <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف الموظف
                </button>
            </form>

            <a href="{{ route('admin.employees.index') }}"
               class="inline-flex items-center px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl shadow-lg transition-transform transform hover:scale-105">
                <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للقائمة
            </a>
        </div>

    </div>
</div>
@endsection
