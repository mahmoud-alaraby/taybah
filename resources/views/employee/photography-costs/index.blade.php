@extends('employee.layouts.app')

@section('title', 'متابعة تكاليف التصوير')
@section('page-title', 'متابعة تكاليف التصوير')
@section('page-subtitle', 'إدارة تكاليف التصوير والمقبوضات والمدفوعات ومتابعة التارجت الشخصي')

@section('content')
<div class="space-y-6">
    <!-- Header with Target & Summary Stats -->
    <div class="bg-white shadow rounded-lg">
        <!-- Target Display Section -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold flex items-center">
                    <i class="fas fa-camera ml-2"></i>
                    تارجت التصوير الشخصي - {{ $months[$currentMonth] }} {{ $currentYear }}
                </h2>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $achievementPercentage }}%</div>
                    <div class="text-sm opacity-90">نسبة التحقق</div>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <div class="text-lg font-bold">{{ number_format($targetAmount, 2) }}</div>
                    <div class="text-sm opacity-90">التارجت المحدد</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <div class="text-lg font-bold">{{ number_format($totalReceipts, 2) }}</div>
                    <div class="text-sm opacity-90">المحقق</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <div class="text-lg font-bold">{{ number_format($targetAmount - $totalReceipts, 2) }}</div>
                    <div class="text-sm opacity-90">المطلوب</div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <!-- إجمالي المقبوضات -->
                <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">إجمالي المقبوضات</p>
                            <p class="text-2xl font-bold">{{ number_format($totalReceipts, 2) }}</p>
                        </div>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- إجمالي المدفوعات -->
                <div class="bg-gradient-to-r from-red-400 to-red-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">إجمالي المدفوعات</p>
                            <p class="text-2xl font-bold">{{ number_format($totalPayments, 2) }}</p>
                        </div>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- صافي السيولة -->
                <div class="rounded-xl p-4 text-white shadow-lg
                    {{ $netAmount >= 0 
                        ? 'bg-gradient-to-r from-blue-500 to-blue-700' 
                        : 'bg-gradient-to-r from-orange-400 to-orange-600' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">{{ $netAmount >= 0 ? 'سيولة متاحة' : 'عجز' }}</p>
                            <p class="text-2xl font-bold">{{ number_format($netAmount, 2) }}</p>
                        </div>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($netAmount >= 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="grid grid-cols-5 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث في البيان</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="مثال: معدات تصوير، إضاءة"
                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">السنة</label>
                <select name="year" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                <select name="month" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
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
        </form>
        
        <div class="mt-3 flex justify-end">
            <a href="{{ route('employee.photography-costs.print', ['year' => $currentYear, 'month' => $currentMonth, 'search' => $search]) }}" 
               target="_blank"
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm">
                <i class="fas fa-print ml-1"></i>
                طباعة التقرير
            </a>
        </div>
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
                <form action="{{ route('employee.photography-costs.receipt.store') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea name="note" rows="2" placeholder="وصف المقبوض..." required
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
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المنشئ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($receipts as $receipt)
                                <tr class="hover:bg-green-50">
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $receipt->creator_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $receipt->creator_id }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($receipt->note, 25) }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-green-600">{{ number_format($receipt->amount, 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-500">{{ $receipt->date->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-sm">
                                        @if($receipt->created_by_type == 'employee' && $receipt->created_by == auth('employee')->id())
                                            <button onclick="deleteItem('receipt', {{ $receipt->id }})" 
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">منشأ بواسطة الإدارة</span>
                                        @endif
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
                <form action="{{ route('employee.photography-costs.payment.store') }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea name="note" rows="2" placeholder="وصف المدفوع..." required
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
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المنشئ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-red-50">
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        <div class="font-medium">{{ $payment->creator_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->creator_id }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ Str::limit($payment->note, 25) }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-red-600">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-500">{{ $payment->date->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-sm">
                                        @if($payment->created_by_type == 'employee' && $payment->created_by == auth('employee')->id())
                                            <button onclick="deleteItem('payment', {{ $payment->id }})" 
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">منشأ بواسطة الإدارة</span>
                                        @endif
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
            const firstInput = form.querySelector('select, textarea, input[type="text"], input[type="number"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

function deleteItem(type, id) {
    const title = type === 'receipt' ? 'حذف المقبوض' : 'حذف المدفوع';
    const text = type === 'receipt' ? 'هل أنت متأكد من حذف هذا المقبوض؟' : 'هل أنت متأكد من حذف هذا المدفوع؟';
    
    confirmDelete(title, text).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/photography-costs/${type}/${id}`;
            
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