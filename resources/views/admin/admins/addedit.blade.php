@extends('admin.layouts.app')

@section('title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-title', isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد')
@section('page-subtitle', isset($admin) ? 'تعديل بيانات المدير' : 'إضافة مدير جديد إلى النظام')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5 rounded-t-xl">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a4 4 0 108 0 4 4 0 00-8 0zM12 14c-5.523 0-10 2.239-10 5v3"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h1 class="text-xl sm:text-2xl font-bold text-white">
                            {{ isset($admin) ? 'تعديل مدير' : 'إضافة مدير جديد' }}
                        </h1>
                        <p class="text-red-100 text-sm mt-1">
                            {{ isset($admin) ? 'تعديل بيانات المدير' : 'إضافة مدير جديد إلى النظام' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ isset($admin) ? route('admin.admins.update', $admin) : route('admin.admins.store') }}" method="POST" class="p-6 sm:p-8" id="adminForm">
                @csrf
                @if(isset($admin)) @method('PUT') @endif

                <div class="space-y-8">
                    {{-- بيانات المدير --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.79.648 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            بيانات المدير
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- الاسم الكامل --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">الاسم الكامل <span class="text-red-600">*</span></label>
                                <input type="text" name="name" value="{{ old('name', isset($admin) ? $admin->name : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('name') border-red-600 ring-2 ring-red-200 @enderror"
                                       placeholder="أدخل الاسم الكامل" required>
                                @error('name')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- البريد الإلكتروني --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">البريد الإلكتروني <span class="text-red-600">*</span></label>
                                <input type="email" name="email" value="{{ old('email', isset($admin) ? $admin->email : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('email') border-red-600 ring-2 ring-red-200 @enderror"
                                       placeholder="admin@taiba.com" required>
                                @error('email')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            {{-- رقم الهاتف --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">رقم الهاتف</label>
                                <input type="text" name="phone" value="{{ old('phone', isset($admin) ? $admin->phone : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('phone') border-red-600 ring-2 ring-red-200 @enderror"
                                       placeholder="05xxxxxxxx">
                                @error('phone')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- الصلاحية --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">الصلاحية <span class="text-red-600">*</span></label>
                                <select name="role"
                                        class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('role') border-red-600 ring-2 ring-red-200 @enderror"
                                        required>
                                    <option value="">اختر الصلاحية</option>
                                    <option value="super_admin" {{ old('role', isset($admin) ? $admin->role : '') == 'super_admin' ? 'selected' : '' }}>مدير عام</option>
                                </select>
                                @error('role')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- بيانات دخول المدير --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0-.552.447-1 1-1s1 .448 1 1c0 1.104-.895 2-2 2s-2-.896-2-2"/>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                </svg>
                            </div>
                            بيانات الدخول
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- كلمة المرور --}}
                            <div class="space-y-2 relative">
                                <label class="block text-sm font-semibold text-gray-700">كلمة المرور
                                    @if(isset($admin))
                                        <span class="text-gray-500 text-xs ml-2">(اتركها فارغة إذا كنت لا تريد تغييرها)</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="password" name="password"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12 @error('password') border-red-600 ring-2 ring-red-200 @enderror"
                                           placeholder="أدخل كلمة المرور" {{ !isset($admin) ? 'required' : '' }}>
                                    <span onclick="togglePasswordVisibility('password', 'eyePasswordEdit')" id="eyePasswordEdit"
                                          class="absolute left-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400">
                                        <!-- أيقونة عين مغلقة SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-.524.048-1.03.136-1.52m1.663-3.227A9.953 9.953 0 0121.2 9m-8.383 5.238a3 3 0 004.243-4.243"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('password')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- تأكيد كلمة المرور --}}
                            <div class="space-y-2 relative">
                                <label class="block text-sm font-semibold text-gray-700">تأكيد كلمة المرور</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12"
                                           placeholder="أعد إدخال كلمة المرور" {{ !isset($admin) ? 'required' : '' }}>
                                    <span onclick="togglePasswordVisibility('password_confirmation', 'eyePasswordConfirmationEdit')" id="eyePasswordConfirmationEdit"
                                          class="absolute left-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400">
                                        <!-- أيقونة عين مغلقة SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-.524.048-1.03.136-1.52m1.663-3.227A9.953 9.953 0 0121.2 9m-8.383 5.238a3 3 0 004.243-4.243"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 sm:space-x-reverse border-t pt-8 mt-8">
                    <a href="{{ route('admin.admins.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-800 hover:to-red-900 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        {{ isset($admin) ? 'تحديث' : 'حفظ' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputName, iconId) {
    const input = document.getElementsByName(inputName)[0];
    const icon = document.getElementById(iconId);

    const eyeClosedSVG = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-.524.048-1.03.136-1.52m1.663-3.227A9.953 9.953 0 0121.2 9m-8.383 5.238a3 3 0 004.243-4.243"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/></svg>`;
    const eyeOpenSVG = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.543 7-1.275 4.057-5.065 7-9.543 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;

    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = eyeOpenSVG;
    } else {
        input.type = 'password';
        icon.innerHTML = eyeClosedSVG;
    }
}
</script>
@endsection
