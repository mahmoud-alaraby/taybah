<!-- resources/views/admin/employees/create.blade.php -->
@extends('admin.layouts.app')

@section('title', 'إضافة موظف جديد')
@section('page-title', 'إضافة موظف جديد')
@section('page-subtitle', 'إضافة موظف جديد إلى النظام')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- معلومات شخصية -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الشخصية</h3>
                    </div>
                    
                    <!-- اسم الموظف -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user ml-1"></i>
                            الاسم الكامل
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('name') border-red-300 @enderror"
                               placeholder="أدخل الاسم الكامل"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- البريد الإلكتروني -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope ml-1"></i>
                            البريد الإلكتروني
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('email') border-red-300 @enderror"
                               placeholder="employee@taiba.com"
                               required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- رقم الهاتف -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone ml-1"></i>
                            رقم الهاتف
                        </label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('phone') border-red-300 @enderror"
                               placeholder="05xxxxxxxx">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- رقم الموظف -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-id-card ml-1"></i>
                            رقم الموظف
                        </label>
                        <input type="text" 
                               name="employee_id" 
                               id="employee_id" 
                               value="{{ old('employee_id') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('employee_id') border-red-300 @enderror"
                               placeholder="EMP001"
                               required>
                        @error('employee_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- معلومات وظيفية -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المعلومات الوظيفية</h3>
                    </div>
                    
                    <!-- القسم -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-building ml-1"></i>
                            القسم
                        </label>
                        <select name="department" 
                                id="department"
                                class="mt-1 block w-full py-3 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('department') border-red-300 @enderror"
                                required>
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
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-briefcase ml-1"></i>
                            المنصب
                        </label>
                        <input type="text" 
                               name="position" 
                               id="position" 
                               value="{{ old('position') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('position') border-red-300 @enderror"
                               placeholder="مدير، موظف، أخصائي..."
                               required>
                        @error('position')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- الراتب -->
                    <div>
                        <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-money-bill-wave ml-1"></i>
                            الراتب (اختياري)
                        </label>
                        <input type="number" 
                               name="salary" 
                               id="salary" 
                               value="{{ old('salary') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('salary') border-red-300 @enderror"
                               placeholder="0.00"
                               step="0.01"
                               min="0">
                        @error('salary')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- تاريخ التوظيف -->
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt ml-1"></i>
                            تاريخ التوظيف
                        </label>
                        <input type="date" 
                               name="hire_date" 
                               id="hire_date" 
                               value="{{ old('hire_date', date('Y-m-d')) }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('hire_date') border-red-300 @enderror"
                               required>
                        @error('hire_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- معلومات الدخول -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الدخول</h3>
                    </div>
                    
                    <!-- كلمة المرور -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock ml-1"></i>
                            كلمة المرور
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('password') border-red-300 @enderror"
                               placeholder="أدخل كلمة المرور"
                               required>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- تأكيد كلمة المرور -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock ml-1"></i>
                            تأكيد كلمة المرور
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4"
                               placeholder="أعد إدخال كلمة المرور"
                               required>
                    </div>
                    
                    <!-- الأدوار والصلاحيات -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">الأدوار والصلاحيات</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($roles as $role)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="role_{{ $role->id }}" 
                                               name="roles[]" 
                                               type="checkbox" 
                                               value="{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                                    </div>
                                    <div class="mr-3 text-sm">
                                        <label for="role_{{ $role->id }}" class="font-medium text-gray-700">
                                            {{ $role->name }}
                                        </label>
                                        <p class="text-gray-500">{{ $role->description }}</p>
                                        <p class="text-xs text-gray-400">{{ $role->permissions->count() }} صلاحيات</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- الأزرار -->
                <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
                    <a href="{{ route('admin.employees.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-times ml-1"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-save ml-1"></i>
                        حفظ الموظف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection