@extends('admin.layouts.app')

@section('title', 'إنشاء شات جديد للعميل: ' . $customer->customer_name)
@section('page-title', 'إنشاء شات جديد')
@section('page-subtitle', 'اختيار الموظف المسؤول عن العميل: ' . $customer->customer_name)

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إنشاء شات جديد مع الموظف</h1>
                <p class="text-gray-600">اختر الموظف المناسب للتواصل مع العميل بخصوص طلبه</p>
            </div>
            
            <a href="{{ route('admin.customer-communication.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Customer Information Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-user text-blue-500 ml-2"></i>
                معلومات العميل
            </h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Customer Avatar and Name -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($customer->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg text-gray-900">{{ $customer->customer_name }}</h3>
                        <p class="text-sm text-gray-500">عميل محتمل</p>
                    </div>
                </div>
                
                <!-- Phone Number -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-phone text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">رقم الهاتف</p>
                        <p class="text-sm text-gray-600">{{ $customer->phone }}</p>
                    </div>
                </div>
                
                <!-- Creation Date -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">تاريخ الإضافة</p>
                        <p class="text-sm text-gray-600">{{ $customer->created_at->format('Y/m/d') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Work Description -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">
                    <i class="fas fa-clipboard-list text-gray-600 ml-2"></i>
                    وصف العمل المطلوب
                </h4>
                <p class="text-gray-700 leading-relaxed">{{ $customer->work_description }}</p>
            </div>
            
            <!-- Customer Classifications -->
            @php
                $classifications = $customer->customer_classifications ?? [];
                if (is_string($classifications)) {
                    $classifications = json_decode($classifications, true) ?? [];
                }
            @endphp
            
            @if(!empty($classifications))
                <div class="mt-6">
                    <h4 class="font-medium text-gray-900 mb-3">
                        <i class="fas fa-tags text-gray-600 ml-2"></i>
                        تصنيفات العميل
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @if(in_array('requested_call', $classifications))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-phone ml-1"></i>
                                يطلب مكالمة هاتفية
                            </span>
                        @endif
                        
                        @if(in_array('requested_visit', $classifications))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-building ml-1"></i>
                                يطلب زيارة المكتب
                            </span>
                        @endif
                        
                        @if(in_array('difficult_customer', $classifications))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                عميل يتطلب اهتمام خاص
                            </span>
                        @endif
                        
                        @if(in_array('quote_sent', $classifications))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-file-invoice ml-1"></i>
                                تم إرسال عرض أسعار
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Employee Selection Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-users text-green-500 ml-2"></i>
                اختيار الموظف المسؤول
            </h2>
            <p class="text-sm text-green-700 mt-1">اختر أحد الموظفين المؤهلين للتواصل مع العملاء</p>
        </div>
        
        <div class="p-6">
            @if($employeesWithPermission->isEmpty())
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موظفين مؤهلين</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        لا يوجد موظفين نشطين لديهم صلاحية "التواصل مع العملاء" حالياً. 
                        يرجى التأكد من تعيين الصلاحيات المناسبة للموظفين.
                    </p>
                </div>
            @else
                <form action="{{ route('admin.customer-communication.assign-employee', $customer->id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        @foreach($employeesWithPermission as $employee)
                            <div class="relative">
                                <input type="radio" 
                                       name="employee_id" 
                                       value="{{ $employee->id }}" 
                                       id="employee_{{ $employee->id }}"
                                       class="sr-only employee-radio">
                                
                                <label for="employee_{{ $employee->id }}" 
                                       class="block p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-300 hover:border-blue-300 hover:shadow-md employee-card">
                                    
                                    <!-- Employee Info -->
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 truncate">{{ $employee->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $employee->position ?? 'موظف' }}</p>
                                            <p class="text-xs text-gray-500">{{ $employee->department ?? 'قسم العملاء' }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Employee Details -->
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <i class="fas fa-phone ml-1"></i>
                                                {{ $employee->phone ?? 'غير متوفر' }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-circle text-green-400 ml-1"></i>
                                                نشط
                                            </span>
                                        </div>
                                        
                                        @if($employee->last_login_at)
                                            <div class="mt-1 text-xs text-gray-400">
                                                آخر دخول: {{ $employee->last_login_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Selection Indicator -->
                                    <div class="absolute top-2 left-2 w-5 h-5 border-2 border-gray-300 rounded-full selection-indicator">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full m-0.5 opacity-0 selection-dot"></div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex items-center justify-center pt-6 border-t">
                        <button type="submit" 
                                id="submitBtn"
                                disabled
                                class="bg-gray-400 text-white px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg disabled:cursor-not-allowed">
                            <i class="fas fa-comments ml-2"></i>
                            إنشاء الشات وتعيين الموظف
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('.employee-radio');
    const employeeCards = document.querySelectorAll('.employee-card');
    const submitBtn = document.getElementById('submitBtn');
    
    // Handle employee selection
    radioButtons.forEach((radio, index) => {
        radio.addEventListener('change', function() {
            // Reset all cards
            employeeCards.forEach(card => {
                card.classList.remove('border-blue-500', 'bg-blue-50', 'shadow-lg');
                card.classList.add('border-gray-200');
                
                const indicator = card.querySelector('.selection-indicator');
                const dot = card.querySelector('.selection-dot');
                indicator.classList.remove('border-blue-500', 'bg-blue-50');
                indicator.classList.add('border-gray-300');
                dot.classList.add('opacity-0');
            });
            
            // Highlight selected card
            if (this.checked) {
                const selectedCard = employeeCards[index];
                selectedCard.classList.remove('border-gray-200');
                selectedCard.classList.add('border-blue-500', 'bg-blue-50', 'shadow-lg');
                
                const indicator = selectedCard.querySelector('.selection-indicator');
                const dot = selectedCard.querySelector('.selection-dot');
                indicator.classList.remove('border-gray-300');
                indicator.classList.add('border-blue-500', 'bg-blue-50');
                dot.classList.remove('opacity-0');
                
                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400');
                submitBtn.classList.add('bg-gradient-to-r', 'from-green-500', 'to-green-600', 'hover:from-green-600', 'hover:to-green-700');
            }
        });
    });
    
    // Handle form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedEmployee = document.querySelector('.employee-radio:checked');
            
            if (!selectedEmployee) {
                e.preventDefault();
                alert('يرجى اختيار موظف أولاً');
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري إنشاء الشات...';
        });
    }
});
</script>

<style>
.employee-card {
    transition: all 0.3s ease;
}

.employee-card:hover {
    transform: translateY(-2px);
}

.selection-dot {
    transition: opacity 0.3s ease;
}

.selection-indicator {
    transition: all 0.3s ease;
}

.employee-card.border-blue-500 {
    animation: pulse 2s ease-in-out;
    animation-iteration-count: 1;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>
@endpush
@endsection