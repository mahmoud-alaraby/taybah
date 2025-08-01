@extends('admin.layouts.app')

@section('title', 'متابعة حركة العملاء')
@section('page-title', 'متابعة حركة العملاء')
@section('page-subtitle', 'إدارة ومتابعة حركة العملاء والاتفاقيات والتارجت الشهري')

@section('content')
<div class="space-y-6">
    <!-- Header with Target & Summary Stats -->
    <div class="bg-white shadow rounded-lg">
        <!-- Target Setting Section -->
        <div class="bg-gradient-to-r from-red-500 to-red-700 p-4 text-white">
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
            
            <form action="{{ route('admin.customer-movement.target') }}" method="POST" class="grid grid-cols-5 gap-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1 text-white opacity-90">الموظف</label>
                    <select name="employee_id" class="w-full h-10 rounded-md border-0 text-gray-900 shadow-sm focus:ring-2 focus:ring-white text-sm" required>
                        <option value="">اختر الموظف</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $employee->id == $employeeId ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
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
<!-- Statistics Cards - متابعة حركة العملاء بالتنسيق العصري -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- المبالغ المتفق عليها -->
        <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">المبالغ المتفق عليها</p>
                    <p class="text-2xl font-bold">{{ number_format($totalAgreed, 2) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a2 2 0 00-2 2v5a2 2 0 002 2h2v2a2 2 0 002 2h0a2 2 0 002-2v-2h2a2 2 0 002-2v-5a2 2 0 00-2-2z"></path>
                </svg>
            </div>
        </div>
        <!-- المبالغ المدفوعة -->
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">المبالغ المدفوعة</p>
                    <p class="text-2xl font-bold">{{ number_format($totalPaid, 2) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path>
                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                </svg>
            </div>
        </div>
        <!-- الديون -->
        <div class="bg-gradient-to-r from-red-400 to-red-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">الديون</p>
                    <p class="text-2xl font-bold">{{ number_format($totalDebts, 2) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414 1.414A9 9 0 1 0 12 21v0"></path>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                </svg>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Filters & Actions -->
    <div class="bg-white shadow rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">البحث والفلترة</h3>
            <a href="{{ route('admin.customer-movement.create') }}" 
               class="bg-red-400 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors font-medium text-sm">
                <i class="fas fa-plus ml-1"></i>
                إضافة عميل جديد
            </a>
        </div>
        
        <form method="GET" class="grid grid-cols-8 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="اسم العميل، الجوال، أو وصف العمل"
                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف</label>
                <select name="employee_id" class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">جميع الموظفين</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $employee->id == $employeeId ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
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
                <button type="submit" class="w-full h-9 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors font-medium text-sm">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
            
            <div>
                <a href="{{ route('admin.customer-movement.print', request()->query()) }}" 
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
        <div class=" bg-red-700 text-white px-4 py-3 flex items-center justify-between">
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
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموظف</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وصف العمل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التواريخ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبالغ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدفعات</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع/الحالة</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $movement->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $movement->customer_phone }}</div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $movement->employee->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $movement->employee->employee_id }}</div>
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
                                        <a href="{{ route('admin.customer-movement.edit', $movement) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
                <a href="{{ route('admin.customer-movement.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول عميل
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteMovement(id) {
    Swal.fire({
        title: 'حذف حركة العميل',
        text: 'هل أنت متأكد من حذف هذه الحركة؟ لن تتمكن من التراجع عن هذا الإجراء!',
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
            form.action = `/admin/customer-movement/${id}`;
            
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
</script>
@endpush