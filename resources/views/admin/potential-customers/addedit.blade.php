@extends('admin.layouts.app')

@section('title', isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد')
@section('page-title', isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد')
@section('page-subtitle', isset($potentialCustomer) ? 'تعديل بيانات العميل المحتمل وتصنيفاته' : 'إضافة عميل محتمل جديد مع تحديد تصنيفاته')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-700 to-red-800 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">
                                {{ isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد' }}
                            </h1>
                            <p class="text-red-100 text-sm mt-1">
                                {{ isset($potentialCustomer) ? 'تحديث بيانات العميل المحتمل وتصنيفاته' : 'أضف بيانات عميل جديد وحدد تصنيفاته بدقة' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.potential-customers.index') }}" 
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
            <form action="{{ isset($potentialCustomer) ? route('admin.potential-customers.update', $potentialCustomer) : route('admin.potential-customers.store') }}" method="POST" class="p-6 sm:p-8">
                @csrf
                @if(isset($potentialCustomer))
                    @method('PUT')
                @endif

                <div class="space-y-8">

                    {{-- معلومات العميل --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12a4 4 0 108 0 4 4 0 00-8 0zM12 14c-5.523 0-10 2.239-10 5v3"></path></svg>
                            </div>
                            معلومات العميل
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- الموظف المسؤول --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        الموظف المسؤول
                                    </div>
                                </label>
                                <select name="employee_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('employee_id') border-red-600 ring-2 ring-red-200 @enderror">
                                    <option value="">لا يوجد موظف محدد</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ (old('employee_id', $potentialCustomer->employee_id ?? '') == $employee->id) ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <p class="text-red-600 text-xs mt-1 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- اسم العميل --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">اسم العميل المحتمل <span class="text-red-600">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $potentialCustomer->customer_name ?? '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('customer_name') border-red-600 ring-2 ring-red-200 @enderror" 
                                       placeholder="أدخل اسم العميل" required>
                                @error('customer_name')
                                    <p class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            {{-- رقم الجوال --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">رقم الجوال <span class="text-red-600">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $potentialCustomer->phone ?? '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('phone') border-red-600 ring-2 ring-red-200 @enderror" 
                                       placeholder="مثال: +966501234567 أو 0501234567" required>
                                <p class="mt-1 text-xs text-gray-500">سيتم تكوين رابط الواتساب تلقائياً</p>
                                @error('phone')
                                    <p class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- وصف العمل --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">وصف العمل المطلوب <span class="text-red-600">*</span></label>
                                <textarea name="work_description" rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('work_description') border-red-600 ring-2 ring-red-200 @enderror" 
                                    placeholder="وصف تفصيلي للعمل المطلوب من العميل" required>{{ old('work_description', $potentialCustomer->work_description ?? '') }}</textarea>
                                @error('work_description')
                                    <p class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">ملاحظات إضافية</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('notes') border-red-600 ring-2 ring-red-200 @enderror" 
                                      placeholder="أي ملاحظات إضافية عن العميل">{{ old('notes', $potentialCustomer->notes ?? '') }}</textarea>
                            @error('notes')
                                <p class="text-red-600 text-xs mt-1 flex items-center"><svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                            @enderror
                        </div>

                        @if(isset($potentialCustomer) && $potentialCustomer->whatsapp_link)
                            <div class="bg-green-50 mt-4 p-4 rounded-lg">
                                <h5 class="text-sm font-medium text-green-800 mb-2">رابط التواصل المباشر</h5>
                                <a href="{{ $potentialCustomer->whatsapp_link }}" target="_blank" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fab fa-whatsapp ml-2"></i>
                                    التواصل عبر الواتساب
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- تصنيف العميل --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-2M4 7v6a2 2 0 002 2h2m4-2v2a2 2 0 002 2h2a2 2 0 002-2v-2"></path>
                                </svg>
                            </div>
                            تصنيف العميل
                        </h3>
                        <label class="block text-sm font-medium text-gray-700 mb-3">اختر التصنيفات المناسبة للعميل:</label>
                        <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                            @foreach($classifications as $classification)
                                <label class="flex items-start">
                                    <input type="checkbox" name="customer_classifications[]" value="{{ $classification['name'] }}" 
                                        {{ in_array($classification['name'], old('customer_classifications', $potentialCustomer->customer_classifications ?? [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mt-1">
                                    <div class="mr-3 flex-1">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <span class="text-sm font-medium text-gray-900">{{ $classification['display_name'] }}</span>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $classification['color_class'] }}">
                                                تصنيف
                                            </span>
                                        </div>
                                        @if($classification['description'])
                                            <p class="text-xs text-gray-500 mt-1">{{ $classification['description'] }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500">يمكن اختيار أكثر من تصنيف للعميل الواحد</p>
                        @error('customer_classifications')
                            <p class="text-red-600 text-xs mt-1 flex items-center">
                                <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}</p>
                        @enderror

                        @if(isset($potentialCustomer) && !empty($potentialCustomer->customer_classifications))
                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">التصنيفات الحالية:</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($potentialCustomer->classifications_badges as $badge)
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $badge['color_class'] }}">
                                        {{ $badge['display_name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- معاينة رابط الواتساب -->
                        <div class="bg-blue-50 p-4 rounded-lg mt-6">
                            <h5 class="text-sm font-medium text-blue-800 mb-2">معاينة رابط الواتساب:</h5>
                            <div id="whatsapp-preview" class="text-sm text-gray-600">
                                أدخل رقم الجوال لمعاينة الرابط
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 sm:space-x-reverse pt-8 border-t border-gray-200 mt-8">
                    <a href="{{ route('admin.potential-customers.index') }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-900 hover:to-red-900 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        {{ isset($potentialCustomer) ? 'تحديث' : 'حفظ' }}
                    </button>
                </div>
            </form>
        </div>    
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // WhatsApp preview
    const phoneInput = document.querySelector('input[name="phone"]');
    const whatsappPreview = document.getElementById('whatsapp-preview');

    function generateWhatsappLink(phone) {
        if (!phone) {
            return 'أدخل رقم الجوال لمعاينة الرابط';
        }
        let cleanPhone = phone.replace(/[^0-9]/g, '');
        if (!cleanPhone.startsWith('966')) {
            if (cleanPhone.startsWith('0')) {
                cleanPhone = '966' + cleanPhone.substring(1);
            } else {
                cleanPhone = '966' + cleanPhone;
            }
        }
        const whatsappUrl = `https://api.whatsapp.com/send/?phone=${cleanPhone}`;
        return `<a href="${whatsappUrl}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">${whatsappUrl}</a>`;
    }

    function updateWhatsappPreview() {
        const phone = phoneInput.value;
        whatsappPreview.innerHTML = generateWhatsappLink(phone);
    }

    phoneInput.addEventListener('input', updateWhatsappPreview);
    updateWhatsappPreview();

    // زر تحديد الكل/إلغاء تحديد الكل
    const selectAllBtn = document.createElement('button');
    selectAllBtn.type = 'button';
    selectAllBtn.className = 'text-sm text-blue-600 hover:text-blue-800 mb-3';
    selectAllBtn.innerHTML = '<i class="fas fa-check-square ml-1"></i> تحديد الكل';
    selectAllBtn.onclick = function() {
        const checkboxes = document.querySelectorAll('input[name="customer_classifications[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        selectAllBtn.innerHTML = allChecked ? 
            '<i class="fas fa-check-square ml-1"></i> تحديد الكل' : 
            '<i class="fas fa-square ml-1"></i> إلغاء تحديد الكل';
    };
    const classificationsContainer = document.querySelector('input[name="customer_classifications[]"]').closest('.space-y-3').parentElement;
    classificationsContainer.insertBefore(selectAllBtn, classificationsContainer.querySelector('.space-y-3'));
});
</script>
@endpush
