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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- إجمالي المقبوضات -->
                <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">إجمالي المقبوضات</p>
                            <p class="text-2xl font-bold">{{ number_format($totalReceipts, 2) }}</p>
                        </div>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path>
                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 1 0 12 21v0"></path>
                        </svg>
                    </div>
                </div>

                <!-- صافي السيولة أو العجز -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m0 0l-4-4m4 4l4-4"></path>
                                <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17v-8m0 0l-4 4m4-4l4 4"></path>
                                <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                            @endif
                        </svg>
                    </div>
                </div>

                <!-- التارجت المحدد -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">التارجت المحدد</p>
                            <p class="text-2xl font-bold">{{ number_format($targetAmount, 2) }}</p>
                        </div>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="bg-white shadow rounded-lg p-4 border border-gray-300">
        <form method="GET" class="grid grid-cols-6 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث في البيان</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="مثال: بروفايل الأستاذ علي"
                       class="w-full h-9 rounded-md border border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">السنة</label>
                <select name="year" class="w-full h-9 rounded-md border border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                <select name="month" class="w-full h-9 rounded-md border border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <button type="submit" class="w-full h-9 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium text-sm border border-blue-600 focus:ring-2 focus:ring-blue-400">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
            
            <div>
                <a href="{{ route('employee.receipts-payments.print', ['year' => $currentYear, 'month' => $currentMonth, 'search' => $search]) }}" 
                   target="_blank"
                   class="w-full h-9 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm flex items-center justify-center border border-green-600 focus:ring-2 focus:ring-green-400">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </a>
            </div>
        </form>
    </div>

    <!-- Main Content: Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- المقبوضات -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
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
                            <textarea name="description" rows="3" placeholder="وصف المقبوض..." required
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">البيان</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">المبلغ</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">التاريخ</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($receipts as $receipt)
                                    <tr class="hover:bg-green-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="break-words leading-relaxed">
                                                {{ $receipt->description }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-green-600 whitespace-nowrap">
                                            {{ number_format($receipt->amount, 2) }} ر.س
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $receipt->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <button onclick="editReceipt({{ $receipt->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 transition-colors" title="تعديل">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </button>
                                                <button onclick="deleteItem('receipt', {{ $receipt->id }})" 
                                                        class="text-red-600 hover:text-red-800 transition-colors" title="حذف">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($receipts->hasPages())
                        <div class="px-4 py-3 border-t bg-gray-50">
                            {{ $receipts->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">لا توجد مقبوضات</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- المدفوعات -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
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
                            <textarea name="description" rows="3" placeholder="وصف المدفوع..." required
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">البيان</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">المبلغ</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">التاريخ</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-red-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="break-words leading-relaxed">
                                                {{ $payment->description }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-red-600 whitespace-nowrap">
                                            {{ number_format($payment->amount, 2) }} ر.س
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $payment->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <button onclick="editPayment({{ $payment->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 transition-colors" title="تعديل">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </button>
                                                <button onclick="deleteItem('payment', {{ $payment->id }})" 
                                                        class="text-red-600 hover:text-red-800 transition-colors" title="حذف">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($payments->hasPages())
                        <div class="px-4 py-3 border-t bg-gray-50">
                            {{ $payments->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">لا توجد مدفوعات</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal للتعديل على المقبوضات -->
    <div id="editReceiptModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="bg-green-600 text-white px-4 py-3 rounded-t-lg">
                    <h3 class="font-bold">تعديل المقبوض</h3>
                </div>
                <form id="editReceiptForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea id="editReceiptDescription" name="description" rows="3" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                <input type="number" id="editReceiptAmount" name="amount" step="0.01" min="0.01" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm text-center">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                <input type="date" id="editReceiptDate" name="date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 rounded-b-lg flex space-x-2 space-x-reverse">
                        <button type="submit"
                            class="flex-1 h-8 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm">
                            حفظ التعديل
                        </button>
                        <button type="button" onclick="closeModal('editReceiptModal')"
                            class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal للتعديل على المدفوعات -->
    <div id="editPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="bg-red-600 text-white px-4 py-3 rounded-t-lg">
                    <h3 class="font-bold">تعديل المدفوع</h3>
                </div>
                <form id="editPaymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                            <textarea id="editPaymentDescription" name="description" rows="3" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                <input type="number" id="editPaymentAmount" name="amount" step="0.01" min="0.01" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm text-center">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1<label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                <input type="date" id="editPaymentDate" name="date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 rounded-b-lg flex space-x-2 space-x-reverse">
                        <button type="submit"
                            class="flex-1 h-8 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                            حفظ التعديل
                        </button>
                        <button type="button" onclick="closeModal('editPaymentModal')"
                            class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// منع ظهور الـ modal تلقائياً عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // التأكد من أن جميع الـ modals مخفية عند التحميل
    const modals = ['editReceiptModal', 'editPaymentModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    });

    // إخفاء الفورمز عند النجاح
    @if(session('success'))
        const forms = document.querySelectorAll('#receiptForm, #paymentForm');
        forms.forEach(form => form.classList.add('hidden'));
    @endif
});

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

function editReceipt(receiptId) {
    // منع أي events أخرى من التداخل
    event.preventDefault();
    event.stopPropagation();

    const currentParams = new URLSearchParams(window.location.search);
    const year = currentParams.get('year') || new Date().getFullYear();
    const month = currentParams.get('month') || (new Date().getMonth() + 1);

    const url = `/employee/receipts-payments/receipt/${receiptId}/edit?year=${year}&month=${month}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ملء بيانات المقبوض
                document.getElementById('editReceiptDescription').value = data.receipt.description;
                document.getElementById('editReceiptAmount').value = data.receipt.amount;
                document.getElementById('editReceiptDate').value = data.receipt.date;

                // تحديث action للفورم
                document.getElementById('editReceiptForm').action = 
                    `/employee/receipts-payments/receipt/${receiptId}?${currentParams.toString()}`;

                // إظهار الـ modal
                document.getElementById('editReceiptModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطأ في تحميل بيانات المقبوض');
        });
}

function editPayment(paymentId) {
    // منع أي events أخرى من التداخل
    event.preventDefault();
    event.stopPropagation();

    const currentParams = new URLSearchParams(window.location.search);
    const year = currentParams.get('year') || new Date().getFullYear();
    const month = currentParams.get('month') || (new Date().getMonth() + 1);

    const url = `/employee/receipts-payments/payment/${paymentId}/edit?year=${year}&month=${month}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ملء بيانات المدفوع
                document.getElementById('editPaymentDescription').value = data.payment.description;
                document.getElementById('editPaymentAmount').value = data.payment.amount;
                document.getElementById('editPaymentDate').value = data.payment.date;

                // تحديث action للفورم
                document.getElementById('editPaymentForm').action = 
                    `/employee/receipts-payments/payment/${paymentId}?${currentParams.toString()}`;

                // إظهار الـ modal
                document.getElementById('editPaymentModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطأ في تحميل بيانات المدفوع');
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// إغلاق الـ modal عند الضغط على الخلفية فقط
document.addEventListener('click', function(event) {
    // التأكد من أن الضغطة على الخلفية وليس على المحتوى
    if (event.target.classList.contains('bg-gray-600') && event.target.classList.contains('bg-opacity-50')) {
        const modals = ['editReceiptModal', 'editPaymentModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
    }
});

// منع إرسال الفورم عند الضغط على Enter في الحقول
document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' && event.target.matches('input, select')) {
        // السماح بـ Enter فقط في textarea وأزرار الإرسال
        if (!event.target.matches('textarea, button[type="submit"]')) {
            event.preventDefault();
        }
    }
});

// إضافة تأثيرات بصرية للجداول
document.addEventListener('DOMContentLoaded', function() {
    // إضافة hover effects للصفوف
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// تحسين تجربة المستخدم مع النماذج
function enhanceFormExperience() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            // إضافة تأثيرات التركيز
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2');
            });
        });
    });
}

// استدعاء تحسينات التجربة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', enhanceFormExperience);
</script>

<style>
/* تحسينات CSS إضافية */
.break-words {
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

.leading-relaxed {
    line-height: 1.625;
}

/* تأثيرات انتقالية سلسة */
.transition-colors {
    transition: color 0.2s ease-in-out;
}

/* تحسين عرض الجداول على الشاشات الصغيرة */
@media (max-width: 768px) {
    .grid-cols-1.lg\\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
    
    table {
        font-size: 0.875rem;
    }
    
    th, td {
        padding: 0.5rem 0.75rem;
    }
}

/* تحسين عرض النص العربي */
.text-right {
    text-align: right;
    direction: rtl;
}

/* تأثيرات للأزرار */
button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* تحسين عرض البطاقات الإحصائية */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* تأثيرات التمرير */
.hover\\:bg-green-50:hover {
    background-color: #f0fdf4;
}

.hover\\:bg-red-50:hover {
    background-color: #fef2f2;
}

/* تحسين عرض النماذج */
.space-y-3 > * + * {
    margin-top: 0.75rem;
}

.space-x-reverse > * + * {
    margin-right: 0.5rem;
    margin-left: 0;
}

/* تأثيرات للمودال */
.z-50 {
    z-index: 50;
}

.bg-opacity-50 {
    background-color: rgba(75, 85, 99, 0.5);
}
</style>
@endpush