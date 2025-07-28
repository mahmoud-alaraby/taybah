{{-- resources/views/admin/operation-system/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تعديل المهمة')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white">تعديل المهمة</h1>
                            <p class="text-orange-100 text-sm">تعديل مهمة {{ $task->employee_name }} - {{ Carbon\Carbon::parse($task->task_date)->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <a href="{{ route('admin.operation-system.show', $task->id) }}" class="inline-flex items-center px-4 py-2 bg-white text-orange-600 font-medium rounded-lg hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            عرض التفاصيل
                        </a>
                        <a href="{{ route('admin.operation-system.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-orange-600 font-medium rounded-lg hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('admin.operation-system.update', $task->id) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Task Info Display --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-orange-800 mb-2">معلومات المهمة الحالية</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">التاريخ:</span>
                            <span class="font-medium text-gray-900">{{ Carbon\Carbon::parse($task->task_date)->translatedFormat('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">النوع:</span>
                            <span class="font-medium text-gray-900">{{ $task->task_type === 'design' ? 'تصميم' : 'تسويق' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">الموظف:</span>
                            <span class="font-medium text-gray-900">{{ $task->employee_name }}</span>
                        </div>
                    </div>
                </div>

                {{-- Basic Info --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            نوع المهمة
                            <span class="text-red-500 mr-1">*</span>
                        </label>
                        <select name="task_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors @error('task_type') border-red-500 @enderror">
                            <option value="design" {{ old('task_type', $task->task_type) == 'design' ? 'selected' : '' }}>🎨 تصميم</option>
                            <option value="marketing" {{ old('task_type', $task->task_type) == 'marketing' ? 'selected' : '' }}>📢 تسويق</option>
                        </select>
                        @error('task_type')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            حالة المهمة
                            <span class="text-red-500 mr-1">*</span>
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors @error('status') border-red-500 @enderror">
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>⏳ قيد الانتظار</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>🔄 قيد التنفيذ</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>✅ مكتملة</option>
                            <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>❌ ملغية</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Employee Selection --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        الموظف المكلف
                        <span class="text-red-500 mr-1">*</span>
                    </label>
                    <select name="assigned_person_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors @error('assigned_person_id') border-red-500 @enderror">
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_person_id', $task->assigned_person_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->department }} ({{ $employee->position }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_person_id')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Task Description --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        وصف المهمة
                    </label>
                    <textarea name="task_description" rows="4" maxlength="1000" 
                              placeholder="أدخل وصفاً مفصلاً للمهمة المطلوبة..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none @error('task_description') border-red-500 @enderror">{{ old('task_description', $task->task_description) }}</textarea>
                    @error('task_description')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-500">حد أقصى 1000 حرف</p>
                    @enderror
                </div>

                {{-- Additional Notes --}}
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        ملاحظات إضافية
                    </label>
                    <textarea name="notes" rows="3" maxlength="500" 
                              placeholder="أي ملاحظات أو تعليمات إضافية..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none @error('notes') border-red-500 @enderror">{{ old('notes', $task->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-500">حد أقصى 500 حرف</p>
                    @enderror
                </div>

                {{-- Reserved Toggle --}}
                <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <label class="text-sm font-semibold text-gray-700">مهمة محجوزة</label>
                                <p class="text-xs text-gray-500">عند التفعيل، المهمة لن تكون قابلة للتعديل من قبل الآخرين</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_reserved" value="1" {{ old('is_reserved', $task->is_reserved) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 sm:space-x-reverse pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.operation-system.show', $task->id) }}" 
                       class="w-full sm:w-auto px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center">
                        <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-orange-500 to-yellow-500 hover:from-orange-600 hover:to-yellow-600 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                        <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript للتحسينات --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عداد الأحرف للنصوص
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const container = textarea.parentNode;
        
        // إنشاء عداد
        const counter = document.createElement('div');
        counter.className = 'text-xs text-gray-500 mt-1';
        counter.textContent = `${textarea.value.length}/${maxLength} حرف`;
        
        // استبدال النص الموجود
        const existingHelper = container.querySelector('.text-xs.text-gray-500');
        if (existingHelper) {
            existingHelper.replaceWith(counter);
        } else {
            container.appendChild(counter);
        }
        
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            counter.textContent = `${currentLength}/${maxLength} حرف`;
            counter.className = remaining < 50 ? 'text-xs text-orange-500 mt-1' : 'text-xs text-gray-500 mt-1';
        });
    });
});
</script>
@endsection
