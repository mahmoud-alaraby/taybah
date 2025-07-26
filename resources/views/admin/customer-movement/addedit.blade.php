@extends('admin.layouts.app')

@section('title', isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد')
@section('page-title', isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد')
@section('page-subtitle', isset($customerMovement) ? 'تعديل بيانات حركة العميل والاتفاق' : 'إضافة حركة عميل جديد وتفاصيل الاتفاق')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <i class="fas fa-{{ isset($customerMovement) ? 'edit' : 'plus' }} ml-2"></i>
                {{ isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد' }}
            </h3>
        </div>

        <form action="{{ isset($customerMovement) ? route('admin.customer-movement.update', $customerMovement) : route('admin.customer-movement.store') }}" method="POST" class="p-6">
            @csrf
            @if(isset($customerMovement))
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
                                    {{ (old('employee_id', $customerMovement->employee_id ?? '') == $employee->id) ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $customerMovement->customer_name ?? '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="أدخل اسم العميل" required>
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم جوال العميل <span class="text-red-500">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $customerMovement->customer_phone ?? '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="مثال: 0501234567" required>
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل <span class="text-red-500">*</span></label>
                        <textarea name="work_description" rows="4" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="وصف تفصيلي للعمل المتفق عليه" required>{{ old('work_description', $customerMovement->work_description ?? '') }}</textarea>
                        @error('work_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوعية العميل <span class="text-red-500">*</span></label>
                            <select name="customer_type" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach($customerTypes as $key => $type)
                                    <option value="{{ $key }}" 
                                        {{ (old('customer_type', $customerMovement->customer_type ?? 'غير محدد') == $key) ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حالة العمل <span class="text-red-500">*</span></label>
                            <select name="work_status" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach($workStatuses as $key => $status)
                                    <option value="{{ $key }}" 
                                        {{ (old('work_status', $customerMovement->work_status ?? 'جاري العمل') == $key) ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('work_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- معلومات الاتفاق -->
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-900 border-b pb-2">معلومات الاتفاق</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">بداية الاتفاق <span class="text-red-500">*</span></label>
                        <input type="date" name="agreement_start_date" 
                               value="{{ old('agreement_start_date', isset($customerMovement) ? $customerMovement->agreement_start_date->format('Y-m-d') : '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('agreement_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">موعد التسليم الأولي <span class="text-red-500">*</span></label>
                        <input type="date" name="initial_delivery_date" 
                               value="{{ old('initial_delivery_date', isset($customerMovement) ? $customerMovement->initial_delivery_date->format('Y-m-d') : '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('initial_delivery_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">موعد التسليم النهائي <span class="text-red-500">*</span></label>
                        <input type="date" name="final_delivery_date" 
                               value="{{ old('final_delivery_date', isset($customerMovement) ? $customerMovement->final_delivery_date->format('Y-m-d') : '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('final_delivery_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المتفق عليه (ريال) <span class="text-red-500">*</span></label>
                        <input type="number" name="agreed_amount" step="0.01" min="0" 
                               value="{{ old('agreed_amount', $customerMovement->agreed_amount ?? '') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="0.00" required>
                        @error('agreed_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدفعة الأولى (ريال)</label>
                            <input type="number" name="first_payment" step="0.01" min="0" 
                                   value="{{ old('first_payment', $customerMovement->first_payment ?? '0') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="0.00">
                            @error('first_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدفعة الثانية (ريال)</label>
                            <input type="number" name="second_payment" step="0.01" min="0" 
                                   value="{{ old('second_payment', $customerMovement->second_payment ?? '0') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="0.00">
                            @error('second_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدفعة الثالثة (ريال)</label>
                            <input type="number" name="third_payment" step="0.01" min="0" 
                                   value="{{ old('third_payment', $customerMovement->third_payment ?? '0') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="0.00">
                            @error('third_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الدفعة الرابعة (ريال)</label>
                            <input type="number" name="fourth_payment" step="0.01" min="0" 
                                   value="{{ old('fourth_payment', $customerMovement->fourth_payment ?? '0') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="0.00">
                            @error('fourth_payment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if(isset($customerMovement))
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">ملخص المبالغ</h5>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">إجمالي المدفوع:</span>
                                    <span class="font-medium text-green-600" id="totalPaid">{{ number_format($customerMovement->total_paid, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">المتبقي:</span>
                                    <span class="font-medium text-red-600" id="remaining">{{ number_format($customerMovement->remaining_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t mt-6">
                <a href="{{ route('admin.customer-movement.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
                
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-save ml-2"></i>
                    {{ isset($customerMovement) ? 'تحديث' : 'حفظ' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // حساب المتبقي عند تغيير المبالغ
    function calculateRemaining() {
        const agreedAmount = parseFloat(document.querySelector('input[name="agreed_amount"]').value) || 0;
        const firstPayment = parseFloat(document.querySelector('input[name="first_payment"]').value) || 0;
        const secondPayment = parseFloat(document.querySelector('input[name="second_payment"]').value) || 0;
        const thirdPayment = parseFloat(document.querySelector('input[name="third_payment"]').value) || 0;
        const fourthPayment = parseFloat(document.querySelector('input[name="fourth_payment"]').value) || 0;
        
        const totalPaid = firstPayment + secondPayment + thirdPayment + fourthPayment;
        const remaining = agreedAmount - totalPaid;
        
        // تحديث العرض إذا كان موجود
        const totalPaidElement = document.getElementById('totalPaid');
        const remainingElement = document.getElementById('remaining');
        
        if (totalPaidElement && remainingElement) {
            totalPaidElement.textContent = totalPaid.toLocaleString('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            remainingElement.textContent = remaining.toLocaleString('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            // تغيير لون المتبقي حسب القيمة
            remainingElement.className = remaining < 0 ? 
                'font-medium text-red-600' : 
                'font-medium text-green-600';
        }
    }
    
    // إضافة event listeners للحقول المالية
    const financialInputs = [
        'input[name="agreed_amount"]',
        'input[name="first_payment"]',
        'input[name="second_payment"]',
        'input[name="third_payment"]',
        'input[name="fourth_payment"]'
    ];
    
    financialInputs.forEach(selector => {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('input', calculateRemaining);
        }
    });
});
</script>
@endpush