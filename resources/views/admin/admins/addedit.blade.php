@extends('admin.layouts.app')

@section('title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-subtitle', isset($admin) ? 'تعديل بيانات المدير' : 'إضافة مدير جديد إلى النظام')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ isset($admin) ? route('admin.admins.update', $admin) : route('admin.admins.store') }}" method="POST">
                @csrf
                @if(isset($admin))
                    @method('PUT')
                @endif
                
                <div class="space-y-6">
                    <!-- اسم المدير -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user ml-1"></i>
                            الاسم الكامل
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', isset($admin) ? $admin->name : '') }}"
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
                               value="{{ old('email', isset($admin) ? $admin->email : '') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('email') border-red-300 @enderror"
                               placeholder="admin@taiba.com"
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
                               value="{{ old('phone', isset($admin) ? $admin->phone : '') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('phone') border-red-300 @enderror"
                               placeholder="05xxxxxxxx">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- الصلاحية -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-shield ml-1"></i>
                            الصلاحية
                        </label>
                        <select name="role" 
                                id="role"
                                class="mt-1 block w-full py-3 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('role') border-red-300 @enderror"
                                required>
                            <option value="">اختر الصلاحية</option>
                            {{-- <option value="admin" {{ old('role', isset($admin) ? $admin->role : '') == 'admin' ? 'selected' : '' }}>
                                مدير
                            </option> --}}
                            <option value="super_admin" {{ old('role', isset($admin) ? $admin->role : '') == 'super_admin' ? 'selected' : '' }}>
                                مدير عام
                            </option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- كلمة المرور -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-lock ml-1"></i>
                            كلمة المرور
                            @if(isset($admin))
                                <span class="text-gray-500 text-xs">(اتركها فارغة إذا كنت لا تريد تغييرها)</span>
                            @endif
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('password') border-red-300 @enderror"
                               placeholder="أدخل كلمة المرور"
                               {{ !isset($admin) ? 'required' : '' }}>
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
                               {{ !isset($admin) ? 'required' : '' }}>
                    </div>
                </div>
                
                <!-- الأزرار -->
                <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
                    <a href="{{ route('admin.admins.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-times ml-1"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-save ml-1"></i>
                        {{ isset($admin) ? 'تحديث' : 'حفظ' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection