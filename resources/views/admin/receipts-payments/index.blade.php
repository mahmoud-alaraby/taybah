@extends('admin.layouts.app')

@section('title', 'متابعة المقبوضات والمدفوعات')
@section('page-title', 'متابعة المقبوضات والمدفوعات')
@section('page-subtitle', 'إدارة المقبوضات والمدفوعات ومتابعة التارجت الشهري لجميع الموظفين')

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

                <form action="{{ route('admin.receipts-payments.target') }}" method="POST" class="grid grid-cols-5 gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium mb-1 text-white opacity-90">الموظف</label>
                        <select name="employee_id"
                            class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm"
                            required>
                            <option value="">اختر الموظف</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $employee->id == $employeeId ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-white opacity-90">السنة</label>
                        <select name="year"
                            class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm">
                            @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-white opacity-90">الشهر</label>
                        <select name="month"
                            class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm">
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-white opacity-90">مبلغ التارجت (ريال)</label>
                        <input type="number" name="target_amount" value="{{ $targetAmount }}" step="0.01"
                            min="0" placeholder="0.00"
                            class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm text-center font-bold">
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full h-10 bg-white text-red-600 font-bold rounded-md hover:bg-gray-50 transition-colors shadow text-sm">
                            <i class="fas fa-save ml-1"></i>
                            حفظ
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- إجمالي المقبوضات -->
                    <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">إجمالي المقبوضات</p>
                                <p class="text-2xl font-bold">{{ number_format($totalReceipts, 2) }}</p>
                            </div>
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3">
                                </path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-1.414 1.414A9 9 0 1 0 12 21v0"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- صافي السيولة -->
                    <div
                        class="rounded-xl p-4 text-white shadow-lg
            {{ $netAmount >= 0
                ? 'bg-gradient-to-r from-blue-500 to-blue-700'
                : 'bg-gradient-to-r from-orange-400 to-orange-600' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">{{ $netAmount >= 0 ? 'سيولة متاحة' : 'عجز' }}</p>
                                <p class="text-2xl font-bold">{{ number_format($netAmount, 2) }}</p>
                            </div>
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if ($netAmount >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v8m0 0l-4-4m4 4l4-4"></path>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 17v-8m0 0l-4 4m4-4l4 4"></path>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                @endif
                            </svg>
                        </div>
                    </div>
                    <!-- التارجتات -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl p-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">{{ $employeeId ? 'تارجت الموظف' : 'إجمالي التارجتات' }}</p>
                                <p class="text-2xl font-bold">{{ number_format($targetAmount, 2) }}</p>
                            </div>
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                                <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-white shadow rounded-lg p-4">
            <form method="GET" class="grid grid-cols-7 gap-3 items-end">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">البحث في البيان</label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="مثال: بروفايل الأستاذ علي"
                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                    <select name="employee_id"
                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="">جميع الموظفين</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $employee->id == $employeeId ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">السنة</label>
                    <select name="year"
                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                        @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الشهر</label>
                    <select name="month"
                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit"
                        class="w-full h-9 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium text-sm">
                        <i class="fas fa-search ml-1"></i>
                        بحث
                    </button>
                </div>

                <div>
                    <a href="{{ route('admin.receipts-payments.print', ['year' => $currentYear, 'month' => $currentMonth, 'search' => $search, 'employee_id' => $employeeId]) }}"
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
                    <button onclick="toggleForm('receiptForm')"
                        class="bg-white text-green-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                        <i class="fas fa-plus ml-1"></i>
                        إضافة
                    </button>
                </div>

                <!-- Receipt Form -->
                <div id="receiptForm" class="hidden bg-green-50 border-b border-green-200 p-4">
                    <form action="{{ route('admin.receipts-payments.receipt.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                                <select name="employee_id"
                                    class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm"
                                    required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ $employee->id == $employeeId ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                                <textarea name="description" rows="2" placeholder="وصف المقبوض..." required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                    <input type="number" name="amount" placeholder="0.00" step="0.01"
                                        min="0.01" required
                                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm text-center">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                            </div>

                            <div class="flex space-x-2 space-x-reverse">
                                <button type="submit"
                                    class="flex-1 h-8 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium text-sm">
                                    حفظ
                                </button>
                                <button type="button" onclick="toggleForm('receiptForm')"
                                    class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Receipts Table -->
                <div class="overflow-hidden">
                    @if ($receipts->count() > 0)
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($receipts as $receipt)
                                    <tr class="hover:bg-green-50">
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            <div class="font-medium">{{ $receipt->employee->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $receipt->employee->employee_id }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            {{ Str::limit($receipt->description, 25) }}</td>
                                        <td class="px-3 py-2 text-sm font-medium text-green-600">
                                            {{ number_format($receipt->amount, 2) }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $receipt->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 text-sm">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <button onclick="editReceipt({{ $receipt->id }})"
                                                    class="text-blue-600 hover:text-blue-800" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteItem('receipt', {{ $receipt->id }})"
                                                    class="text-red-600 hover:text-red-800" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        @if ($receipts->hasPages())
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
                    <button onclick="toggleForm('paymentForm')"
                        class="bg-white text-red-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                        <i class="fas fa-plus ml-1"></i>
                        إضافة
                    </button>
                </div>

                <!-- Payment Form -->
                <div id="paymentForm" class="hidden bg-red-50 border-b border-red-200 p-4">
                    <form action="{{ route('admin.receipts-payments.payment.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                                <select name="employee_id"
                                    class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                    required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ $employee->id == $employeeId ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                                <textarea name="description" rows="2" placeholder="وصف المدفوع..." required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                    <input type="number" name="amount" placeholder="0.00" step="0.01"
                                        min="0.01" required
                                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm text-center">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                                </div>
                            </div>

                            <div class="flex space-x-2 space-x-reverse">
                                <button type="submit"
                                    class="flex-1 h-8 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
                                    حفظ
                                </button>
                                <button type="button" onclick="toggleForm('paymentForm')"
                                    class="h-8 px-4 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-sm">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Payments Table -->
                <div class="overflow-hidden">
                    @if ($payments->count() > 0)
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">البيان</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            <div class="font-medium">{{ $payment->employee->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->employee->employee_id }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-900">
                                            {{ Str::limit($payment->description, 25) }}</td>
                                        <td class="px-3 py-2 text-sm font-medium text-red-600">
                                            {{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $payment->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 text-sm">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <button onclick="editPayment({{ $payment->id }})"
                                                    class="text-blue-600 hover:text-blue-800" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteItem('payment', {{ $payment->id }})"
                                                    class="text-red-600 hover:text-red-800" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        @if ($payments->hasPages())
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                                <select id="editReceiptEmployeeId" name="employee_id"
                                    class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm"
                                    required>
                                    <!-- سيتم ملؤها بواسطة JavaScript -->
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                                <textarea id="editReceiptDescription" name="description" rows="2" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                    <input type="number" id="editReceiptAmount" name="amount" step="0.01"
                                        min="0.01" required
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                                <select id="editPaymentEmployeeId" name="employee_id"
                                    class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                    required>
                                    <!-- سيتم ملؤها بواسطة JavaScript -->
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">البيان</label>
                                <textarea id="editPaymentDescription" name="description" rows="2" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ريال)</label>
                                    <input type="number" id="editPaymentAmount" name="amount" step="0.01"
                                        min="0.01" required
                                        class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm text-center">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
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
            @if (session('success'))
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
                    const firstInput = form.querySelector(
                        'select, textarea, input[type="text"], input[type="number"]');
                    if (firstInput) firstInput.focus();
                }, 100);
            }
        }

        function deleteItem(type, id) {
            const title = type === 'receipt' ? 'حذف المقبوض' : 'حذف المدفوع';
            const text = type === 'receipt' ? 'هل أنت متأكد من حذف هذا المقبوض؟' : 'هل أنت متأكد من حذف هذا المدفوع؟';

            // التأكد من وجود confirmDelete function
            if (typeof confirmDelete === 'function') {
                confirmDelete(title, text).then((result) => {
                    if (result.isConfirmed) {
                        submitDeleteForm(type, id);
                    }
                });
            } else {
                // fallback في حالة عدم وجود confirmDelete
                if (confirm(text)) {
                    submitDeleteForm(type, id);
                }
            }
        }

        function submitDeleteForm(type, id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/receipts-payments/${type}/${id}`;

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

        function editReceipt(receiptId) {
            // منع أي events أخرى من التداخل
            event.preventDefault();
            event.stopPropagation();

            const currentParams = new URLSearchParams(window.location.search);
            const year = currentParams.get('year') || new Date().getFullYear();
            const month = currentParams.get('month') || (new Date().getMonth() + 1);
            const employeeId = currentParams.get('employee_id') || '';

            const url = `/admin/receipts-payments/receipt/${receiptId}/edit?year=${year}&month=${month}&employee_id=${employeeId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ملء بيانات الموظفين
                        const employeeSelect = document.getElementById('editReceiptEmployeeId');
                        employeeSelect.innerHTML = '<option value="">اختر الموظف</option>';
                        data.employees.forEach(employee => {
                            const selected = employee.id === data.receipt.employee_id ? 'selected' : '';
                            employeeSelect.innerHTML += 
                                `<option value="${employee.id}" ${selected}>${employee.name} (${employee.employee_id})</option>`;
                        });

                        // ملء بيانات المقبوض
                        document.getElementById('editReceiptDescription').value = data.receipt.description;
                        document.getElementById('editReceiptAmount').value = data.receipt.amount;
                        document.getElementById('editReceiptDate').value = data.receipt.date;

                        // تحديث action للفورم
                        document.getElementById('editReceiptForm').action = 
                            `/admin/receipts-payments/receipt/${receiptId}?${currentParams.toString()}`;

                        // إظهار الـ modal
                        document.getElementById('editReceiptModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showAlert === 'function') {
                        showAlert('خطأ في تحميل بيانات المقبوض', 'error');
                    } else {
                        alert('خطأ في تحميل بيانات المقبوض');
                    }
                });
        }

        function editPayment(paymentId) {
            // منع أي events أخرى من التداخل
            event.preventDefault();
            event.stopPropagation();

            const currentParams = new URLSearchParams(window.location.search);
            const year = currentParams.get('year') || new Date().getFullYear();
            const month = currentParams.get('month') || (new Date().getMonth() + 1);
            const employeeId = currentParams.get('employee_id') || '';

            const url = `/admin/receipts-payments/payment/${paymentId}/edit?year=${year}&month=${month}&employee_id=${employeeId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ملء بيانات الموظفين
                        const employeeSelect = document.getElementById('editPaymentEmployeeId');
                        employeeSelect.innerHTML = '<option value="">اختر الموظف</option>';
                        data.employees.forEach(employee => {
                            const selected = employee.id === data.payment.employee_id ? 'selected' : '';
                            employeeSelect.innerHTML += 
                                `<option value="${employee.id}" ${selected}>${employee.name} (${employee.employee_id})</option>`;
                        });

                        // ملء بيانات المدفوع
                        document.getElementById('editPaymentDescription').value = data.payment.description;
                        document.getElementById('editPaymentAmount').value = data.payment.amount;
                        document.getElementById('editPaymentDate').value = data.payment.date;

                        // تحديث action للفورم
                        document.getElementById('editPaymentForm').action = 
                            `/admin/receipts-payments/payment/${paymentId}?${currentParams.toString()}`;

                        // إظهار الـ modal
                        document.getElementById('editPaymentModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showAlert === 'function') {
                        showAlert('خطأ في تحميل بيانات المدفوع', 'error');
                    } else {
                        alert('خطأ في تحميل بيانات المدفوع');
                    }
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
    </script>
@endpush