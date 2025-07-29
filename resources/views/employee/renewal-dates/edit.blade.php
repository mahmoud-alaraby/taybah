{{-- resources/views/employee/renewal-dates/edit.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'تعديل حدث التجديد')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-edit text-red-600 mr-2"></i>
                    تعديل حدث التجديد
                </h1>
                <p class="text-gray-600 mt-1">تعديل بيانات الحدث الموجود</p>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('employee.renewal-dates.show', $renewalDate) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    عرض الحدث
                </a>
                <a href="{{ route('employee.renewal-dates.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($renewalDate->isOverdue())
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    @elseif($renewalDate->isToday())
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    @elseif($renewalDate->isUpcoming(3))
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    @else
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    @endif
                </div>
                <div class="mr-3">
                    <h4 class="text-sm font-medium text-gray-900">الحالة الحالية:</h4>
                    <div class="mt-1">
                        @if($renewalDate->isOverdue())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                متأخر {{ abs($renewalDate->getDaysUntilRenewal()) }} يوم
                            </span>
                        @elseif($renewalDate->isToday())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-calendar-day mr-1"></i>
                                موعد التجديد اليوم
                            </span>
                        @elseif($renewalDate->isUpcoming(3))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                باقي {{ $renewalDate->getDaysUntilRenewal() }} يوم
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $renewalDate->getStatusDisplayName() }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-edit text-red-600 mr-2"></i>
                تعديل بيانات الحدث
            </h3>
            <p class="text-sm text-gray-600 mt-1">قم بتعديل البيانات المطلوبة</p>
        </div>

        <form action="{{ route('employee.renewal-dates.update', $renewalDate) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- العنوان -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان الحدث <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $renewalDate->title) }}"
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
                           value="{{ old('renewal_date', $renewalDate->renewal_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('renewal_date') border-red-500 @enderror" 
                           required>
                    <p class="mt-1 text-xs text-gray-500">سيتم إعادة حساب التاريخ التالي للتجديد عند التغيير</p>
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
                        <option value="monthly" {{ old('frequency', $renewalDate->frequency) == 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="quarterly" {{ old('frequency', $renewalDate->frequency) == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="yearly" {{ old('frequency', $renewalDate->frequency) == 'yearly' ? 'selected' : '' }}>سنوي</option>
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
                           value="{{ old('amount', $renewalDate->amount) }}"
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
                        <option value="active" {{ old('status', $renewalDate->status) == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ old('status', $renewalDate->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status', $renewalDate->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                              placeholder="يمكنك إضافة أي ملاحظات أو تفاصيل إضافية هنا">{{ old('description', $renewalDate->description) }}</textarea>
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
                    حفظ التعديلات
                </button>
                <a href="{{ route('employee.renewal-dates.show', $renewalDate) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>
                    عرض الحدث
                </a>
                <a href="{{ route('employee.renewal-dates.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Event Info Section -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- معلومات إضافية -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-800 flex items-center mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                معلومات إضافية
            </h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">تاريخ الإنشاء:</span>
                    <span class="font-medium">{{ $renewalDate->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">آخر تحديث:</span>
                    <span class="font-medium">{{ $renewalDate->updated_at->format('Y-m-d H:i') }}</span>
                </div>
                @if($renewalDate->next_renewal_date)
                <div class="flex justify-between">
                    <span class="text-gray-600">التاريخ التالي للتجديد:</span>
                    <span class="font-medium text-blue-600">{{ $renewalDate->next_renewal_date->format('Y-m-d') }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">المنشئ:</span>
                    <span class="font-medium">{{ $renewalDate->getCreatorName() }}</span>
                </div>
                @if($renewalDate->getUpdaterName() !== 'غير محدد')
                <div class="flex justify-between">
                    <span class="text-gray-600">آخر محدث:</span>
                    <span class="font-medium">{{ $renewalDate->getUpdaterName() }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-blue-800 flex items-center mb-4">
                <i class="fas fa-bolt text-blue-600 mr-2"></i>
                إجراءات سريعة
            </h4>
            <div class="space-y-3">
                @if($renewalDate->status === 'active')
                <button onclick="markCompleted({{ $renewalDate->id }})" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-check mr-2"></i>
                    تحديد كمكتمل
                </button>
                <button onclick="renewEvent({{ $renewalDate->id }})" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-refresh mr-2"></i>
                    إنشاء تجديد جديد
                </button>
                @endif
                <a href="{{ route('employee.renewal-dates.show', $renewalDate) }}" 
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>
                    عرض تفاصيل كاملة
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function markCompleted(id) {
    if (confirm('هل تريد تحديد هذا الحدث كمكتمل؟')) {
        const url = `{{ route('employee.renewal-dates.complete', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تحديد الحدث كمكتمل بنجاح');
                location.reload();
            } else {
                alert('حدث خطأ أثناء التحديث');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء التحديث');
        });
    }
}

function renewEvent(id) {
    if (confirm('هل تريد إنشاء حدث تجديد جديد لهذا الحدث؟')) {
        const url = `{{ route('employee.renewal-dates.renew', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('حدث خطأ أثناء التجديد');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء التجديد');
        });
    }
}

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
