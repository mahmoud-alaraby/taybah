<!-- resources/views/employee/systems/receipts-payments.blade.php -->
@extends('employee.layouts.app')

@section('title', 'متابعة المقبوضات والمدفوعات')
@section('page-title', 'متابعة المقبوضات والمدفوعات')
@section('page-subtitle', 'إدارة المقبوضات والمدفوعات ومتابعة التارجت الشهري')

@section('content')
<div class="space-y-6">
    <!-- Header with Target & Summary Stats -->
    <div class="bg-white shadow rounded-lg">
        <!-- Target Setting Section -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold flex items-center">
                    <i class="fas fa-bullseye ml-2"></i>
                    تحديد التارجت الشهري - {{ $months[$currentMonth] }} {{ $currentYear }}
                </h2>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $achievementPercentage }}%</div>
                    <div class="text-sm opacity-90">نسبة التحقق</div>
                </div>
            </div>
            
            <form action="{{ route('employee.receipts-payments.target') }}" method="POST" class="grid grid-cols-4 gap-3">
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
                    <button type="submit" class="w-full h-10 bg-white text-red-600 font-bold rounded-md hover:bg-gray-50 transition-colors shadow text-sm">
                        <i class="fas fa-save ml-1"></i>
                        حفظ
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="p-4 bg-gray-50">
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-green-50 p-3 rounded-lg border border-green-200 text-center">
                    <div class="text-lg font-bold text-green-600">{{ number_format($totalReceipts, 2) }}</div>
                    <div class="text-xs text-green-700">إجمالي المقبوضات</div>
                </div>
                
                <div class="bg-red-50 p-3 rounded-lg border border-red-200 text-center">
                    <div class="text-lg font-bold text-red-600">{{ number_format($totalPayments, 2) }}</div>
                    <div class="text-xs text-red-700">إجمالي المدفوعات</div>
                </div>
                
                <div class="bg-{{ $netAmount >= 0 ? 'blue' : 'orange' }}-50 p-3 rounded-lg border border-{{ $netAmount >= 0 ? 'blue' : 'orange' }}-200 text-center">
                    <div class="text-lg font-bold text-{{ $netAmount >= 0 ? 'blue' : 'orange' }}-600">{{ number_format($netAmount, 2) }}</div>
                    <div class="text-xs text-{{ $netAmount >= 0 ? 'blue' : 'orange' }}-700">{{ $netAmount >= 0 ? 'سيولة متاحة' : 'عجز' }}</div>
                </div>
                
                <div class="bg-purple-50 p-3 rounded-lg border border-purple-200 text-center">
                    <div class="text-lg font-bold text-purple-600">{{ number_format($targetAmount, 2) }}</div>
                    <div class="text-xs text-purple-700">التارجت المحدد</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="grid grid-cols-6 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث في البيان</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="مثال: بروفايل الأستاذ علي"
                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">السنة</label>
                <select name="year" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                <select name="month" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <button type="submit" class="w-full h-9 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium text-sm">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
            
            <div>
                <a href="{{ route('employee.receipts-payments.print', ['year' => $currentYear, 'month' => $currentMonth, 'search' => $search]) }}" 
                   target="_blank"
                   class="w-full h-9 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </a>
            </div>
        </form>
    </div>

    <!-- Main Content: Tables -->
    <div class="grid grid-cols-2 gap-6">
        <!-- المقبوضات -->
        <div class="bg-white shadow rounded-lg">
            <div class="bg-green-600 text-white px-4 py-3 flex items-center justify-between">
                <h3 class="font-bold flex items-center">
                    <i class="fas fa-arrow-down ml-2"></i>
                    المقبوضات ({{ $receipts->total() }})
                </h3>
                <button onclick="toggleForm('receiptForm')" class="bg-white text-green-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة
                </button>
            </div>

            <!-- Receipt Form -->
            <div id="receiptForm" class="hidden bg-green-50 border-b border-green-200 p-4">
                <form action="{{ route('employee.receipts-payments.receipt.store') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea name="description" rows="2" placeholder="وصف المقبوض..." required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm resize-none"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                <input type="number" name="amount" placeholder="0.00" step="0.01" min="0.01" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm text-center">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>
                        </div>
                        
                        <div class="flex space-x-2 space-x-reverse">
                            <button type="submit" class="flex-1 h-8 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm">
                                حفظ
                            </button>
                            <button type="button" onclick="toggleForm('receiptForm')" class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                                إلغاء
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Receipts Table -->
            <div class="overflow-hidden">
                @if($receipts->count() > 0)
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($receipts as $receipt)
                                <tr class="hover:bg-green-50">
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($receipt->description, 30) }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-green-600">{{ number_format($receipt->amount, 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-500">{{ $receipt->date->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-sm">
                                        <button onclick="deleteItem('receipt', {{ $receipt->id }})" 
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    @if($receipts->hasPages())
                        <div class="px-4 py-3 border-t">
                            {{ $receipts->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">لا توجد مقبوضات</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- المدفوعات -->
        <div class="bg-white shadow rounded-lg">
            <div class="bg-red-600 text-white px-4 py-3 flex items-center justify-between">
                <h3 class="font-bold flex items-center">
                    <i class="fas fa-arrow-up ml-2"></i>
                    المدفوعات ({{ $payments->total() }})
                </h3>
                <button onclick="toggleForm('paymentForm')" class="bg-white text-red-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة
                </button>
            </div>

            <!-- Payment Form -->
            <div id="paymentForm" class="hidden bg-red-50 border-b border-red-200 p-4">
                <form action="{{ route('employee.receipts-payments.payment.store') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea name="description" rows="2" placeholder="وصف المدفوع..." required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                <input type="number" name="amount" placeholder="0.00" step="0.01" min="0.01" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm text-center">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                            </div>
                        </div>
                        
                        <div class="flex space-x-2 space-x-reverse">
                            <button type="submit" class="flex-1 h-8 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                                حفظ
                            </button>
                            <button type="button" onclick="toggleForm('paymentForm')" class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                                إلغاء
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="overflow-hidden">
                @if($payments->count() > 0)
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-red-50">
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($payment->description, 30) }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-red-600">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-500">{{ $payment->date->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-sm">
                                        <button onclick="deleteItem('payment', {{ $payment->id }})" 
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    @if($payments->hasPages())
                        <div class="px-4 py-3 border-t">
                            {{ $payments->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">لا توجد مدفوعات</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleForm(formId) {
    const form = document.getElementById(formId);
    const isHidden = form.classList.contains('hidden');
    
    // إخفاء كل الفورمز الأخرى
    document.querySelectorAll('[id$="Form"]').forEach(f => {
        if (f.id !== formId) f.classList.add('hidden');
    });
    
    // toggle الفورم المطلوب
    form.classList.toggle('hidden');
    
    if (isHidden) {
        // focus على أول input
        setTimeout(() => {
            const firstInput = form.querySelector('textarea, input[type="text"], input[type="number"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

function deleteItem(type, id) {
    const title = type === 'receipt' ? 'حذف المقبوض' : 'حذف المدفوع';
    const text = type === 'receipt' ? 'هل أنت متأكد من حذف هذا المقبوض؟' : 'هل أنت متأكد من حذف هذا المدفوع؟';
    
    Swal.fire({
        title: title,
        text: text,
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
            form.action = `/employee/receipts-payments/${type}/${id}`;
            
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

// Auto-hide forms after successful submission
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('#receiptForm, #paymentForm');
        forms.forEach(form => form.classList.add('hidden'));
    });
@endif
</script>
@endpush