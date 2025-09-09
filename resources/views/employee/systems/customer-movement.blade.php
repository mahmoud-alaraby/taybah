@extends('employee.layouts.app')

@section('title', 'متابعة حركة العملاء')
@section('page-title', 'متابعة حركة العملاء')
@section('page-subtitle', 'إدارة ومتابعة حركة العملاء والاتفاقيات والتارجت الشهري')

@section('content')
<div class="space-y-6">

    <!-- Header with Target & Summary Stats -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

     <!-- Target Setting Section - محسّن -->
<div class="p-8 bg-white rounded-t-xl shadow-md border border-gray-200 text-black">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <!-- عنوان التارجت مع ايقونة حمراء أولاً -->
        <h2 class="text-lg font-bold flex items-center gap-3">
            <i class="fas fa-bullseye text-2xl text-red-600"></i>
            تحديد تارجت الاتفاق - {{ $months[$currentMonth] }} {{ $currentYear }}
        </h2>
        <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded border">
            <i class="fas fa-battery-three-quarters text-2xl text-green-600"></i>
            <div class="text-right">
                <div class="text-2xl font-bold text-green-800">{{ $achievementPercentage }}%</div>
                <div class="text-sm opacity-80">نسبة التحقق</div>
            </div>
        </div>
    </div>

    <form action="{{ route('employee.customer-movement.target') }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-6 items-end">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-2 flex items-center gap-2 opacity-90">
                <i class="fas fa-calendar-alt text-red-600"></i>
                السنة
            </label>
            <select name="year" class="w-full h-10 rounded-md border border-gray-300 bg-white px-2 text-gray-900 focus:ring-2 focus:ring-red-300 text-sm">
                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-2 flex items-center gap-2 opacity-90">
                <i class="fas fa-calendar text-red-600"></i>
                الشهر
            </label>
            <select name="month" class="w-full h-10 rounded-md border border-gray-300 bg-white px-2 text-gray-900 focus:ring-2 focus:ring-red-300 text-sm">
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-2 flex items-center gap-2 opacity-90">
                <i class="fas fa-coins text-red-600"></i>
                مبلغ التارجت (ريال)
            </label>
            <input type="number" name="target_amount" value="{{ $targetAmount }}" step="0.01" min="0"
                   placeholder="0.00"
                   class="w-full h-10 rounded-md border border-gray-300 bg-white px-2 text-gray-900 focus:ring-2 focus:ring-red-300 text-center font-bold text-sm">
        </div>
        <div>
            <button type="submit" class="w-full h-10 bg-white text-red-700 font-bold rounded-md hover:bg-gray-50 transition-colors shadow border border-red-400 text-sm flex items-center justify-center gap-2">
                <i class="fas fa-save text-red-600"></i>
                حفظ
            </button>
        </div>
    </form>

</div>
>
<!-- Summary Stats - إحصائيات محسّنة -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <!-- المبالغ المتفق عليها -->
        <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-5 text-white shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">المبالغ المتفق عليها</p>
                <p class="text-2xl font-bold">{{ number_format($totalAgreed, 2) }}</p>
            </div>
            <i class="fas fa-hand-holding-usd text-3xl opacity-90"></i>
        </div>
        <!-- المبالغ المدفوعة -->
        <div class="bg-gradient-to-r from-red-400 to-red-600 rounded-xl p-5 text-white shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">المبالغ المدفوعة</p>
                <p class="text-2xl font-bold">{{ number_format($totalPaid, 2) }}</p>
            </div>
            <i class="fas fa-money-bill-wave text-3xl opacity-90"></i>
        </div>
        <!-- الديون -->
        <div class="bg-gradient-to-r from-orange-400 to-orange-600 rounded-xl p-5 text-white shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-1">الديون</p>
                <p class="text-2xl font-bold">{{ number_format($totalDebts, 2) }}</p>
            </div>
            <i class="fas fa-exclamation-circle text-3xl opacity-90"></i>
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
                                <input type="date" name="final_delivery_date" 
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
<!-- Filters & Search - محسّن -->
<div class="bg-white rounded-xl shadow-sm border border-gray-300 p-6 my-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-5 items-end my-8">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                <i class="fas fa-search text-red-600"></i> البحث
            </label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="اسم العميل، الجوال، أو وصف العمل"
                   class="w-full h-10 rounded-md border border-gray-300 shadow-sm px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                <i class="fas fa-users text-red-600"></i> نوع العميل
            </label>
            <select name="customer_type" 
                    class="w-full h-10 rounded-md border border-gray-300 shadow-sm px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 transition">
                <option value="">جميع الأنواع</option>
                @foreach($customerTypes as $key => $type)
                    <option value="{{ $key }}" {{ $key == $customerType ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                <i class="fas fa-tasks text-red-600"></i> حالة العمل
            </label>
            <select name="work_status" 
                    class="w-full h-10 rounded-md border border-gray-300 shadow-sm px-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 transition">
                <option value="">جميع الحالات</option>
                @foreach($workStatuses as $key => $status)
                    <option value="{{ $key }}" {{ $key == $workStatus ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit"
                class="w-full h-10 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-bold text-base flex items-center justify-center gap-2 shadow">
                <i class="fas fa-search"></i> بحث
            </button>
        </div>
        <div>
            <a href="{{ route('employee.customer-movement.print', request()->query()) }}"
               target="_blank"
               class="w-full h-10 bg-gradient-to-r from-green-600 to-green-800 text-white rounded-md hover:from-green-700 hover:to-green-900 transition-colors font-bold text-base flex items-center justify-center gap-2 shadow">
                <i class="fas fa-print"></i> طباعة
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
                                    {{ $movement->customer_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-phone ml-1"></i> {{ $movement->customer_phone }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($movement->work_description, 40) }}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500 flex flex-col gap-1">
                                    <span><i class="far fa-calendar-check ml-1"></i> بداية: {{ $movement->agreement_start_date->format('Y-m-d') }}</span>
                                    <span><i class="far fa-calendar-alt ml-1"></i> أولي: {{ $movement->initial_delivery_date->format('Y-m-d') }}</span>
<span>
 
    
    @if($movement->final_delivery_date)
       <i class="far fa-calendar ml-1"></i> 
نهائي : {{ $movement->final_delivery_date->format('Y-m-d') }}
 
  
    @endif
</span>
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
                                <div class="flex gap-4 justify-start">
                                    <a href="{{ route('employee.customer-movement.edit', $movement->id) }}" class="text-yellow-600 hover:text-yellow-800" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('employee.customer-movement.destroy', $movement->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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

<!-- JavaScript handling edit and delete -->
<script>
    function editMovement(id) {
        // مثال بسيط: إعادة التوجيه لصفحة تعديل مع تمرير المعرف
        window.location.href = `/employee/customer-movement/${id}/edit`;
    }

    function deleteMovement(id) {
        if(confirm('هل أنت متأكد من حذف هذا السجل؟')) {
            // إنشاء فورم ديناميكي لإرسال طلب الحذف باستخدام method POST و method_field DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/employee/customer-movement/${id}`;

            // إضافة حقول _token و _method للحماية من الهجمات ولطلب الحذف
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const inputToken = document.createElement('input');
            inputToken.type = 'hidden';
            inputToken.name = '_token';
            inputToken.value = csrfToken;
            form.appendChild(inputToken);

            const inputMethod = document.createElement('input');
            inputMethod.type = 'hidden';
            inputMethod.name = '_method';
            inputMethod.value = 'DELETE';
            form.appendChild(inputMethod);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>


<script>
function toggleForm(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
}
</script>
@endsection
