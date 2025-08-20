@extends('employee.layouts.app')

@section('title', 'تعديل حركة العميل')
@section('page-title', 'تعديل حركة العميل')
@section('page-subtitle', 'تعديل بيانات حركة العميل والاتفاق')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-xl border border-gray-200 mb-8">
        <!-- الهيدر بوردر جراديانت مع أيقونة + العودة للقائمة -->
        <div class="rounded-t-xl bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-edit w-6 h-6 text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white">تعديل حركة العميل</h1>
                        <p class="text-red-100 text-sm mt-1">تحديث معلومات الاتفاق وتحرير بيانات العميل</p>
                    </div>
                </div>
                <a href="{{ route('employee.customer-movement') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20 gap-2">
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form action="{{ route('employee.customer-movement.update', $customerMovement) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- معلومات العميل -->
                <div class="space-y-5">
                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                        <i class="fas fa-info-circle text-red-400"></i> معلومات العميل
                    </h4>
                    <div>
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-user text-red-400"></i> اسم العميل <span class="text-red-500">*</span>
                        </label>
                        <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name', $customerMovement->customer_name) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                               placeholder="أدخل اسم العميل" required>
                        @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-phone-alt text-red-400"></i> رقم جوال العميل <span class="text-red-500">*</span>
                        </label>
                        <input id="customer_phone" type="text" name="customer_phone" value="{{ old('customer_phone', $customerMovement->customer_phone) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                               placeholder="مثال: 0501234567" required>
                        @error('customer_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="work_description" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-align-right text-red-400"></i> وصف العمل <span class="text-red-500">*</span>
                        </label>
                        <textarea id="work_description" name="work_description" rows="3"
                                  class="w-full bg-gray-50 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                  placeholder="وصف تفصيلي للعمل المتفق عليه" required>{{ old('work_description', $customerMovement->work_description) }}</textarea>
                        @error('work_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="customer_type" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-users text-red-400"></i> نوعية العميل <span class="text-red-500">*</span>
                            </label>
                            <select id="customer_type" name="customer_type"
                                    class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                    required>
                                @foreach($customerTypes as $key => $type)
                                    <option value="{{ $key }}" {{ (old('customer_type', $customerMovement->customer_type) == $key) ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="work_status" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-tasks text-red-400"></i> حالة العمل <span class="text-red-500">*</span>
                            </label>
                            <select id="work_status" name="work_status"
                                    class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                    required>
                                @foreach($workStatuses as $key => $status)
                                    <option value="{{ $key }}" {{ (old('work_status', $customerMovement->work_status) == $key) ? 'selected' : '' }}>
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
                <!-- بيانات الاتفاق -->
                <div class="space-y-5">
                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                        <i class="fas fa-file-contract text-red-500"></i> معلومات الاتفاق
                    </h4>
                    <div>
                        <label for="agreement_start_date" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar text-red-500"></i> بداية الاتفاق <span class="text-red-500">*</span>
                        </label>
                        <input id="agreement_start_date" type="date" name="agreement_start_date"
                               value="{{ old('agreement_start_date', $customerMovement->agreement_start_date->format('Y-m-d')) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                               required>
                        @error('agreement_start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="initial_delivery_date" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-day text-red-500"></i> موعد التسليم الأولي <span class="text-red-500">*</span>
                        </label>
                        <input id="initial_delivery_date" type="date" name="initial_delivery_date"
                               value="{{ old('initial_delivery_date', $customerMovement->initial_delivery_date->format('Y-m-d')) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-green-400 transition"
                               required>
                        @error('initial_delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="final_delivery_date" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-check text-red-500"></i> موعد التسليم النهائي <span class="text-red-500">*</span>
                        </label>
                        <input id="final_delivery_date" type="date" name="final_delivery_date"
                               value="{{ old('final_delivery_date', $customerMovement->final_delivery_date->format('Y-m-d')) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                               required>
                        @error('final_delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="agreed_amount" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-money-bill text-red-500"></i> المبلغ المتفق عليه (ريال) <span class="text-red-500">*</span>
                        </label>
                        <input id="agreed_amount" type="number" name="agreed_amount" step="0.01" min="0"
                               value="{{ old('agreed_amount', $customerMovement->agreed_amount) }}"
                               class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                               placeholder="0.00" required>
                        @error('agreed_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_payment" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-credit-card text-red-500"></i> الدفعة الأولى (ريال)
                            </label>
                            <input id="first_payment" type="number" name="first_payment" step="0.01" min="0"
                                   value="{{ old('first_payment', $customerMovement->first_payment) }}"
                                   class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                   placeholder="0.00">
                            @error('first_payment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="second_payment" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-money-check text-red-500"></i> الدفعة الثانية (ريال)
                            </label>
                            <input id="second_payment" type="number" name="second_payment" step="0.01" min="0"
                                   value="{{ old('second_payment', $customerMovement->second_payment) }}"
                                   class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                   placeholder="0.00">
                            @error('second_payment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="third_payment" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-coins text-red-500"></i> الدفعة الثالثة (ريال)
                            </label>
                            <input id="third_payment" type="number" name="third_payment" step="0.01" min="0"
                                   value="{{ old('third_payment', $customerMovement->third_payment) }}"
                                   class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-green-400 transition"
                                   placeholder="0.00">
                            @error('third_payment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="fourth_payment" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-piggy-bank text-red-500"></i> الدفعة الرابعة (ريال)
                            </label>
                            <input id="fourth_payment" type="number" name="fourth_payment" step="0.01" min="0"
                                   value="{{ old('fourth_payment', $customerMovement->fourth_payment) }}"
                                   class="w-full h-12 bg-gray-50 rounded-lg border border-gray-300 px-3 shadow-sm focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                                   placeholder="0.00">
                            @error('fourth_payment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="bg-gray-100 p-4 mt-4 rounded-lg">
                        <h5 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-red-500"></i> ملخص المبالغ
                        </h5>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">إجمالي المدفوع:</span>
                                <span class="font-bold text-red-700" id="totalPaid">{{ number_format($customerMovement->total_paid, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">المتبقي:</span>
                                <span class="font-bold text-red-700" id="remaining">{{ number_format($customerMovement->remaining_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراء -->
            <div class="flex items-center justify-between pt-8 border-t mt-8">
                <button type="reset"
                        class="bg-gray-100 text-gray-600 px-5 py-2 rounded-lg hover:bg-gray-200 transition flex items-center gap-2 font-bold border">
                    <i class="fas fa-times"></i>
                    إلغاء
                </button>
                <button type="submit"
                        class="bg-red-600 text-white px-7 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2 font-bold">
                    <i class="fas fa-save"></i>
                    تحديث
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function calculateRemaining() {
        const agreedAmount = parseFloat(document.querySelector('input[name="agreed_amount"]').value) || 0;
        const firstPayment = parseFloat(document.querySelector('input[name="first_payment"]').value) || 0;
        const secondPayment = parseFloat(document.querySelector('input[name="second_payment"]').value) || 0;
        const thirdPayment = parseFloat(document.querySelector('input[name="third_payment"]').value) || 0;
        const fourthPayment = parseFloat(document.querySelector('input[name="fourth_payment"]').value) || 0;

        const totalPaid = firstPayment + secondPayment + thirdPayment + fourthPayment;
        const remaining = agreedAmount - totalPaid;

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

            remainingElement.className =
                'font-bold ' + (remaining < 0 ? 'text-red-700' : 'text-green-700');
        }
    }

    [
        'input[name="agreed_amount"]',
        'input[name="first_payment"]',
        'input[name="second_payment"]',
        'input[name="third_payment"]',
        'input[name="fourth_payment"]'
    ].forEach(selector => {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('input', calculateRemaining);
        }
    });
});
</script>
@endpush
