@extends('employee.layouts.app')

@section('title', 'العملاء المحتملين')
@section('page-title', 'العملاء المحتملين')
@section('page-subtitle', 'إدارة ومتابعة العملاء المحتملين وتصنيفاتهم')

@section('content')
<div class="space-y-6">
    <!-- Classification Tabs with Stats -->
    <div class="bg-white shadow rounded-lg">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-3">
            <h3 class="text-lg font-bold flex items-center">
                <i class="fas fa-users ml-2"></i>
                تصنيفات العملاء المحتملين
            </h3>
        </div>

        <div class="p-4">
            <!-- All Customers Tab -->
            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('employee.potential-customers', array_merge(request()->query(), ['classification' => ''])) }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ !request('classification') ? 'bg-gradient-to-r from-red-400 to-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-users ml-1"></i>
                    جميع العملاء
                    <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs mr-1">
                        {{ array_sum(array_column($classificationStats, 'count')) }}
                    </span>
                </a>
            </div>

            <!-- Classification Tabs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($classificationStats as $stat)
                    <a href="{{ route('employee.potential-customers', array_merge(request()->query(), ['classification' => $stat['name']])) }}" 
                       class="p-3 rounded-lg border-2 transition-all hover:shadow-md {{ request('classification') === $stat['name'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $stat['display_name'] }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $stat['count'] }} عميل</div>
                            </div>
                            <div class="mr-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $stat['color_class'] }}">
                                    {{ $stat['count'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add New Customer Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold flex items-center">
                <i class="fas fa-plus ml-2"></i>
                إضافة عميل محتمل جديد
            </h3>
            <button onclick="toggleForm('customerForm')" class="bg-white text-red-600 px-3 py-1 rounded text-sm font-bold hover:bg-gray-50">
                <i class="fas fa-plus ml-1"></i>
                إضافة
            </button>
        </div>

        <div id="customerForm" class="hidden bg-stone-50 border-b border-stone-200 p-6">
            <form action="{{ route('employee.potential-customers.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- معلومات العميل -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700">معلومات العميل</h5>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل المحتمل</label>
                            <input type="text" name="customer_name" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                   placeholder="اسم العميل">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                            <input type="text" name="phone" required
                                   class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                   placeholder="+966501234567">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">وصف العمل المطلوب</label>
                            <textarea name="work_description" rows="3" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"
                                   placeholder="وصف تفصيلي للعمل المطلوب"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية</label>
                            <textarea name="notes" rows="2"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 text-sm resize-none"
                                   placeholder="أي ملاحظات إضافية"></textarea>
                        </div>
                    </div>

                    <!-- التصنيفات -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700">تصنيف العميل</h5>
                        
                        <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                            @foreach($classifications as $classification)
                                <label class="flex items-center">
                                    <input type="checkbox" name="customer_classifications[]" value="{{ $classification['name'] }}" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="mr-2 text-sm">{{ $classification['display_name'] }}</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $classification['color_class'] }} mr-auto">
                                        {{ array_key_exists($classification['name'], $classificationStats) ? $classificationStats[$classification['name']]['count'] : 0 }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500">يمكن اختيار أكثر من تصنيف</p>
                    </div>
                </div>
                
                <div class="flex space-x-2 space-x-reverse mt-4">
                    <button type="submit" class="flex-1 h-9 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-medium text-sm">
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
        <form method="GET" class="grid grid-cols-4 gap-3 items-end">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="اسم العميل، الجوال، أو وصف العمل"
                       class="w-full h-9 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <button type="submit" class="w-full h-9 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors font-medium text-sm">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
            
            <div>
                <a href="{{ route('employee.potential-customers.print', request()->query()) }}" 
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
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-3 flex items-center justify-between">
            <h3 class="font-bold flex items-center">
                <i class="fas fa-users ml-2"></i>
                العملاء المحتملين ({{ $customers->total() }})
            </h3>
            @if(request('classification'))
                <span class="text-sm opacity-90">
                    فلترة: {{ collect($classificationStats)->where('name', request('classification'))->first()['display_name'] ?? '' }}
                </span>
            @endif
        </div>

        @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">العميل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">وصف العمل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">التصنيفات</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">التواصل</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإضافة</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->customer_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($customer->work_description, 40) }}</div>
                                    @if($customer->notes)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($customer->notes, 30) }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex flex-wrap gap-1" id="classifications-{{ $customer->id }}">
                                        @foreach($customer->classifications_badges as $badge)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $badge['color_class'] }}">
                                                {{ $badge['display_name'] }}
                                                <button onclick="removeClassification({{ $customer->id }}, '{{ $badge['name'] }}')" 
                                                        class="mr-1 text-xs hover:text-red-600">
                                                    ×
                                                </button>
                                            </span>
                                        @endforeach
                                        @if(empty($customer->classifications_badges))
                                            <span class="text-xs text-gray-400">لا يوجد تصنيف</span>
                                        @endif
                                    </div>
                                    <button onclick="quickClassification({{ $customer->id }})" 
                                            class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                        + إضافة تصنيف
                                    </button>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if($customer->whatsapp_link)
                                        <a href="{{ $customer->whatsapp_link }}" target="_blank" 
                                           class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full hover:bg-green-200 transition-colors">
                                            <i class="fab fa-whatsapp ml-1"></i>
                                            واتساب
                                        </a>
                                    @endif
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $customer->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2 space-x-reverse">
                                        <button onclick="editCustomer({{ $customer->id }})" 
                                                class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteCustomer({{ $customer->id }})" 
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
            @if($customers->hasPages())
                <div class="px-4 py-3 border-t">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عملاء محتملين</h3>
                <p class="text-gray-600 mb-4">
                    @if(request('classification'))
                        لم يتم العثور على عملاء محتملين بهذا التصنيف
                    @else
                        لم يتم العثور على أي عملاء محتملين للبحث المحدد
                    @endif
                </p>
                <button onclick="toggleForm('customerForm')" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول عميل محتمل
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Quick Classification Modal -->
<div id="quickClassificationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">إضافة تصنيف سريع</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($classifications as $classification)
                    <button onclick="addClassificationQuick('{{ $classification['name'] }}')" 
                            class="w-full text-right px-3 py-2 rounded-md border border-gray-200 hover:bg-gray-50 transition-colors">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $classification['color_class'] }} float-left">
                            تصنيف
                        </span>
                        <span class="text-sm">{{ $classification['display_name'] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="flex items-center justify-end space-x-2 space-x-reverse mt-6">
                <button onclick="closeQuickClassificationModal()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentCustomerId = null;

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

function deleteCustomer(id) {
    Swal.fire({
        title: 'حذف العميل المحتمل',
        text: 'هل أنت متأكد من حذف هذا العميل؟',
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
            form.action = `/employee/potential-customers/${id}`;
            
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

function editCustomer(id) {
    // الذهاب لصفحة التعديل
    window.location.href = `/employee/potential-customers/edit/${id}`;
}

function quickClassification(customerId) {
    currentCustomerId = customerId;
    document.getElementById('quickClassificationModal').classList.remove('hidden');
}

function closeQuickClassificationModal() {
    document.getElementById('quickClassificationModal').classList.add('hidden');
    currentCustomerId = null;
}

function addClassificationQuick(classification) {
    if (!currentCustomerId) return;
    
    fetch(`/employee/potential-customers/${currentCustomerId}/quick-classification`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            classification: classification,
            action: 'add'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث التصنيفات في الجدول
            updateClassificationsDisplay(currentCustomerId, data.classifications);
            closeQuickClassificationModal();
            
            // عرض رسالة نجاح
            Swal.fire({
                title: 'تم بنجاح!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'خطأ!',
            text: 'حدث خطأ أثناء إضافة التصنيف',
            icon: 'error'
        });
    });
}

function removeClassification(customerId, classification) {
    fetch(`/employee/potential-customers/${customerId}/quick-classification`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            classification: classification,
            action: 'remove'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث التصنيفات في الجدول
            updateClassificationsDisplay(customerId, data.classifications);
            
            // عرض رسالة نجاح
            Swal.fire({
                title: 'تم بنجاح!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'خطأ!',
            text: 'حدث خطأ أثناء إزالة التصنيف',
            icon: 'error'
        });
    });
}

function updateClassificationsDisplay(customerId, classifications) {
    const container = document.getElementById(`classifications-${customerId}`);
    
    let html = '';
    if (classifications && classifications.length > 0) {
        classifications.forEach(badge => {
            html += `
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${badge.color_class}">
                    ${badge.display_name}
                    <button onclick="removeClassification(${customerId}, '${badge.name}')" 
                            class="mr-1 text-xs hover:text-red-600">
                        ×
                    </button>
                </span>
            `;
        });
    } else {
        html = '<span class="text-xs text-gray-400">لا يوجد تصنيف</span>';
    }
    
    container.innerHTML = html;
}

// إغلاق المودال عند الضغط خارجه
document.getElementById('quickClassificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickClassificationModal();
    }
});

// Auto-hide forms after successful submission
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('customerForm');
        if (form) form.classList.add('hidden');
    });
@endif
</script>
@endpush