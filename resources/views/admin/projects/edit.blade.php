@extends('admin.layouts.app')

@section('title', 'تعديل المشروع')
@section('page-title', 'تعديل المشروع: ' . $project->name)
@section('page-subtitle', 'تحديث بيانات المشروع')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-xl p-6">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-edit ml-3"></i>
                تعديل المشروع
            </h3>
            <p class="text-blue-100 mt-1">تحديث بيانات المشروع وإعداداته</p>
        </div>

        <!-- Form Body -->
        <div class="p-6">
            <form action="{{ route('admin.projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- اسم المشروع -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-project-diagram text-blue-600 mr-1"></i>
                            اسم المشروع <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 bg-red-50 @enderror"
                               placeholder="أدخل اسم المشروع" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- اسم العميل -->
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-600 mr-1"></i>
                            اسم العميل
                        </label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name', $project->client_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="اسم العميل أو الشركة">
                    </div>

                    <!-- الحالة -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag text-blue-600 mr-1"></i>
                            حالة المشروع <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>معلق</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <!-- تاريخ البداية -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-plus text-blue-600 mr-1"></i>
                            تاريخ البداية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- تاريخ النهاية -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check text-blue-600 mr-1"></i>
                            تاريخ النهاية المتوقع
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- وصف المشروع -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left text-blue-600 mr-1"></i>
                            وصف المشروع
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="وصف تفصيلي للمشروع ومتطلباته">{{ old('description', $project->description) }}</textarea>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 mt-6">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center font-medium">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.projects.show', $project) }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center font-medium">
                        <i class="fas fa-arrow-left ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Project Stats -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">إحصائيات المشروع</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $project->tasks->count() }}</div>
                <div class="text-gray-600">مهام</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $project->tasks->where('status', 'completed')->count() }}</div>
                <div class="text-gray-600">مكتملة</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($project->total_hours, 1) }}</div>
                <div class="text-gray-600">ساعة</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $project->completion_percentage }}%</div>
                <div class="text-gray-600">مكتمل</div>
            </div>
        </div>
    </div>
</div>
@endsection