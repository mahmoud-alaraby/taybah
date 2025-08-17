{{-- resources/views/employee/renewal-dates/create.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'إضافة حدث تجديد جديد')

@section('content')
<div class="mx-auto max-w-4xl p-6">
  

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold">نجح!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold">خطأ!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
         <div class="">
           <!-- Header Section -->
 

           <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
            <div class = "flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
    <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              أضف حدث جديد إلى النظام
            </h2>

                  <a href="{{ route('employee.renewal-dates.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للقائمة
                    </a>
            </div>
        
        </div>
        </div>

        <form action="{{ route('employee.renewal-dates.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- العنوان -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان الحدث <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('title') border-red-500 @enderror" 
                           placeholder="اكتب عنواناً واضحاً ومفهوماً للحدث"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تاريخ التجديد -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تاريخ التجديد <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="renewal_date" 
                           value="{{ old('renewal_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('renewal_date') border-red-500 @enderror" 
                           required>
                    <p class="mt-1 text-xs text-gray-500">سيتم حساب التاريخ التالي للتجديد تلقائياً</p>
                    @error('renewal_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تكرار التجديد -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تكرار التجديد <span class="text-red-500">*</span>
                    </label>
                    <select name="frequency" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('frequency') border-red-500 @enderror" 
                            required>
                        <option value="">اختر تكرار التجديد</option>
                        <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>سنوي</option>
                    </select>
                    @error('frequency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المبلغ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المبلغ (ريال سعودي)
                    </label>
                    <input type="number" 
                           name="amount" 
                           value="{{ old('amount') }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('amount') border-red-500 @enderror" 
                           placeholder="0.00">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- حالة الحدث -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        حالة الحدث <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('status') border-red-500 @enderror" 
                            required>
                        <option value="">اختر حالة الحدث</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الوصف -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الوصف والملاحظات
                    </label>
                    <textarea name="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('description') border-red-500 @enderror" 
                              placeholder="يمكنك إضافة أي ملاحظات أو تفاصيل إضافية هنا">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>
                    حفظ الحدث
                </button>
                <a href="{{ route('employee.renewal-dates.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-blue-800 flex items-center mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            معلومات مفيدة
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
            <div>
                <h5 class="font-semibold mb-2">أنواع التكرار:</h5>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>شهري:</strong> كل شهر</li>
                    <li><strong>ربع سنوي:</strong> كل 3 أشهر</li>
                    <li><strong>سنوي:</strong> كل سنة</li>
                </ul>
            </div>
            <div>
                <h5 class="font-semibold mb-2">حالات الأحداث:</h5>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>نشط:</strong> الحدث قيد التفعيل</li>
                    <li><strong>مكتمل:</strong> تم تنفيذ الحدث</li>
                    <li><strong>ملغي:</strong> تم إلغاء الحدث</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إخفاء رسائل النجاح/الخطأ تلقائياً بعد 5 ثواني
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
@endsection
