@extends('employee.layouts.app')

@section('title', 'الحضور والانصراف')
@section('page-title', 'الحضور والانصراف')
@section('page-subtitle', 'متابعة سجل الحضور والانصراف الشهري')

@section('content')
<div class="space-y-6">
    <!-- فلاتر الشهر والسنة -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-4 gap-4 items-end">
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
                    @foreach($months as $key => $monthName)
                        <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700">
                    <i class="fas fa-search ml-1"></i>
                    عرض
                </button>
            </div>
            <div>
                <a href="{{ route('employee.attendance.print', request()->query()) }}" target="_blank"
                   class="w-full bg-green-600 text-white rounded-md py-2 hover:bg-green-700 flex items-center justify-center">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </a>
            </div>
        </form>
    </div>

    <!-- إحصائيات الشهر -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">أيام العمل المطلوبة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $monthlyStats['working_days'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">أيام الحضور</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $monthlyStats['attended_days'] }}</p>
                    <p class="text-sm text-green-600">{{ $monthlyStats['attendance_percentage'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">الحضور في الموعد</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $monthlyStats['on_time_days'] }}</p>
                    <p class="text-sm text-yellow-600">{{ $monthlyStats['punctuality_percentage'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-business-time text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">ساعات إضافية</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($monthlyStats['total_overtime_hours'], 1) }}</p>
                    <p class="text-sm text-purple-600">ساعة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول سجل الحضور -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                سجل الحضور - {{ $months[$month] }} {{ $year }}
            </h3>
            
            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">يوم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت الحضور</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت الانصراف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي الساعات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ساعات إضافية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attendance->date->translatedFormat('l') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center">
                                            <span class="text-gray-900">{{ $attendance->check_in_time->format('H:i') }}</span>
                                            @if($attendance->is_late)
                                                <span class="mr-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    متأخر {{ $attendance->late_minutes }}د
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : 'لم ينصرف' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($attendance->total_hours, 1) }} ساعة
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($attendance->overtime_hours > 0)
                                            <span class="text-purple-600 font-medium">{{ number_format($attendance->overtime_hours, 1) }} ساعة</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->is_late)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                                متأخر
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check ml-1"></i>
                                                في الموعد
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900">
                                    الإجماليات
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ number_format($monthlyStats['total_work_hours'], 1) }} ساعة
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-purple-600">
                                    {{ number_format($monthlyStats['total_overtime_hours'], 1) }} ساعة
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    متوسط: {{ number_format($monthlyStats['average_daily_hours'], 1) }}ساعة/يوم
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد سجل حضور</h3>
                    <p class="text-sm text-gray-500">لم يتم تسجيل أي حضور في هذا الشهر</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ملخص الأداء -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">ملخص الأداء الشهري</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- نسبة الالتزام بالحضور -->
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $monthlyStats['attendance_percentage'] }}%</div>
                <div class="text-sm text-gray-600">نسبة الحضور</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $monthlyStats['attendance_percentage'] }}%"></div>
                </div>
            </div>

            <!-- نسبة الالتزام بالمواعيد -->
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $monthlyStats['punctuality_percentage'] }}%</div>
                <div class="text-sm text-gray-600">الالتزام بالمواعيد</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $monthlyStats['punctuality_percentage'] }}%"></div>
                </div>
            </div>

            <!-- متوسط ساعات العمل -->
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ number_format($monthlyStats['average_daily_hours'], 1) }}</div>
                <div class="text-sm text-gray-600">متوسط ساعات العمل اليومية</div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($monthlyStats['average_daily_hours'] / 8) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection