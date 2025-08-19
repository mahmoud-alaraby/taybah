{{-- resources/views/employee/renewal-dates/show.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'عرض حدث التجديد')

@section('content')
<div class="container-fluid p-6 max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-6 bg-white p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
           <div class="flex items-center">
                            <div class="w-16 h-16 sm:w-16 sm:h-16 ml-2 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    
                         <i class="fas fa-eye text-gray-200  text-2xl"></i>
                      
                    </div>
                        <div>
                            <!-- <h1 class="text-xl sm:text-2xl font-bold text-gray-600">    {{ $renewalDate->department }} - {{ $renewalDate->position }}</h1> -->
                            <p class="text-gray-600 text-md font-bold">تفاصيل الحدث كاملة ومعلومات التجديد</p>
                        </div>
                    </div>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('employee.renewal-dates.edit', $renewalDate) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('employee.renewal-dates.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if($renewalDate->status === 'active')
        @php $days = $renewalDate->getDaysUntilRenewal(); @endphp
        <div class="mb-6">
            @if($days < 0)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                    <div>
                        <strong>تنبيه هام!</strong>
                        <p class="mt-1">هذا الحدث متأخر {{ abs($days) }} يوم ويحتاج معالجة عاجلة</p>
                    </div>
                </div>
            @elseif($days == 0)
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-calendar-day text-xl mr-3"></i>
                    <div>
                        <strong>موعد اليوم!</strong>
                        <p class="mt-1">هذا الحدث مجدول ليتم اليوم</p>
                    </div>
                </div>
            @elseif($days <= 3)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-clock text-xl mr-3"></i>
                    <div>
                        <strong>قادم قريباً!</strong>
                        <p class="mt-1">باقي {{ $days }} يوم على موعد التجديد</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Event Details -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-red-600 ml-2"></i>
                        المعلومات الأساسية
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">تفاصيل الحدث الأساسية</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- العنوان -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الحدث</label>
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-lg border border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900">{{ $renewalDate->title }}</h2>
                            </div>
                        </div>

                        <!-- تاريخ التجديد -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ التجديد</label>
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="text-lg font-bold text-blue-900">{{ $renewalDate->renewal_date->format('Y-m-d') }}</div>
                                <div class="text-sm text-blue-700">{{ $renewalDate->renewal_date->format('l، j F Y') }}</div>
                            </div>
                        </div>

                        <!-- تكرار التجديد -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تكرار التجديد</label>
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-refresh ml-2"></i>
                                    {{ $renewalDate->getFrequencyDisplayName() }}
                                </span>
                            </div>
                        </div>

                        @if($renewalDate->amount)
                        <!-- المبلغ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <div class="text-lg font-bold text-green-700">{{ number_format($renewalDate->amount, 2) }} ريال سعودي</div>
                            </div>
                        </div>
                        @endif

                        @if($renewalDate->next_renewal_date)
                        <!-- التاريخ التالي للتجديد -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ التالي للتجديد</label>
                            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                                <div class="text-lg font-bold text-indigo-700">{{ $renewalDate->next_renewal_date->format('Y-m-d') }}</div>
                                <div class="text-sm text-indigo-600">{{ $renewalDate->next_renewal_date->format('l، j F Y') }}</div>
                            </div>
                        </div>
                        @endif

                        <!-- الحالة -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحدث</label>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                @if($renewalDate->status == 'active')
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check ml-2"></i>
                                        نشط
                                    </span>
                                @elseif($renewalDate->status == 'completed')
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check-circle ml-2"></i>
                                        مكتمل
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times ml-2"></i>
                                        ملغي
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($renewalDate->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-file-alt text-red-600 ml-2"></i>
                        الوصف والملاحظات
                    </h3>
                </div>

                <div class="p-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-gray-800 leading-relaxed">{{ $renewalDate->description }}</p>
                    </div>
                </div>
            </div>
            @endif

        
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            @if($renewalDate->status === 'active')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt text-red-600 ml-2"></i>
                        إجراءات سريعة
                    </h3>
                </div>

                <div class="p-6 space-y-3">
                    <button onclick="markCompleted({{ $renewalDate->id }})" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-check ml-2"></i>
                        تحديد كمكتمل
                    </button>
                    <button onclick="renewEvent({{ $renewalDate->id }})" 
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-refresh ml-2"></i>
                        إنشاء تجديد جديد
                    </button>
                    <a href="{{ route('employee.renewal-dates.edit', $renewalDate) }}" 
                       class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الحدث
                    </a>
                </div>
            </div>
            @endif

            <!-- Calendar Widget -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-red-600 ml-2"></i>
                        التقويم
                    </h3>
                </div>

                <div class="p-6">
                    <div class="text-center bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border border-red-200">
                        <div class="text-4xl font-bold text-red-600 mb-2">{{ $renewalDate->renewal_date->format('d') }}</div>
                        <div class="text-lg font-semibold text-red-700">{{ $renewalDate->renewal_date->format('F') }}</div>
                        <div class="text-sm text-red-600">{{ $renewalDate->renewal_date->format('Y') }}</div>
                        <div class="text-xs text-red-500 mt-2 border-t border-red-200 pt-2">{{ $renewalDate->renewal_date->format('l') }}</div>
                    </div>
                </div>
            </div>

            <!-- Event Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-red-600 ml-2"></i>
                        معلومات إضافية
                    </h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-calendar-plus text-gray-400 ml-2"></i>
                                تاريخ الإنشاء
                            </span>
                            <span class="font-medium text-gray-900">{{ $renewalDate->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-edit text-gray-400 ml-2"></i>
                                آخر تحديث
                            </span>
                            <span class="font-medium text-gray-900">{{ $renewalDate->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-user text-gray-400 ml-2"></i>
                                المنشئ
                            </span>
                            <span class="font-medium text-gray-900">{{ $renewalDate->getCreatorName() }}</span>
                        </div>
                        @if($renewalDate->getUpdaterName() !== 'غير محدد')
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-user-edit text-gray-400 ml-2"></i>
                                آخر محدث
                            </span>
                            <span class="font-medium text-gray-900">{{ $renewalDate->getUpdaterName() }}</span>
                        </div>
                        @endif
                   
                    </div>
                </div>
            </div>

    
        </div>
    </div>
</div>

<script>
function markCompleted(id) {
    if (confirm('هل تريد تحديد هذا الحدث كمكتمل؟')) {
        const url = `{{ route('employee.renewal-dates.complete', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // استخدام SweetAlert إذا كان متاح
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تم بنجاح!',
                        text: 'تم تحديد الحدث كمكتمل بنجاح',
                        icon: 'success',
                        confirmButtonColor: '#dc143c',
                        confirmButtonText: 'موافق'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert('تم تحديد الحدث كمكتمل بنجاح');
                    location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التحديث',
                        icon: 'error',
                        confirmButtonColor: '#dc143c'
                    });
                } else {
                    alert('حدث خطأ أثناء التحديث');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء التحديث',
                    icon: 'error',
                    confirmButtonColor: '#dc143c'
                });
            } else {
                alert('حدث خطأ أثناء التحديث');
            }
        });
    }
}

function renewEvent(id) {
    if (confirm('هل تريد إنشاء حدث تجديد جديد لهذا الحدث؟')) {
        const url = `{{ route('employee.renewal-dates.renew', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تم بنجاح!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#dc143c',
                        confirmButtonText: 'موافق'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert(data.message);
                    location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التجديد',
                        icon: 'error',
                        confirmButtonColor: '#dc143c'
                    });
                } else {
                    alert('حدث خطأ أثناء التجديد');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ أثناء التجديد',
                    icon: 'error',
                    confirmButtonColor: '#dc143c'
                });
            } else {
                alert('حدث خطأ أثناء التجديد');
            }
        });
    }
}
</script>
@endsection
