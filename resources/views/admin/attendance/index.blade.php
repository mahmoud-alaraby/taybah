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
                    @foreach($employees as $employee)
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


    <!-- جدول الحضور -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">
                                                    {{ mb_substr($attendance->employee->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $attendance->employee->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $attendance->employee->employee_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $attendance->date->format('Y-m-d') }}
                                    <div class="text-xs text-gray-500">{{ $attendance->date->translatedFormat('l') }}</div>
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
                </table>
            </div>
            
            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد سجل حضور</h3>
                <p class="text-sm text-gray-500">لم يتم تسجيل أي حضور للفترة المحددة</p>
            </div>
        @endif
    </div>
</div>
@endsection