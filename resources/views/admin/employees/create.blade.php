@extends('admin.layouts.app')

@section('title', 'إضافة موظف جديد')
@section('page-title', 'إضافة موظف جديد')
@section('page-subtitle', 'إضافة موظف جديد إلى النظام')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 w-full">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-700 to-red-800 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">إضافة موظف جديد</h1>
                            <p class="text-red-100 text-sm mt-1">إضافة موظف جديد إلى النظام</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('admin.employees.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- اسم الموظف -->
                    <div class="space-y-2">
                        <label for="name" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-user text-red-600 text-lg"></i>
                            الاسم الكامل
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name') }}"
                               class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-600 ring-2 ring-red-200 @enderror"
                               placeholder="أدخل الاسم الكامل" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="space-y-2">
                        <label for="email" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-envelope text-red-600 text-lg"></i>
                            البريد الإلكتروني
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('email') border-red-600 ring-2 ring-red-200 @enderror"
                               placeholder="employee@taiba.com" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="space-y-2">
                        <label for="phone" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-phone text-red-600 text-lg"></i>
                            رقم الهاتف
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone') }}"
                               class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('phone') border-red-600 ring-2 ring-red-200 @enderror"
                               placeholder="05xxxxxxxx">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الموظف -->
                    <div class="space-y-2">
                        <label for="employee_id" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-id-card text-red-600 text-lg"></i>
                            رقم الموظف
                        </label>
                        <input type="text"
                               name="employee_id"
                               id="employee_id"
                               value="{{ old('employee_id') }}"
                               class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('employee_id') border-red-600 ring-2 ring-red-200 @enderror"
                               placeholder="EMP001" required>
                        @error('employee_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-bold text-black mb-4 flex items-center gap-2">
                        <i class="fas fa-briefcase text-black"></i>
                        المعلومات الوظيفية
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- القسم -->
                        <div class="space-y-2">
                            <label for="department" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-building text-red-600 text-lg"></i>
                                القسم
                            </label>
                            <select name="department" id="department"
                                    class="w-full py-3 px-5 border border-gray-300 bg-white rounded-lg text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('department') border-red-600 ring-2 ring-red-200 @enderror" required>
                                <option value="">اختر القسم</option>
                                @foreach($departments as $key => $name)
                                    <option value="{{ $key }}" {{ old('department') == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- المنصب -->
                        <div class="space-y-2">
                            <label for="position" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-user-tag text-red-600 text-lg"></i>
                                المنصب
                            </label>
                            <input type="text"
                                   name="position"
                                   id="position"
                                   value="{{ old('position') }}"
                                   class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('position') border-red-600 ring-2 ring-red-200 @enderror"
                                   placeholder="مدير، موظف، أخصائي..." required>
                            @error('position')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الراتب -->
                        <div class="space-y-2">
                            <label for="salary" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-money-bill-alt text-red-600 text-lg"></i>
                                الراتب (اختياري)
                            </label>
                            <input type="number"
                                   name="salary"
                                   id="salary"
                                   value="{{ old('salary') }}"
                                   class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('salary') border-red-600 ring-2 ring-red-200 @enderror"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0">
                            @error('salary')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تاريخ التوظيف -->
                        <div class="space-y-2">
                            <label for="hire_date" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-calendar-alt text-red-600 text-lg"></i>
                                تاريخ التوظيف
                            </label>
                            <input type="date"
                                   name="hire_date"
                                   id="hire_date"
                                   value="{{ old('hire_date', date('Y-m-d')) }}"
                                   class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('hire_date') border-red-600 ring-2 ring-red-200 @enderror"
                                   required>
                            @error('hire_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-bold text-black mb-4 flex items-center gap-2">
                        <i class="fas fa-key text-black"></i>
                        معلومات الدخول
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- كلمة المرور -->
                        <div class="space-y-2">
                            <label for="password" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-lock text-red-600 text-lg"></i>
                                كلمة المرور
                            </label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('password') border-red-600 ring-2 ring-red-200 @enderror"
                                   placeholder="أدخل كلمة المرور" required>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تأكيد كلمة المرور -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-lock text-red-600 text-lg"></i>
                                تأكيد كلمة المرور
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="w-full py-3 px-5 border border-gray-300 rounded-lg bg-gray-50 text-base transition focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="أعد إدخال كلمة المرور" required>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-bold text-black mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-black"></i>
                        الأدوار والصلاحيات
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <div class="flex items-start space-x-2 space-x-reverse">
                                <input id="role_{{ $role->id }}"
                                       name="roles[]"
                                       type="checkbox"
                                       value="{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                       class="h-5 w-5 text-red-500 focus:ring-red-500 border-gray-300 rounded mt-1">
                                <div>
                                    <label for="role_{{ $role->id }}" class="font-medium text-gray-700 cursor-pointer">
                                        {{ $role->name }}
                                    </label>
                                    <div class="text-xs text-gray-500">{{ $role->description }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="mt-10 flex flex-col md:flex-row items-center justify-end gap-4">
                    <a href="{{ route('admin.employees.index') }}"
                       class="bg-white py-3 px-8 border-2 border-gray-300 rounded-xl text-base font-bold text-gray-700 hover:bg-red-50 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-times text-red-500"></i>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="bg-gradient-to-l from-red-600 to-red-500 py-3 px-12 border border-red-600 rounded-xl text-base font-bold text-white hover:from-red-700 hover:to-red-600 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-save"></i>
                        حفظ الموظف
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
