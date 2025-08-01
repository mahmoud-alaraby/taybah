@extends('employee.layouts.app')

@section('title', 'متابعة حركة العملاء')
@section('page-title', 'متابعة حركة العملاء')
@section('page-subtitle', 'إدارة ومتابعة حركة العملاء والاتفاقيات والتارجت الشهري')

@section('content')
<div class="space-y-6">

    <!-- Header with Target & Summary Stats -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <!-- Target Setting Section -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white rounded-t-xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-bullseye text-2xl"></i>
                    تحديد تارجت الاتفاق - {{ $months[$currentMonth] }} {{ $currentYear }}
                </h2>
                <div class="flex items-center gap-2">
                    <i class="fas fa-battery-three-quarters text-2xl"></i>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ $achievementPercentage }}%</div>
                        <div class="text-sm opacity-80">نسبة التحقق</div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('employee.customer-movement.target') }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-90">السنة <i class="fas fa-calendar-alt ml-1"></i></label>
                    <select name="year" class="w-full h-10 rounded-md border-0 text-gray-900 focus:ring-2 focus:ring-red-300 text-sm">
                        @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-90">الشهر <i class="fas fa-calendar ml-1"></i></label>
                    <select name="month" class="w-full h-10 rounded-md border-0 text-gray-900 focus:ring-2 focus:ring-red-300 text-sm">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 opacity-90">مبلغ التارجت (ريال) <i class="fas fa-coins ml-1"></i></label>
                    <input type="number" name="target_amount" value="{{ $targetAmount }}" step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full h-10 rounded-md border-0 text-gray-900 focus:ring-2 focus:ring-red-300 text-center font-bold text-sm">
                </div>
                <div>
                    <button type="submit" class="w-full h-10 bg-white text-red-700 font-bold rounded-md hover:bg-gray-50 transition-colors shadow text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        حفظ
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="p-5 bg-gray-50 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center flex flex-col items-center">
                    <i class="fas fa-hand-holding-usd text-green-600 text-2xl mb-2"></i>
                    <div class="text-xl font-bold text-green-800">{{ number_format($totalAgreed, 2) }}</div>
                    <div class="text-xs text-green-800 mt-1">المبالغ المتفق عليها</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center flex flex-col items-center">
                    <i class="fas fa-money-bill-wave text-red-700 text-2xl mb-2"></i>
                    <div class="text-xl font-bold text-red-800">{{ number_format($totalPaid, 2) }}</div>
                    <div class="text-xs text-red-800 mt-1">المبالغ المدفوعة</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 text-center flex flex-col items-center">
                    <i class="fas fa-exclamation-circle text-orange-500 text-2xl mb-2"></i>
                    <div class="text-xl font-bold text-orange-700">{{ number_format($totalDebts, 2) }}</div>
                    <div class="text-xs text-orange-700 mt-1">الديون</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Customer Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-4 flex items-center justify-between rounded-t-xl">
            <h3 class="font-bold flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                إضافة عميل جديد
            </h3>
            <button onclick="toggleForm('customerForm')" type="button"
                    class="bg-white text-red-800 px-3 py-1 rounded font-bold text-sm hover:bg-gray-100 flex items-center gap-1">
                <i class="fas fa-plus"></i> إضافة
            </button>
        </div>

        <div id="customerForm" class="hidden bg-stone-50 border-b border-stone-200 p-6">
            <form action="{{ route('employee.customer-movement.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- معلومات العميل -->
                    <div class="space-y-4">
                        <h5 class="text-base font-bold text-gray-800 flex items-center gap-2 mb-2">
                            <i class="fas fa-user-tie text-red-400"></i>
                            معلومات العميل
                        </h5>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل</label>
                            <input type="text" name="customer_name" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm"
                                   placeholder="اسم العميل">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                            <input type="text" name="customer_phone" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm"
                                   placeholder="0501234567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل</label>
                            <textarea name="work_description" rows="3" required
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm resize-none"
                                      placeholder="وصف تفصيلي للعمل"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حالة العمل</label>
                            <select name="work_status" required class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                                @foreach($workStatuses as $key => $status)
                                    <option value="{{ $key }}" {{ $key == 'جاري العمل' ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- معلومات الاتفاق -->
                    <div class="space-y-4">
                        <h5 class="text-base font-bold text-gray-800 flex items-center gap-2 mb-2">
                            <i class="fas fa-handshake text-green-500"></i>
                            معلومات الاتفاق
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">بداية الاتفاق</label>
                                <input type="date" name="agreement_start_date" value="{{ date('Y-m-d') }}" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التسليم الأولي</label>
                                <input type="date" name="initial_delivery_date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التسليم النهائي</label>
                                <input type="date" name="final_delivery_date" required
                                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المتفق عليه (ريال)</label>
                            <input type="number" name="agreed_amount" step="0.01" min="0" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm"
                                   placeholder="0.00">
                        </div>
                        <div class="grid grid-cols-4 gap-3">
                            @foreach(['first_payment'=>'دفعة 1','second_payment'=>'دفعة 2','third_payment'=>'دفعة 3','fourth_payment'=>'دفعة 4'] as $field => $label)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                                    <input type="number" name="{{ $field }}" step="0.01" min="0" value="0"
                                           class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm"
                                           placeholder="0.00">
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوعية العميل</label>
                            <select name="customer_type" required
                                    class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                                <option value="">اختر نوعية العميل</option>
                                @foreach($customerTypes as $key => $type)
                                    <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2 space-x-reverse mt-6">
                    <button type="submit"
                        class="flex-1 h-10 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-md hover:from-red-800 hover:to-red-900 transition-colors font-bold text-base flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        حفظ العميل
                    </button>
                    <button type="button" onclick="toggleForm('customerForm')"
                        class="h-10 px-6 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition-colors text-base flex items-center gap-1">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث <i class="fas fa-search"></i></label>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="اسم العميل، الجوال، أو وصف العمل"
                       class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع العميل <i class="fas fa-users"></i></label>
                <select name="customer_type" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                    <option value="">جميع الأنواع</option>
                    @foreach($customerTypes as $key => $type)
                        <option value="{{ $key }}" {{ $key == $customerType ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">حالة العمل <i class="fas fa-tasks"></i></label>
                <select name="work_status" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 text-sm">
                    <option value="">جميع الحالات</option>
                    @foreach($workStatuses as $key => $status)
                        <option value="{{ $key }}" {{ $key == $workStatus ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit"
                    class="w-full h-10 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors font-bold text-base flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
            <div>
                <a href="{{ route('employee.customer-movement.print', request()->query()) }}"
                   target="_blank"
                   class="w-full h-10 bg-gradient-to-r from-green-500 to-green-700 text-white rounded-md hover:from-green-600 hover:to-green-800 transition-colors font-bold text-base flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i>
                    طباعة
                </a>
            </div>
        </form>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-4 flex items-center justify-between rounded-t-xl">
            <h3 class="font-bold flex items-center gap-2">
                <i class="fas fa-users"></i>
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
                                    <div class="text-sm font-medium text-gray-900 flex items-center gap-1">
                                        <i class="fas fa-user text-gray-400"></i>
                                        {{ $movement->customer_name }}</div>
                                    <div class="text-xs text-gray-500"><i class="fas fa-phone ml-1"></i> {{ $movement->customer_phone }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($movement->work_description, 40) }}</div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-500 flex flex-col gap-1">
                                        <span><i class="far fa-calendar-check ml-1"></i> بداية: {{ $movement->agreement_start_date->format('Y-m-d') }}</span>
                                        <span><i class="far fa-calendar-alt ml-1"></i> أولي: {{ $movement->initial_delivery_date->format('Y-m-d') }}</span>
                                        <span><i class="far fa-calendar ml-1"></i> نهائي: {{ $movement->final_delivery_date->format('Y-m-d') }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-green-700 flex items-center gap-1">
                                            <i class="fas fa-coins"></i> {{ number_format($movement->agreed_amount, 2) }}
                                        </div>
                                        <div class="text-xs text-red-600 flex items-center gap-1">
                                            <i class="fas fa-comment-dollar"></i> متبقي: {{ number_format($movement->remaining_amount, 2) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-xs flex flex-col space-y-1">
                                        <span><i class="fas fa-money-bill-wave ml-1"></i> 1: {{ number_format($movement->first_payment, 2) }}</span>
                                        <span><i class="fas fa-money-bill ml-1"></i> 2: {{ number_format($movement->second_payment, 2) }}</span>
                                        <span><i class="fas fa-money-bill ml-1"></i> 3: {{ number_format($movement->third_payment, 2) }}</span>
                                        <span><i class="fas fa-money-bill ml-1"></i> 4: {{ number_format($movement->fourth_payment, 2) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $movement->customer_type }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $movement->work_status == 'جاري العمل' ? 'bg-orange-100 text-orange-700' :
                                                   ($movement->work_status == 'تم الانتهاء' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $movement->work_status }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2 justify-start">
                                        <button onclick="editMovement({{ $movement->id }})"
                                                class="text-yellow-600 hover:text-yellow-800">
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
                <!-- Pagination -->
                <div class="bg-gray-50 px-4 py-2 border-t text-center">
                    {{ $movements->links() }}
                </div>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">لا توجد بيانات.</div>
        @endif
    </div>
</div>

<script>
function toggleForm(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
}
</script>
@endsection
