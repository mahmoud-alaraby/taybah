@extends('admin.layouts.app')

@section('title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-subtitle', isset($admin) ? 'تعديل بيانات المدير' : 'إضافة مدير جديد إلى النظام')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 w-full">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-red-200 mb-8">
            <div class="bg-gradient-to-r from-red-700 to-red-800 px-8 py-10 sm:px-12 rounded-t-xl">
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <i class="fas fa-user text-white text-2xl"></i>
                    {{ isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد' }}
                </h1>
                <p class="text-red-200 mt-2 text-sm">
                    {{ isset($admin) ? 'تعديل بيانات المدير' : 'إضافة مدير جديد إلى النظام' }}
                </p>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form action="{{ isset($admin) ? route('admin.admins.update', $admin) : route('admin.admins.store') }}" 
                  method="POST" 
                  class="space-y-8"
                  id="adminForm">
                @csrf
                @if(isset($admin))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-8">
                    <div class="space-y-6">
                        {{-- اسم المدير --}}
                        <div>
                            <label for="name" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-user text-red-500 text-xl"></i>
                                الاسم الكامل
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', isset($admin) ? $admin->name : '') }}"
                                class="w-full py-4 px-6 border-2 border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-500 bg-gray-50 rounded-xl text-base transition-colors duration-150 @error('name') border-red-600 ring-2 ring-red-200 @enderror"
                                placeholder="أدخل الاسم الكامل"
                                required
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div>
                            <label for="email" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-envelope text-red-500 text-xl"></i>
                                البريد الإلكتروني
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', isset($admin) ? $admin->email : '') }}"
                                class="w-full py-4 px-6 border-2 border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-500 bg-gray-50 rounded-xl text-base transition-colors duration-150 @error('email') border-red-600 ring-2 ring-red-200 @enderror"
                                placeholder="admin@taiba.com"
                                required
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- رقم الهاتف --}}
                        <div>
                            <label for="phone" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-phone text-red-500 text-xl"></i>
                                رقم الهاتف
                            </label>
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', isset($admin) ? $admin->phone : '') }}"
                                class="w-full py-4 px-6 border-2 border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-500 bg-gray-50 rounded-xl text-base transition-colors duration-150 @error('phone') border-red-600 ring-2 ring-red-200 @enderror"
                                placeholder="05xxxxxxxx"
                            >
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الصلاحية --}}
                        <div>
                            <label for="role" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-user-shield text-red-500 text-xl"></i>
                                الصلاحية
                            </label>
                            <select
                                name="role"
                                id="role"
                                class="w-full py-4 px-6 border-2 border-gray-300 bg-white focus:border-red-400 focus:ring-2 focus:ring-red-500 rounded-xl text-base transition-colors duration-150 @error('role') border-red-600 ring-2 ring-red-200 @enderror"
                                required
                            >
                                <option value="">اختر الصلاحية</option>
                                <option value="super_admin" {{ old('role', isset($admin) ? $admin->role : '') == 'super_admin' ? 'selected' : '' }}>مدير عام</option>
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- كلمة المرور --}}
                        <div>
                            <label for="password" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-lock text-red-500 text-xl"></i>
                                كلمة المرور
                                @if(isset($admin))
                                    <span class="text-gray-500 text-xs ml-2">(اتركها فارغة إذا كنت لا تريد تغييرها)</span>
                                @endif
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="w-full py-4 px-6 border-2 border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-500 bg-gray-50 rounded-xl text-base transition-colors duration-150 @error('password') border-red-600 ring-2 ring-red-200 @enderror"
                                placeholder="أدخل كلمة المرور"
                                {{ !isset($admin) ? 'required' : '' }}
                            >
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div>
                            <label for="password_confirmation" class="flex items-center text-lg font-bold text-gray-700 mb-3 gap-3">
                                <i class="fas fa-lock text-red-500 text-xl"></i>
                                تأكيد كلمة المرور
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full py-4 px-6 border-2 border-gray-300 focus:border-red-400 focus:ring-2 focus:ring-red-500 bg-gray-50 rounded-xl text-base transition-colors duration-150"
                                placeholder="أعد إدخال كلمة المرور"
                                {{ !isset($admin) ? 'required' : '' }}
                            >
                        </div>

                    </div>
                </div>

                <!-- الأزرار -->
                <div class="mt-10 flex flex-col md:flex-row items-center justify-end gap-4">
                    <a href="{{ route('admin.admins.index') }}" 
                       class="bg-white py-3 px-8 border-2 border-gray-300 rounded-xl text-base font-bold text-gray-700 hover:bg-red-50 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-times text-red-500"></i>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="bg-gradient-to-r from-red-600 to-red-700 py-3 px-12 border border-red-600 rounded-xl text-base font-bold text-white hover:from-red-700 hover:to-red-800 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-save"></i>
                        {{ isset($admin) ? 'تحديث' : 'حفظ' }}
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
