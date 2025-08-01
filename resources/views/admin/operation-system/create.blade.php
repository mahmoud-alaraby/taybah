{{-- resources/views/admin/operation-system/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة مهمة جديدة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">إضافة مهمة جديدة</h1>
                            <p class="text-blue-100 text-sm mt-1">أضف مهمة جديدة إلى نظام التشغيل العام</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.operation-system.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('admin.operation-system.store') }}" class="p-6 sm:p-8">
                @csrf
                
                {{-- Form Grid Container --}}
                <div class="space-y-8">
                    
                    {{-- Basic Information Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            المعلومات الأساسية
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Task Date --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        تاريخ المهمة
                                        <span class="text-red-500 mr-1">*</span>
                                    </div>
                                </label>
                                <input type="date" 
                                       name="task_date" 
                                       value="{{ request('date', old('task_date')) }}" 
                                       required 
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('task_date') border-red-500 ring-2 ring-red-200 @enderror">
                                @error('task_date')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">لا يمكن اختيار تاريخ سابق</p>
                                @enderror
                            </div>

                            {{-- Task Type --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        نوع المهمة
                                        <span class="text-red-500 mr-1">*</span>
                                    </div>
                                </label>
                                <select name="task_type" 
                                        required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('task_type') border-red-500 ring-2 ring-red-200 @enderror">
                                    <option value="">اختر نوع المهمة</option>
                                    <option value="design" {{ old('task_type') == 'design' ? 'selected' : '' }}>
                                        🎨 تصميم
                                    </option>
                                    <option value="marketing" {{ old('task_type') == 'marketing' ? 'selected' : '' }}>
                                        📢 تسويق
                                    </option>
                                </select>
                                @error('task_type')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Employee Assignment Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            تخصيص الموظف
                        </h3>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                الموظف المكلف
                                <span class="text-red-500 mr-1">*</span>
                            </label>
                            <select name="assigned_person_id" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('assigned_person_id') border-red-500 ring-2 ring-red-200 @enderror">
                                <option value="">اختر الموظف المكلف</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ old('assigned_person_id') == $employee->id ? 'selected' : '' }} 
                                            data-department="{{ $employee->department }}">
                                        {{ $employee->name }} - {{ $employee->department }} ({{ $employee->position }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_person_id')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Task Details Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                            </div>
                            تفاصيل المهمة
                        </h3>
                        
                        <div class="space-y-6">
                            {{-- Task Description --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    وصف المهمة
                                </label>
                                <div class="relative">
                                    <textarea name="task_description" 
                                              rows="4" 
                                              maxlength="1000" 
                                              placeholder="أدخل وصفاً مفصلاً للمهمة المطلوبة..."
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none @error('task_description') border-red-500 ring-2 ring-red-200 @enderror">{{ old('task_description') }}</textarea>
                                    <div class="absolute bottom-3 left-3 text-xs text-gray-400">
                                        <span id="desc-counter">{{ strlen(old('task_description', '')) }}</span>/1000
                                    </div>
                                </div>
                                @error('task_description')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">حد أقصى 1000 حرف</p>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    ملاحظات إضافية
                                </label>
                                <div class="relative">
                                    <textarea name="notes" 
                                              rows="3" 
                                              maxlength="500" 
                                              placeholder="أي ملاحظات أو تعليمات إضافية..."
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none @error('notes') border-red-500 ring-2 ring-red-200 @enderror">{{ old('notes') }}</textarea>
                                    <div class="absolute bottom-3 left-3 text-xs text-gray-400">
                                        <span id="notes-counter">{{ strlen(old('notes', '')) }}</span>/500
                                    </div>
                                </div>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-1">حد أقصى 500 حرف</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Settings Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            إعدادات المهمة
                        </h3>
                        
                        {{-- Reserved Toggle --}}
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <div class="mr-4">
                                        <label class="text-sm font-semibold text-gray-900">مهمة محجوزة</label>
                                        <p class="text-xs text-gray-600 mt-1">عند التفعيل، المهمة لن تكون قابلة للتعديل من قبل الآخرين</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_reserved" value="1" {{ old('is_reserved') ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 shadow-sm"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 sm:space-x-reverse pt-8 border-t border-gray-200">
                        <a href="{{ route('admin.operation-system.index') }}" 
                           class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            حفظ المهمة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript للتحسينات --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عداد الأحرف للوصف
    const descTextarea = document.querySelector('textarea[name="task_description"]');
    const descCounter = document.getElementById('desc-counter');
    
    if (descTextarea && descCounter) {
        descTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            descCounter.textContent = currentLength;
            
            if (currentLength > 950) {
                descCounter.parentElement.classList.add('text-red-500');
                descCounter.parentElement.classList.remove('text-gray-400');
            } else {
                descCounter.parentElement.classList.add('text-gray-400');
                descCounter.parentElement.classList.remove('text-red-500');
            }
        });
    }
    
    // عداد الأحرف للملاحظات
    const notesTextarea = document.querySelector('textarea[name="notes"]');
    const notesCounter = document.getElementById('notes-counter');
    
    if (notesTextarea && notesCounter) {
        notesTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            notesCounter.textContent = currentLength;
            
            if (currentLength > 450) {
                notesCounter.parentElement.classList.add('text-red-500');
                notesCounter.parentElement.classList.remove('text-gray-400');
            } else {
                notesCounter.parentElement.classList.add('text-gray-400');
                notesCounter.parentElement.classList.remove('text-red-500');
            }
        });
    }
});
</script>
@endsection
