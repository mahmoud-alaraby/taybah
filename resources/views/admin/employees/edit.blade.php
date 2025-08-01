@extends('admin.layouts.app')

@section('title', 'تعديل موظف')
@section('page-title', 'تعديل موظف')
@section('page-subtitle', 'تعديل بيانات الموظف: ' . $employee->name)

@section('content')
<div class="max-w-3xl mx-auto w-full">
    <div class="bg-white border-2 border-gray-200 rounded-2xl mt-8">
        <div class="px-6 py-10 sm:px-10">
            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- اسم الموظف -->
                    <div>
                        <label for="name" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-user text-red-600 text-lg"></i>
                            الاسم الكامل
                        </label>
                        <input type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $employee->name) }}"
                            class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('name') border-red-400 @enderror"
                            placeholder="أدخل الاسم الكامل" required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div>
                        <label for="email" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-envelope text-red-600 text-lg"></i>
                            البريد الإلكتروني
                        </label>
                        <input type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $employee->email) }}"
                            class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('email') border-red-400 @enderror"
                            placeholder="employee@taiba.com" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الهاتف -->
                    <div>
                        <label for="phone" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-phone text-red-600 text-lg"></i>
                            رقم الهاتف
                        </label>
                        <input type="text"
                            name="phone"
                            id="phone"
                            value="{{ old('phone', $employee->phone) }}"
                            class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('phone') border-red-400 @enderror"
                            placeholder="05xxxxxxxx">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- رقم الموظف -->
                    <div>
                        <label for="employee_id" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                            <i class="fas fa-id-card text-red-600 text-lg"></i>
                            رقم الموظف
                        </label>
                        <input type="text"
                            name="employee_id"
                            id="employee_id"
                            value="{{ old('employee_id', $employee->employee_id) }}"
                            class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('employee_id') border-red-400 @enderror"
                            placeholder="EMP001" required>
                        @error('employee_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-briefcase text-red-500"></i>
                        المعلومات الوظيفية
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- القسم -->
                        <div>
                            <label for="department" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-building text-red-600 text-lg"></i>
                                القسم
                            </label>
                            <select name="department" id="department"
                                    class="w-full py-3 px-5 border-2 border-gray-300 bg-white rounded-lg focus:border-gray-500 focus:ring-1 focus:ring-gray-500 text-base transition @error('department') border-red-400 @enderror" required>
                                <option value="">اختر القسم</option>
                                @foreach($departments as $key => $name)
                                    <option value="{{ $key }}" {{ old('department', $employee->department) == $key ? 'selected' : '' }}>
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
                            <label for="position" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-user-tag text-red-600 text-lg"></i>
                                المنصب
                            </label>
                            <input type="text"
                                name="position"
                                id="position"
                                value="{{ old('position', $employee->position) }}"
                                class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('position') border-red-400 @enderror"
                                placeholder="مدير، موظف، أخصائي..." required>
                            @error('position')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الراتب -->
                        <div>
                            <label for="salary" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-money-bill-alt text-red-600 text-lg"></i>
                                الراتب (اختياري)
                            </label>
                            <input type="number"
                                name="salary"
                                id="salary"
                                value="{{ old('salary', $employee->salary) }}"
                                class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('salary') border-red-400 @enderror"
                                placeholder="0.00"
                                step="0.01"
                                min="0">
                            @error('salary')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تاريخ التوظيف -->
                        <div>
                            <label for="hire_date" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-calendar-alt text-red-600 text-lg"></i>
                                تاريخ التوظيف
                            </label>
                            <input type="date"
                                name="hire_date"
                                id="hire_date"
                                value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}"
                                class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('hire_date') border-red-400 @enderror"
                                required>
                            @error('hire_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الحالة -->
                        <div>
                            <label for="status" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-toggle-on text-red-600 text-lg"></i>
                                الحالة
                            </label>
                            <select name="status" id="status"
                                class="w-full py-3 px-5 border-2 border-gray-300 bg-white rounded-lg focus:border-gray-500 focus:ring-1 focus:ring-gray-500 text-base transition @error('status') border-red-400 @enderror"
                                required>
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-key text-red-500"></i>
                        معلومات الدخول
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- كلمة المرور (اختياري!) -->
                        <div>
                            <label for="password" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-lock text-red-600 text-lg"></i>
                                كلمة المرور الجديدة
                            </label>
                            <input type="password"
                                name="password"
                                id="password"
                                class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition @error('password') border-red-400 @enderror"
                                placeholder="اتركها فارغة إذا كنت لا تريد تغييرها">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- تأكيد كلمة المرور -->
                        <div>
                            <label for="password_confirmation" class="flex items-center gap-2 text-base font-semibold text-black mb-2">
                                <i class="fas fa-lock text-red-600 text-lg"></i>
                                تأكيد كلمة المرور
                            </label>
                            <input type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full py-3 px-5 border-2 border-gray-300 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 rounded-lg bg-gray-50 text-base transition"
                                placeholder="أعد إدخال كلمة المرور">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-shield text-red-500"></i>
                        الأدوار والصلاحيات
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $selectedRoles = old('roles', $employee->roles->pluck('id')->toArray());
                        @endphp
                        @foreach($roles as $role)
                            <div class="flex items-start space-x-2 space-x-reverse">
                                <input id="role_{{ $role->id }}"
                                    name="roles[]"
                                    type="checkbox"
                                    value="{{ $role->id }}"
                                    {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}
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
                    <a href="{{ route('admin.employees.show', $employee) }}"
                       class="bg-white py-3 px-8 border-2 border-gray-300 rounded-xl text-base font-bold text-gray-700 hover:bg-red-50 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-eye text-red-500"></i>
                        عرض
                    </a>
                    <a href="{{ route('admin.employees.index') }}"
                       class="bg-white py-3 px-8 border-2 border-gray-300 rounded-xl text-base font-bold text-gray-700 hover:bg-red-50 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-times text-red-500"></i>
                        إلغاء
                    </a>
                    <button type="submit"
                        class="bg-gradient-to-l from-red-600 to-red-500 py-3 px-12 border border-red-600 rounded-xl text-base font-bold text-white hover:from-red-700 hover:to-red-600 focus:ring-2 focus:ring-red-400 flex items-center gap-2 transition-colors">
                        <i class="fas fa-save"></i>
                        تحديث الموظف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
