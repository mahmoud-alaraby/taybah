{{-- resources/views/admin/renewal-dates/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة حدث تجديد جديد')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-calendar-plus text-red-600 mr-2"></i>
                    إضافة حدث تجديد جديد
                </h1>
                <p class="text-gray-600 mt-1">إضافة حدث تجديد جديد إلى النظام</p>
            </div>
            <!-- Breadcrumb -->
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                            <i class="fas fa-home ml-3"></i>
                            الرئيسية
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                            <a href="{{ route('admin.renewal-dates.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-red-600">
                                مواعيد التجديد
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500">إضافة جديد</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-xl p-6">
                <h3 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-calendar-event mr-3"></i>
                    بيانات الحدث الجديد
                </h3>
                <p class="text-red-100 mt-1">املأ جميع البيانات المطلوبة بدقة</p>
            </div>

            <!-- Form Body -->
            <div class="p-6">
                <form action="{{ route('admin.renewal-dates.store') }}" method="POST" id="renewalForm">
                    @csrf
                    
                    <!-- Event Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag text-red-600 mr-1"></i>
                            عنوان الحدث <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('title') border-red-500 bg-red-50 @enderror" 
                               id="title" name="title" value="{{ old('title') }}" 
                               placeholder="مثال: تجديد دومين شركة الحمد">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            اكتب عنواناً واضحاً ومفهوماً للحدث
                        </p>
                    </div>

                    <!-- Date and Frequency Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Renewal Date -->
                        <div>
                            <label for="renewal_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar text-red-600 mr-1"></i>
                                تاريخ التجديد <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('renewal_date') border-red-500 bg-red-50 @enderror" 
                                   id="renewal_date" name="renewal_date" value="{{ old('renewal_date') }}">
                            @error('renewal_date')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Frequency -->
                        <div>
                            <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-repeat text-red-600 mr-1"></i>
                                تكرار الحدث <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('frequency') border-red-500 bg-red-50 @enderror" 
                                    id="frequency" name="frequency">
                                <option value="">اختر تكرار الحدث</option>
                                <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar-year"></i> سنوي
                                </option>
                                <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar-week"></i> ربع سنوي (كل 3 شهور)
                                </option>
                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar-day"></i> شهري
                                </option>
                            </select>
                            @error('frequency')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-lightbulb mr-1"></i>
                                سيتم حساب التاريخ التالي للتجديد تلقائياً
                            </p>
                        </div>
                    </div>

                    <!-- Amount and Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill text-red-600 mr-1"></i>
                                المبلغ (اختياري)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('amount') border-red-500 bg-red-50 @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" 
                                       step="0.01" min="0" placeholder="0.00">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">ريال</span>
                                </div>
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag text-red-600 mr-1"></i>
                                حالة الحدث <span class="text-red-500">*</span>
                            </label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('status') border-red-500 bg-red-50 @enderror" 
                                    id="status" name="status">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    🟢 نشط
                                </option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                    🔵 مكتمل
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                    🔴 ملغي
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left text-red-600 mr-1"></i>
                            وصف الحدث (اختياري)
                        </label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('description') border-red-500 bg-red-50 @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="وصف تفصيلي للحدث، الجهة المسؤولة، تفاصيل إضافية...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            يمكنك إضافة أي ملاحظات أو تفاصيل إضافية هنا
                        </p>
                    </div>

                    <!-- Next Renewal Preview -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6 hidden" id="nextRenewalPreview">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-blue-800 font-semibold">التاريخ التالي للتجديد</h4>
                                <p class="text-blue-600 text-sm" id="nextRenewalDate"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center font-medium">
                            <i class="fas fa-save mr-2"></i>
                            حفظ الحدث
                        </button>
                        <a href="{{ route('admin.renewal-dates.index') }}" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            رجوع
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-question-circle text-gray-600 mr-2"></i>
                نصائح مفيدة
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-0.5"></i>
                    <div>
                        <strong>العنوان:</strong> اكتب عنواناً واضحاً يصف الحدث بدقة
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-calendar-alt text-blue-500 mr-2 mt-0.5"></i>
                    <div>
                        <strong>التاريخ:</strong> اختر تاريخ التجديد الحالي، وسيتم حساب التالي تلقائياً
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-repeat text-green-500 mr-2 mt-0.5"></i>
                    <div>
                        <strong>التكرار:</strong> اختر المدة الزمنية بين كل تجديد
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-bell text-red-500 mr-2 mt-0.5"></i>
                    <div>
                        <strong>التنبيهات:</strong> سيتم إرسال تنبيه قبل 3 أيام من التجديد
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const renewalDateInput = document.getElementById('renewal_date');
    const frequencySelect = document.getElementById('frequency');
    const nextRenewalPreview = document.getElementById('nextRenewalPreview');
    const nextRenewalDate = document.getElementById('nextRenewalDate');

    function calculateNextRenewal() {
        const renewalDate = renewalDateInput.value;
        const frequency = frequencySelect.value;

        if (renewalDate && frequency) {
            const date = new Date(renewalDate);
            let nextDate = new Date(date);

            switch (frequency) {
                case 'yearly':
                    nextDate.setFullYear(date.getFullYear() + 1);
                    break;
                case 'quarterly':
                    nextDate.setMonth(date.getMonth() + 3);
                    break;
                case 'monthly':
                    nextDate.setMonth(date.getMonth() + 1);
                    break;
            }

            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            nextRenewalDate.textContent = nextDate.toLocaleDateString('ar-SA', options);
            nextRenewalPreview.classList.remove('hidden');
        } else {
            nextRenewalPreview.classList.add('hidden');
        }
    }

    renewalDateInput.addEventListener('change', calculateNextRenewal);
    frequencySelect.addEventListener('change', calculateNextRenewal);

    // حساب التاريخ التالي عند تحميل الصفحة إذا كانت القيم موجودة
    calculateNextRenewal();

    // Form validation
    const form = document.getElementById('renewalForm');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const renewalDate = document.getElementById('renewal_date').value;
        const frequency = document.getElementById('frequency').value;

        if (!title || !renewalDate || !frequency) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول المطلوبة');
            return false;
        }

        // التحقق من أن التاريخ ليس في الماضي البعيد
        const selectedDate = new Date(renewalDate);
        const today = new Date();
        const oneYearAgo = new Date();
        oneYearAgo.setFullYear(today.getFullYear() - 1);

        if (selectedDate < oneYearAgo) {
            e.preventDefault();
            alert('لا يمكن اختيار تاريخ أقدم من سنة واحدة');
            return false;
        }
    });

    // Auto-resize textarea
    const textarea = document.getElementById('description');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});
</script>
@endsection
