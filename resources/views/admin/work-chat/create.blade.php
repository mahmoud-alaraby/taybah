@extends('admin.layouts.app')

@section('title', 'إنشاء شات جديد')
@section('page-title', 'إنشاء شات جديد')
@section('page-subtitle', $type === 'design' ? 'شات متابعة التصميم' : 'شات متابعة المونتاج')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-lg p-6">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <i class="fas {{ $type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} ml-3"></i>
                إنشاء شات {{ $type === 'design' ? 'تصميم' : 'مونتاج' }} جديد
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.work-chat.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <!-- عنوان الشات -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-heading text-blue-600 mr-1"></i>
                        عنوان الشات <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 bg-red-50 @enderror"
                           placeholder="مثال: تصميم بروفايل شركة XYZ"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اختيار الموظف -->
                <div class="mb-6">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-green-600 mr-1"></i>
                        {{ $type === 'design' ? 'المصمم' : 'المونتير' }} <span class="text-red-500">*</span>
                    </label>
                    <select name="employee_id" 
                            id="employee_id"
                            class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('employee_id') border-red-500 bg-red-50 @enderror"
                            required>
                        <option value="">اختر {{ $type === 'design' ? 'المصمم' : 'المونتير' }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->employee_id }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    @if($employees->count() === 0)
                        <p class="mt-2 text-sm text-yellow-600 bg-yellow-50 p-3 rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            لا يوجد موظفين لديهم صلاحية {{ $type === 'design' ? 'متابعة التصميم' : 'متابعة المونتاج' }}
                        </p>
                    @endif
                </div>

                <!-- معلومات إضافية -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">معلومات مهمة:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• سيتم إنشاء شات مباشر بينك وبين {{ $type === 'design' ? 'المصمم' : 'المونتير' }}</li>
                        <li>• يمكنك إرسال رسائل نصية وصوتية ومرفقات</li>
                        <li>• جميع الملفات ستكون متاحة برابط مباشر للمشاركة</li>
                        <li>• يمكن إدارة وحذف الملفات لتوفير المساحة</li>
                    </ul>
                </div>

                <!-- أزرار التحكم -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors font-medium"
                            {{ $employees->count() === 0 ? 'disabled' : '' }}>
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء الشات
                    </button>
                    <a href="{{ route('admin.work-chat.index', ['type' => $type]) }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center font-medium">
                        <i class="fas fa-arrow-right ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- نصائح -->
    <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 ml-2"></i>
            نصائح للاستخدام الأمثل
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div class="flex items-start">
                <i class="fas fa-check text-green-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>عنوان واضح:</strong> استخدم عنواناً يصف المشروع بوضوح
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-users text-blue-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>الموظف المناسب:</strong> تأكد من اختيار الشخص المسؤول عن المهمة
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-file-alt text-purple-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>المرفقات:</strong> يمكن إرفاق ملفات بحجم يصل إلى 10 ميجابايت
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-link text-orange-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>المشاركة:</strong> جميع الملفات تحصل على رابط مباشر للمشاركة
                </div>
            </div>
        </div>
    </div>
</div>
@endsection