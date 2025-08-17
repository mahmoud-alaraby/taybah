@extends('employee.layouts.app')

@section('title', 'تعديل العميل المحتمل')
@section('page-title', 'تعديل العميل المحتمل')
@section('page-subtitle', 'تعديل بيانات العميل المحتمل وتصنيفاته')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-edit text-white text-xl"></i>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">تعديل العميل المحتمل</h1>
                            <p class="text-red-100 text-sm mt-1">تحديث بيانات العميل وتصنيفاته</p>
                        </div>
                    </div>
                    <a href="{{ route('employee.potential-customers') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <i class="fas fa-arrow-left ml-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('employee.potential-customers.update', $potentialCustomer) }}" method="POST" class="p-6 sm:p-8">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    {{-- معلومات العميل --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center ml-3">
                                <i class="fas fa-id-card text-red-600 text-sm"></i>
                            </div>
                            معلومات العميل
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- اسم العميل --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">اسم العميل المحتمل <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $potentialCustomer->customer_name) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                                @error('customer_name')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- رقم الجوال --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">رقم الجوال <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $potentialCustomer->phone) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                                <p class="mt-1 text-xs text-gray-500">سيتم تكوين رابط الواتساب تلقائياً</p>
                                @error('phone')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- وصف العمل --}}
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">وصف العمل المطلوب <span class="text-red-500">*</span></label>
                            <textarea name="work_description" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500">{{ old('work_description', $potentialCustomer->work_description) }}</textarea>
                            @error('work_description')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ملاحظات --}}
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">ملاحظات إضافية</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-red-500">{{ old('notes', $potentialCustomer->notes) }}</textarea>
                        </div>
                    </div>

                    {{-- تصنيف العميل --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center ml-3">
                                <i class="fas fa-tags text-purple-600 text-sm"></i>
                            </div>
                            تصنيف العميل
                        </h3>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700">اختر التصنيفات المناسبة:</label>
                            <button type="button" id="selectAllBtn" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-check-square ml-1"></i> تحديد الكل
                            </button>
                        </div>
                        <div class="space-y-2 max-h-72 overflow-y-auto border border-gray-200 rounded-lg p-4">
                            @foreach($classifications as $classification)
                                <label class="flex items-start">
                                    <input type="checkbox" name="customer_classifications[]" value="{{ $classification['name'] }}" 
                                           {{ in_array($classification['name'], old('customer_classifications', $potentialCustomer->customer_classifications ?? [])) ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-blue-600 focus:ring focus:ring-blue-200">
                                    <div class="mr-2 flex-1">
                                        <span class="font-medium">{{ $classification['display_name'] }}</span>
                                        <p class="text-xs text-gray-500">{{ $classification['description'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @if(!empty($potentialCustomer->customer_classifications))
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
                        {{-- معاينة واتساب --}}
                        <div class="bg-blue-50 p-4 rounded-lg mt-6">
                            <h5 class="text-sm font-medium text-blue-800 mb-2">معاينة رابط الواتساب:</h5>
                            <div id="whatsapp-preview" class="text-sm text-gray-600">
                                {{ $potentialCustomer->whatsapp_link ?? 'أدخل رقم الجوال لمعاينة الرابط' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex justify-end space-x-3 space-x-reverse border-t pt-6 mt-8">
                    <a href="{{ route('employee.potential-customers') }}" 
                       class="inline-flex items-center px-6 py-3 bg-white border rounded-lg text-gray-700 hover:bg-gray-50">
                       <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-700 to-red-800 text-white rounded-lg shadow hover:from-red-900 hover:to-red-900">
                        <i class="fas fa-save ml-2"></i>
                        تحديث
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
    const phoneInput = document.querySelector('input[name="phone"]');
    const whatsappPreview = document.getElementById('whatsapp-preview');
    const selectAllBtn = document.getElementById('selectAllBtn');

    function generateWhatsappLink(phone) {
        if (!phone) return 'أدخل رقم الجوال لمعاينة الرابط';
        let cleanPhone = phone.replace(/[^0-9]/g, '');
        if (!cleanPhone.startsWith('966')) {
            if(cleanPhone.startsWith('0')) cleanPhone = '966' + cleanPhone.substring(1);
            else cleanPhone = '966' + cleanPhone;
        }
        const url = `https://api.whatsapp.com/send/?phone=${cleanPhone}`;
        return `<a href="${url}" target="_blank" class="text-blue-600 underline">${url}</a>`;
    }

    phoneInput.addEventListener('input', () => {
        whatsappPreview.innerHTML = generateWhatsappLink(phoneInput.value);
    });

    // تحديد/إلغاء الكل
    selectAllBtn.addEventListener('click', () => {
        const boxes = document.querySelectorAll('input[name="customer_classifications[]"]');
        const allChecked = Array.from(boxes).every(b => b.checked);
        boxes.forEach(b => b.checked = !allChecked);
        selectAllBtn.innerHTML = allChecked ?
            '<i class="fas fa-check-square ml-1"></i> تحديد الكل' :
            '<i class="fas fa-square ml-1"></i> إلغاء تحديد الكل';
    });
});
</script>
@endpush
