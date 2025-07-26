@extends('employee.layouts.app')

@section('title', 'تعديل العميل المحتمل')
@section('page-title', 'تعديل العميل المحتمل')
@section('page-subtitle', 'تعديل بيانات العميل المحتمل وتصنيفاته')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <i class="fas fa-edit ml-2"></i>
                تعديل العميل المحتمل: {{ $potentialCustomer->customer_name }}
            </h3>
        </div>

        <form action="{{ route('employee.potential-customers.update', $potentialCustomer) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- معلومات العميل -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 border-b pb-2">معلومات العميل</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل المحتمل <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $potentialCustomer->customer_name) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="أدخل اسم العميل" required>
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $potentialCustomer->phone) }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="مثال: +966501234567 أو 0501234567" required>
                        <p class="mt-1 text-xs text-gray-500">سيتم تحديث رابط الواتساب تلقائياً</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل المطلوب <span class="text-red-500">*</span></label>
                        <textarea name="work_description" rows="4" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="وصف تفصيلي للعمل المطلوب من العميل" required>{{ old('work_description', $potentialCustomer->work_description) }}</textarea>
                        @error('work_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="أي ملاحظات إضافية عن العميل">{{ old('notes', $potentialCustomer->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($potentialCustomer->whatsapp_link)
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
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-700">اختر التصنيفات المناسبة للعميل:</label>
                            <button type="button" id="selectAllBtn" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-check-square ml-1"></i> تحديد الكل
                            </button>
                        </div>
                        <div class="space-y-3 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4">
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

                    @if(!empty($potentialCustomer->customer_classifications))
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
                            {{ $potentialCustomer->whatsapp_link }}
                        </div>
                    </div>

                    <!-- تاريخ التصنيفات -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h5 class="text-sm font-medium text-yellow-800 mb-2">معلومات إضافية:</h5>
                        <div class="text-xs text-yellow-700 space-y-1">
                            <div>تاريخ الإضافة: {{ $potentialCustomer->created_at->format('Y-m-d H:i') }}</div>
                            <div>آخر تحديث: {{ $potentialCustomer->updated_at->format('Y-m-d H:i') }}</div>
                            <div>عدد التصنيفات: {{ count($potentialCustomer->customer_classifications ?? []) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t mt-6">
                <a href="{{ route('employee.potential-customers') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
                
                <div class="flex space-x-2 space-x-reverse">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-save ml-2"></i>
                        تحديث
                    </button>
                    <button type="button" onclick="resetForm()" 
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-undo ml-2"></i>
                        إعادة تعيين
                    </button>
                </div>
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
    const selectAllBtn = document.getElementById('selectAllBtn');

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

    // تحديد/إلغاء تحديد جميع التصنيفات
    selectAllBtn.onclick = function() {
        const checkboxes = document.querySelectorAll('input[name="customer_classifications[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => cb.checked = !allChecked);
        selectAllBtn.innerHTML = allChecked ? 
            '<i class="fas fa-check-square ml-1"></i> تحديد الكل' : 
            '<i class="fas fa-square ml-1"></i> إلغاء تحديد الكل';
    };

    // تحديث نص الزر عند تغيير التحديد
    const checkboxes = document.querySelectorAll('input[name="customer_classifications[]"]');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
            
            if (allChecked) {
                selectAllBtn.innerHTML = '<i class="fas fa-square ml-1"></i> إلغاء تحديد الكل';
            } else if (noneChecked) {
                selectAllBtn.innerHTML = '<i class="fas fa-check-square ml-1"></i> تحديد الكل';
            } else {
                selectAllBtn.innerHTML = '<i class="fas fa-check-square ml-1"></i> تحديد الكل';
            }
        });
    });
});

function resetForm() {
    Swal.fire({
        title: 'إعادة تعيين البيانات',
        text: 'هل أنت متأكد من إعادة تعيين جميع البيانات للقيم الأصلية؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، أعد التعيين',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // إعادة تحميل الصفحة لإعادة تعيين القيم
            window.location.reload();
        }
    });
}
</script>
@endpush