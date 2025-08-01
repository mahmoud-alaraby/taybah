{{-- resources/views/admin/operation-system/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'نظام التشغيل العام')

@section('content')
<div class="bg-gray-50 min-h-screen">
    {{-- Header Section --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center py-6 space-y-4 sm:space-y-0">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">نظام التشغيل العام</h1>
                        <p class="text-sm text-gray-600">{{ $startDate->translatedFormat('F Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.operation-system.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    إضافة مهمة جديدة
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي المهام</p>
                        <p class="text-2xl font-bold">{{ $monthStats['total_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مهام التصميم</p>
                        <p class="text-2xl font-bold">{{ $monthStats['design_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مهام التسويق</p>
                        <p class="text-2xl font-bold">{{ $monthStats['marketing_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">المهام المحجوزة</p>
                        <p class="text-2xl font-bold">{{ $monthStats['reserved_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مهام مكتملة</p>
                        <p class="text-2xl font-bold">{{ $monthStats['completed_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مهام معلقة</p>
                        <p class="text-2xl font-bold">{{ $monthStats['pending_tasks'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    البحث والفلاتر
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('admin.operation-system.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">الشهر</label>
                        <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">السنة</label>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">نوع المهمة</label>
                        <select name="task_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الأنواع</option>
                            <option value="design" {{ request('task_type') == 'design' ? 'selected' : '' }}>تصميم</option>
                            <option value="marketing" {{ request('task_type') == 'marketing' ? 'selected' : '' }}>تسويق</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">الموظف</label>
                        <select name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الموظفين</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">حالة الحجز</label>
                        <select name="is_reserved" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_reserved') == '1' ? 'selected' : '' }}>محجوز</option>
                            <option value="0" {{ request('is_reserved') == '0' ? 'selected' : '' }}>غير محجوز</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2 space-x-reverse">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                            <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            بحث
                        </button>
                        <a href="{{ route('admin.operation-system.index') }}" class="px-3 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2L15 16"></path>
                            </svg>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- زر لفتح/إغلاق جميع الأيام --}}
        <div class="flex justify-center space-x-2 space-x-reverse mb-6">
            <button onclick="toggleAllDays(true)" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium transition-colors">
                فتح جميع الأيام
            </button>
            <button onclick="toggleAllDays(false)" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors">
                إغلاق جميع الأيام
            </button>
        </div>

   {{-- Calendar Grid - Day Cards محدث ليأخذ Full Width --}}
<div class="space-y-4">
    @foreach($days as $index => $day)
        @php
            // إنشاء ID فريد لكل كارد باستخدام التاريخ الكامل
            $uniqueId = $year . '_' . $month . '_' . $day['day'];
            $dayDate = \Carbon\Carbon::parse($day['date']);
            $isPastDayWithoutTasks = $dayDate->lt(\Carbon\Carbon::today()) && $day['total_tasks'] == 0;
            // (ملاحظة: الأيام الفارغة الماضية لا تظهر أصلاً من الكنترولر، هذا شرط فقط للوضوح)
        @endphp
        
        {{-- نتأكد هذا اليوم له مهام او انه اليوم الحالي او مستقبلي --}}
        @if(!$isPastDayWithoutTasks)
            <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ $day['is_today'] ? 'ring-2 ring-yellow-400 bg-yellow-50' : ($day['is_weekend'] ? 'bg-gray-50' : '') }}">
                {{-- رأس اليوم --}}
                <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition-colors" onclick="toggleDay('{{ $uniqueId }}')">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        {{-- رقم اليوم --}}
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $day['is_today'] ? 'bg-yellow-500 text-white' : 'bg-blue-100 text-blue-600' }} font-bold text-lg">
                                {{ $day['day'] }}
                            </div>
                        </div>
                        
                        {{-- معلومات اليوم --}}
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $day['day_name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $day['date'] }}</p>
                        </div>
                        
                        {{-- ملخص المهام --}}
                        <div class="flex items-center space-x-4 space-x-reverse">
                            @if($day['total_tasks'] > 0)
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        إجمالي: {{ $day['total_tasks'] }}
                                    </span>
                                    @if($day['design_tasks'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            تصميم: {{ $day['design_tasks'] }}
                                        </span>
                                    @endif
                                    @if($day['marketing_tasks'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            تسويق: {{ $day['marketing_tasks'] }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                {{-- اليوم حر إذا اليوم الحالي أو مستقبلي وبدون مهام --}}
                                @if($dayDate->gte(\Carbon\Carbon::today()))
                                    <span class="text-green-600 font-semibold">اليوم حر</span>
                                @else
                                    <span class="text-gray-400">لا توجد مهام</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- أزرار الإضافة والطباعة --}}
                    <div class="flex items-center space-x-2 space-x-reverse">
                        {{-- زر إضافة مهمة --}}
                        <a href="{{ route('admin.operation-system.create', ['date' => $day['date']]) }}" 
                           onclick="event.stopPropagation()"
                           class="p-2 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 transition-colors" 
                           title="إضافة مهمة">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </a>

                        {{-- سهم تبديل التفاصيل --}}
                        <div class="p-2">
                            <svg id="arrow-{{ $uniqueId }}" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- المحتوى القابل للطي --}}
                <div id="content-{{ $uniqueId }}" class="hidden">
                    @if($day['total_tasks'] > 0)
                        <div class="p-4 pt-0">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {{-- مهام التصميم --}}
                                @if($day['design_tasks'] > 0)
                                    <div>
                                        <h4 class="text-lg font-semibold text-purple-600 mb-4 flex items-center">
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                            </svg>
                                            مهام التصميم ({{ $day['design_tasks'] }})
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($day['tasks']->where('task_type', 'design') as $task)
                                                @include('admin.operation-system.partials.task-card', ['task' => $task, 'type' => 'design'])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- مهام التسويق --}}
                                @if($day['marketing_tasks'] > 0)
                                    <div>
                                        <h4 class="text-lg font-semibold text-orange-600 mb-4 flex items-center">
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                            </svg>
                                            مهام التسويق ({{ $day['marketing_tasks'] }})
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($day['tasks']->where('task_type', 'marketing') as $task)
                                                @include('admin.operation-system.partials.task-card', ['task' => $task, 'type' => 'marketing'])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- رسالة لا توجد مهام إذا لم يكن اليوم الحالي أو مستقبل --}}
                        <div class="p-4 pt-0">
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"></path>
                                </svg>
                                <p class="text-lg font-medium">@if($dayDate->gte(\Carbon\Carbon::today())) اليوم حر @else لا توجد مهام في هذا اليوم @endif</p>
                                <p class="text-sm mt-1">يمكنك إضافة مهمة جديدة بالضغط على الزر أدناه</p>
                                <a href="{{ route('admin.operation-system.create', ['date' => $day['date']]) }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    إضافة مهمة جديدة
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>

{{-- جافاسكريبت لتبديل ظهور تفاصيل اليوم --}}
<script>
    function toggleDay(id) {
        const content = document.getElementById('content-' + id);
        const arrow = document.getElementById('arrow-' + id);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }
</script>

    </div>
</div>

{{-- JavaScript المُحسن للـ Toggle --}}
<script>
function toggleDay(uniqueId) {
    const content = document.getElementById('content-' + uniqueId);
    const arrow = document.getElementById('arrow-' + uniqueId);
    
    if (!content || !arrow) {
        console.error('العناصر غير موجودة:', 'content-' + uniqueId, 'arrow-' + uniqueId);
        return;
    }
    
    // إغلاق جميع الكاردات الأخرى أولاً
    const allContents = document.querySelectorAll('[id^="content-"]');
    const allArrows = document.querySelectorAll('[id^="arrow-"]');
    
    allContents.forEach(otherContent => {
        if (otherContent.id !== 'content-' + uniqueId && !otherContent.classList.contains('hidden')) {
            otherContent.classList.add('hidden');
        }
    });
    
    allArrows.forEach(otherArrow => {
        if (otherArrow.id !== 'arrow-' + uniqueId) {
            otherArrow.classList.remove('rotate-180');
        }
    });
    
    // تبديل حالة الكارد الحالي
    if (content.classList.contains('hidden')) {
        // فتح
        content.classList.remove('hidden');
        arrow.classList.add('rotate-180');
    } else {
        // إغلاق
        content.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}

// إضافة وظيفة لفتح/إغلاق جميع الأيام
function toggleAllDays(open = true) {
    @foreach($days as $index => $day)
        @php
            $uniqueId = $year . '_' . $month . '_' . $day['day'];
        @endphp
        const content{{ $index }} = document.getElementById('content-{{ $uniqueId }}');
        const arrow{{ $index }} = document.getElementById('arrow-{{ $uniqueId }}');
        
        if (content{{ $index }} && arrow{{ $index }}) {
            if (open) {
                content{{ $index }}.classList.remove('hidden');
                arrow{{ $index }}.classList.add('rotate-180');
            } else {
                content{{ $index }}.classList.add('hidden');
                arrow{{ $index }}.classList.remove('rotate-180');
            }
        }
    @endforeach
}

// تحسين تجربة المستخدم - إضافة تأثيرات الانتقال
document.addEventListener('DOMContentLoaded', function() {
    // إضافة تأثير انتقال للمحتوى القابل للطي
    const allContents = document.querySelectorAll('[id^="content-"]');
    allContents.forEach(content => {
        content.style.transition = 'all 0.3s ease-in-out';
    });
    
    // فتح الأيام التي بها مهام تلقائياً عند تحميل الصفحة (اختياري)
    // يمكنك إزالة هذا الجزء إذا كنت لا تريد فتح أي كاردات تلقائياً
    /*
    @foreach($days as $index => $day)
        @if($day['total_tasks'] > 0)
            @php
                $uniqueId = $year . '_' . $month . '_' . $day['day'];
            @endphp
            toggleDay('{{ $uniqueId }}');
        @endif
    @endforeach
    */
});

// إضافة إمكانية الإغلاق بالضغط على ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        toggleAllDays(false);
    }
});
</script>

@endsection
