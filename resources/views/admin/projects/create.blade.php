@extends('admin.layouts.app')

@section('title', 'إنشاء مشروع جديد')
@section('page-title', 'إنشاء مشروع جديد')
@section('page-subtitle', 'إضافة مشروع جديد للنظام')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-xl p-6">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-project-diagram ml-3"></i>
                إنشاء مشروع جديد
            </h3>
            <p class="text-red-100 mt-1">املأ بيانات المشروع بعناية</p>
        </div>

        <!-- Form Body -->
        <div class="p-6">
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- اسم المشروع -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-project-diagram text-red-600 mr-1"></i>
                            اسم المشروع <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 bg-red-50 @enderror"
                               placeholder="أدخل اسم المشروع" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- اسم العميل -->
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-red-600 mr-1"></i>
                            اسم العميل
                        </label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="اسم العميل أو الشركة">
                    </div>

                    <!-- الحالة -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag text-red-600 mr-1"></i>
                            حالة المشروع <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <!-- تاريخ البداية -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-plus text-red-600 mr-1"></i>
                            تاريخ البداية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               required>
                    </div>

                    <!-- تاريخ النهاية -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check text-red-600 mr-1"></i>
                            تاريخ النهاية المتوقع
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- وصف المشروع -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left text-red-600 mr-1"></i>
                            وصف المشروع
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="وصف تفصيلي للمشروع ومتطلباته">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 mt-6">
                    <button type="submit" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center font-medium">
                        <i class="fas fa-save ml-2"></i>
                        إنشاء المشروع
                    </button>
                    <a href="{{ route('admin.projects.index') }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center font-medium">
                        <i class="fas fa-arrow-left ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Card -->
    <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 ml-2"></i>
            نصائح لإنشاء مشروع ناجح
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>اسم واضح:</strong> اختر اسماً يعبر عن طبيعة المشروع بوضوح
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-calendar text-blue-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>تواريخ واقعية:</strong> حدد تواريخ قابلة للتحقيق لضمان نجاح المشروع
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-users text-purple-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>فريق العمل:</strong> ستتمكن من إضافة المهام وتوزيعها على الفريق لاحقاً
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-tasks text-orange-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>تتبع التقدم:</strong> سيتم تتبع ساعات العمل والتقدم تلقائياً
                </div>
            </div>
        </div>
    </div>
</div>
@endsection