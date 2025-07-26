@extends('employee.layouts.app')

@section('title', 'متابعة حركة العملاء')
@section('page-title', 'متابعة حركة العملاء')
@section('page-subtitle', 'إدارة ومتابعة حركة العملاء والاتفاقيات والتارجت الشهري')

@section('content')
<div class="space-y-6">
    <!-- Header with Target & Summary Stats -->
    <div class="bg-white shadow rounded-lg">
        <!-- Target Setting Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold flex items-center">
                    <i class="fas fa-bullseye ml-2"></i>
                    تحديد تارجت الاتفاق - {{ $months[$currentMonth] }} {{ $currentYear }}
                </h2>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $achievementPercentage }}%</div>
                    <div class="text-sm opacity-90">نسبة التحقق</div>
                </div>
            </div>
            
            <form action="{{ route('employee.customer-movement.target') }}" method="POST" class="grid grid-cols-4 gap-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1 text-white opacity-90">السنة</label>
                    <select name="year" class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm">
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium mb-1 text-white opacity-90">الشهر</label>
                    <select name="month" class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium mb-1 text-white opacity-90">مبلغ التارجت (ريال)</label>
                    <input type="number" name="target_amount" value="{{ $targetAmount }}" step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm text-center font-bold">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full h-10 bg-white text-blue-600 font-bold rounded-md hover:bg-gray-50 transition-colors shadow text-sm">
                        <i class="fas fa-save ml-1"></i>
                        حفظ
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="p-4 bg-gray-50">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-green-50 p-3 rounded-lg border border-green-200 text-center">
                    <div class="text-lg font-bold text-green-600">{{ number_format($totalAgreed, 2) }}</div>
                    <div class="text-xs text-green-700">المبالغ المتفق عليها</div>
                </div>
                
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-center">
                    <div class="text-lg font-bold text-blue-600">{{ number_format($totalPaid, 2) }}</div>
                    <div class="text-xs text-blue-700">المبالغ المدفوعة</div>
                </div>
                
                <div class="bg-red-50 p-3 rounded-lg border border-red-200 text-center">
                    <div class="text-lg font-bold text-red-600">{{ number_format($totalDebts, 2) }}</div>
                    <div class="text-xs text-red-700">الديون</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Customer Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="bg-blue-600 text-white px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold flex items-center">
                <i class="fas fa-plus ml-2"></i>
                إضافة عميل جديد
            </h3>
            <button onclick="toggleForm('customerForm')" class="bg-white text-blue-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                <i class="fas fa-plus ml-1"></i>
                إضافة
            </button>
        </div>

        <div id="customerForm" class="hidden bg-blue-50 border-b border-blue-200 p-4">
            <form action="{{ route('employee.customer-movement.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- معلومات العميل -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700">معلومات العميل</h5>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل</label>
                            <input type="text" name="customer_name" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="اسم العميل">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                            <input type="text" name="customer_phone" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="0501234567">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل</label>
                            <textarea name="work_description" rows="3" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm resize-none"
                                   placeholder="وصف تفصيلي للعمل"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">حالة العمل</label>
                                <select name="work_status" required class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @foreach($workStatuses as $key => $status)
                                        <option value="{{ $key }}" {{ $key == 'جاري العمل' ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- معلومات الاتفاق -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700">معلومات الاتفاق</h5>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">بداية الاتفاق</label>
                                <input type="date" name="agreement_start_date" value="{{ date('Y-m-d') }}" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التسليم الأولي</label>
                                <input type="date" name="initial_delivery_date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التسليم النهائي</label>
                                <input type="date" name="final_delivery_date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المتفق عليه (ريال)</label>
                            <input type="number" name="agreed_amount" step="0.01" min="0" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="0.00">
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دفعة 1</label>
                                <input type="number" name="first_payment" step="0.01" min="0" value="0"
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="0.00">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دفعة 2</label>
                                <input type="number" name="second_payment" step="0.01" min="0" value="0"
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="0.00">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دفعة 3</label>
                                <input type="number" name="third_payment" step="0.01" min="0" value="0"
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="0.00">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دفعة 4</label>
                                <input type="number" name="fourth_payment" step="0.01" min="0" value="0"
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-2 space-x-reverse mt-4">
                    <button type="submit" class="flex-1 h-9 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium text-sm">
                        حفظ العميل
                    </button>
                    <button type="button" onclick="toggleForm('customerForm')" class="h-9 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="grid grid-cols-6 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="اسم العميل، الجوال، أو وصف العمل"
                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع العميل</label>
                <select name="customer_type" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">جميع الأنواع</option>
                    @foreach($customerTypes as $key => $type)
                        <option value="{{ $key }}" {{ $key == $customerType ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">حالة العمل</label>
                <select name="work_status" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">جميع الحالات</option>
                    @foreach($workStatuses as $key => $status)
                        <option value="{{ $key }}" {{ $key == $workStatus ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="w-full h-9 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors font-medium text-sm">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
            
            <div>
                <a href="{{ route('employee.customer-movement.print', request()->query()) }}" 
                   target="_blank"
                   class="w-full h-9 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </a>
            </div>
        </form>
    </div>

    <!-- Main Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold flex items-center">
                <i class="fas fa-users ml-2"></i>
                حركة العملاء ({{ $movements->total() }})
            </h3>
        </div>

        @if($movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">العميل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">وصف العمل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">التواريخ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبالغ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">الدفعات</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع/الحالة</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $movement->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $movement->customer_phone }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($movement->work_description, 40) }}</div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-500">
                                        <div>بداية: {{ $movement->agreement_start_date->format('Y-m-d') }}</div>
                                        <div>أولي: {{ $movement->initial_delivery_date->format('Y-m-d') }}</div>
                                        <div>نهائي: {{ $movement->final_delivery_date->format('Y-m-d') }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-green-600">{{ number_format($movement->agreed_amount, 2) }}</div>
                                        <div class="text-xs text-red-600">متبقي: {{ number_format($movement->remaining_amount, 2) }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-xs space-y-1">
                                        <div>1: {{ number_format($movement->first_payment, 2) }}</div>
                                        <div>2: {{ number_format($movement->second_payment, 2) }}</div>
                                        <div>3: {{ number_format($movement->third_payment, 2) }}</div>
                                        <div>4: {{ number_format($movement->fourth_payment, 2) }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $movement->customer_type }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $movement->work_status == 'جاري العمل' ? 'bg-blue-100 text-blue-800' : 
                                                   ($movement->work_status == 'تم الانتهاء' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $movement->work_status }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2 space-x-reverse">
                                        <button onclick="editMovement({{ $movement->id }})" 
                                                class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteMovement({{ $movement->id }})" 
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($movements->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $movements->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد حركة عملاء</h3>
                <p class="text-gray-600 mb-4">لم يتم العثور على أي حركة عملاء للفترة المحددة</p>
                <button onclick="toggleForm('customerForm')" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول عميل
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تعديل حركة العميل</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div id="editFormContent">
                    <!-- سيتم ملء المحتوى ديناميكياً -->
                </div>
                <div class="flex items-center justify-end space-x-2 space-x-reverse mt-6">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
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
function toggleForm(formId) {
    const form = document.getElementById(formId);
    form.classList.toggle('hidden');
    
    if (!form.classList.contains('hidden')) {
        // focus على أول input
        setTimeout(() => {
            const firstInput = form.querySelector('input[type="text"], textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

function deleteMovement(id) {
    Swal.fire({
        title: 'حذف حركة العميل',
        text: 'هل أنت متأكد من حذف هذه الحركة؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc143c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/customer-movement/${id}`;
            
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfField);
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function editMovement(id) {
    // الذهاب لصفحة التعديل
    window.location.href = `/employee/customer-movement/edit/${id}`;
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Auto-hide forms after successful submission
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('customerForm');
        if (form) form.classList.add('hidden');
    });
@endif
</script>
@endpush