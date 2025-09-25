@extends('admin.layouts.app')

@section('title', 'متابعة الحضور')
@section('page-title', 'متابعة الحضور والانصراف')
@section('page-subtitle', 'متابعة حضور الموظفين والإحصائيات الشهرية')

@section('content')
<div class="space-y-6">
    <!-- فلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الموظف</label>
                <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الموظفين</option>
                    @foreach($users as $employee)
                        <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السنة</label>
                <select name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الشهر</label>
                <select name="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">
                    <i class="fas fa-search ml-1"></i>
                    عرض
                </button>
                <a href="{{ route('admin.attendance.reports', request()->query()) }}" 
                   class="bg-green-600 text-white rounded-md py-2 px-4 hover:bg-green-700">
                    <i class="fas fa-chart-line"></i>
                </a>
            </div>
        </form>
    </div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- أيام العمل -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">أيام العمل</p>
                    <p class="text-2xl font-bold">{{ $monthlyStats['total_days'] }}</p>
                </div>
                <i class="fas fa-calendar-alt text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- نسبة الالتزام بالمواعيد -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">نسبة الالتزام بالمواعيد</p>
                    <p class="text-2xl font-bold">{{ $monthlyStats['on_time_percentage'] }}%</p>
                </div>
                <i class="fas fa-check text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- متوسط ساعات العمل -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">متوسط ساعات العمل</p>
                    <p class="text-2xl font-bold">{{ $monthlyStats['average_hours'] }}</p>
                </div>
                <i class="fas fa-clock text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- إجمالي الساعات الإضافية -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي الساعات الإضافية</p>
                    <p class="text-2xl font-bold">{{ $monthlyStats['total_overtime'] }}</p>
                </div>
                <i class="fas fa-business-time text-2xl opacity-80"></i>
            </div>
        </div>
    </div>
</div>

@php
    function formatTimeTo12HourWithAmPmSpace($time) {
        if (!$time) return '';
        $hour = (int)$time->format('H');
        $minute = $time->format('i');
        $hour12 = $hour % 12;
        if ($hour12 == 0) $hour12 = 12;
        $ampm = $hour < 12 ? 'AM' : 'PM';
        return sprintf('%02d:%s&nbsp;%s', $hour12, $minute, $ampm);
    }

    function formatHoursToHoursMinutes($hoursFloat) {
        $totalMinutes = round($hoursFloat * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    function formatLateTime($minutes) {
        $minutes = abs($minutes);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
@endphp

<!-- جدول الحضور - الجزء المحدث فقط -->
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت الحضور</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت الانصراف</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">انصراف مؤقت</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي الساعات</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ساعات إضافية</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($attendances as $attendance)
            <tr class="hover:bg-gray-50">
                <!-- معلومات الموظف -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-700">
                                    @if($attendance->employee_type === 'admin')
                                        {{ mb_substr(optional($attendance->admin)->name ?? 'غير محدد', 0, 1) }}
                                    @else
                                        {{ mb_substr(optional($attendance->employee)->name ?? 'غير محدد', 0, 1) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mr-3">
                            <div class="text-sm font-medium text-gray-900">
                                @if($attendance->employee_type === 'admin')
                                    {{ optional($attendance->admin)->name ?? 'غير معروف' }}
                                    <span class="text-xs text-blue-600">(إدارة)</span>
                                @else
                                    {{ optional($attendance->employee)->name ?? 'غير معروف' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($attendance->employee_type === 'admin')
                                    ADM-{{ $attendance->employee_id }}
                                @else
                                    {{ optional($attendance->employee)->employee_id ?? 'بدون معرف' }}
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                <!-- التاريخ -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $attendance->date->format('Y-m-d') }}
                    <div class="text-xs text-gray-500">{{ $attendance->date->translatedFormat('l') }}</div>
                </td>

                <!-- وقت الحضور -->
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <span class="text-gray-900" style="direction: ltr; display: inline-block;">
                            {!! formatTimeTo12HourWithAmPmSpace($attendance->check_in_time) !!}
                        </span>
                        @if($attendance->is_late)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                متأخر {{ formatLateTime($attendance->late_minutes) }}
                            </span>
                        @endif
                    </div>
                </td>

                <!-- وقت الانصراف -->
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($attendance->check_out_time)
                        <div class="text-gray-900" style="direction: ltr; display: inline-block;">
                            {!! formatTimeTo12HourWithAmPmSpace($attendance->check_out_time) !!}
                        </div>
                        @if($attendance->checkout_type)
                            <div class="text-xs {{ $attendance->checkout_type === 'final' ? 'text-red-600' : 'text-orange-600' }}">
                                {{ $attendance->checkout_type === 'final' ? 'نهائي' : 'مؤقت' }}
                            </div>
                        @endif
                    @else
                        @if($attendance->is_temp_out)
                            <span class="text-orange-600 font-medium">في انصراف مؤقت</span>
                        @else
                            <span class="text-green-600">لم ينصرف</span>
                        @endif
                    @endif
                </td>

                <!-- معلومات الانصراف المؤقت -->
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($attendance->temp_checkout_time)
                        <div class="space-y-1">
                            @if($attendance->is_temp_out)
                                <!-- في انصراف مؤقت حالياً -->
                                <div class="text-orange-600 font-medium">
                                    <i class="fas fa-clock text-xs"></i>
                                    منذ {{ $attendance->temp_checkout_time->format('H:i') }}
                                </div>
                                <div class="text-xs text-orange-500">
                                    المدة: {{ $attendance->temp_out_duration }}
                                </div>
                            @else
                                <!-- عاد من الانصراف المؤقت -->
                                <div class="text-gray-600 text-xs">
                                    {{ $attendance->temp_checkout_time->format('H:i') }} - 
                                    {{ $attendance->temp_checkin_time ? $attendance->temp_checkin_time->format('H:i') : 'غير محدد' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    مدة: {{ $attendance->temp_out_duration }}
                                </div>
                                @if($attendance->temp_checkout_count > 1)
                                    <div class="text-xs text-blue-600">
                                        ({{ $attendance->temp_checkout_count }} مرات)
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                <!-- إجمالي الساعات -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    @if($attendance->total_hours)
                        {{ formatHoursToHoursMinutes($attendance->total_hours) }} ساعة
                        @if($attendance->temp_checkout_time && !$attendance->check_out_time)
                            <div class="text-xs text-orange-500">
                                (يتم الحساب عند الانصراف النهائي)
                            </div>
                        @endif
                    @else
                        @if($attendance->check_out_time)
                            غير محسوب
                        @else
                            جاري العمل
                        @endif
                    @endif
                </td>

                <!-- الساعات الإضافية -->
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($attendance->overtime_hours > 0)
                        <span class="text-purple-600 font-medium">
                            {{ formatHoursToHoursMinutes($attendance->overtime_hours) }} ساعة
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                <!-- الحالة -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="space-y-1">
                        @if($attendance->is_temp_out)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-pause ml-1"></i>
                                انصراف مؤقت
                            </span>
                        @elseif($attendance->check_out_time)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $attendance->checkout_type === 'final' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-sign-out-alt ml-1"></i>
                                {{ $attendance->checkout_type === 'final' ? 'انصراف نهائي' : 'منصرف' }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-user-check ml-1"></i>
                                حاضر
                            </span>
                        @endif

                        @if($attendance->is_late)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                متأخر
                            </span>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection