@extends('admin.layouts.app')

@section('title', isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد')
@section('page-title', isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد')
@section('page-subtitle', isset($potentialCustomer) ? 'تعديل بيانات العميل المحتمل وتصنيفاته' : 'إضافة عميل محتمل جديد مع تحديد تصنيفاته')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <i class="fas fa-{{ isset($potentialCustomer) ? 'edit' : 'plus' }} ml-2"></i>
                {{ isset($potentialCustomer) ? 'تعديل العميل المحتمل' : 'إضافة عميل محتمل جديد' }}
            </h3>
        </div>

        <form action="{{ isset($potentialCustomer) ? route('admin.potential-customers.update', $potentialCustomer) : route('admin.potential-customers.store') }}" method="POST" class="p-6">
            @csrf
            @if(isset($potentialCustomer))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- معلومات العميل -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 border-b pb-2">معلومات العميل</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الموظف المسؤول</label>
                        <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">لا يوجد موظف محدد</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" 
                                    {{ (old('employee_id', $potentialCustomer->employee_id ?? '') == $employee->id) ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل المحتمل <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $potentialCustomer->customer_name ?? '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="أدخل اسم العميل" required>
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $potentialCustomer->phone ?? '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="مثال: +966501234567 أو 0501234567" required>
                        <p class="mt-1 text-xs text-gray-500">سيتم تكوين رابط الواتساب تلقائياً</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل المطلوب <span class="text-red-500">*</span></label>
                        <textarea name="work_description" rows="4" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="وصف تفصيلي للعمل المطلوب من العميل" required>{{ old('work_description', $potentialCustomer->work_description ?? '') }}</textarea>
                        @error('work_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="أي ملاحظات إضافية عن العميل">{{ old('notes', $potentialCustomer->notes ?? '') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(isset($potentialCustomer) && $potentialCustomer->whatsapp_link)
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-green-800 mb-2">رابط التواصل المباشر</h5>
                            <a href="{{ $potentialCustomer->whatsapp_link }}" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <i class="fab fa-whatsapp ml-2"></i>
                                التواصل عبر الواتساب
                            </a>
                        </div>
                    @endif
                </div>

                <!-- تصنيف العميل -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 border-b pb-2">تصنيف العميل</h4>
                    
                    <div>
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
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(isset($potentialCustomer) && !empty($potentialCustomer->customer_classifications))
                        <div class="bg-gray-50 p-4 rounded-lg">
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
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h5 class="text-sm font-medium text-blue-800 mb-2">معاينة رابط الواتساب:</h5>
                        <div id="whatsapp-preview" class="text-sm text-gray-600">
                            أدخل رقم الجوال لمعاينة الرابط
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t mt-6">
                <a href="{{ route('admin.potential-customers.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
                
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-save ml-2"></i>
                    {{ isset($potentialCustomer) ? 'تحديث' : 'حفظ' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('input[name="phone"]');
    const whatsappPreview = document.getElementById('whatsapp-preview');

    function generateWhatsappLink(phone) {
        if (!phone) {
            return 'أدخل رقم الجوال لمعاينة الرابط';
        }

        // تنظيف الرقم من الفواصل والمسافات والعلامات
        let cleanPhone = phone.replace(/[^0-9]/g, '');
        
        // إضافة كود المملكة إذا لم يكن موجود
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

    // تحديث المعاينة عند تغيير رقم الجوال
    phoneInput.addEventListener('input', updateWhatsappPreview);

    // تحديث المعاينة عند تحميل الصفحة
    updateWhatsappPreview();

    // تحديد/إلغاء تحديد جميع التصنيفات
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

    // إضافة زر تحديد الكل
    const classificationsContainer = document.querySelector('input[name="customer_classifications[]"]').closest('.space-y-3').parentElement;
    classificationsContainer.insertBefore(selectAllBtn, classificationsContainer.querySelector('.space-y-3'));
});
</script>
@endpush