@extends('admin.layouts.app')

@section('title', 'تقارير الحضور')
@section('page-title', 'تقارير الحضور الشهرية')
@section('page-subtitle', 'تقارير مفصلة لحضور الموظفين والالتزام بالمواعيد')

@php
    // دالة تحويل القيمة العشرية للساعات إلى صيغة hh:mm (ساعات:دقائق)
    function formatHoursMinutes($hoursFloat) {
        $totalMinutes = round($hoursFloat * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // دالة تحويل دقائق التأخير إلى hh:mm مع التعامل مع القيم السالبة
    function formatLateTime($minutes) {
        $minutes = abs($minutes);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    // دالة عرض الوقت بصيغة 12 ساعة مع إضافة فراغ وAM/PM
    function formatTimeTo12HourWithAmPm($time) {
        if (!$time) return '';
        $hour = (int)$time->format('H');
        $minute = $time->format('i');
        $hour12 = $hour % 12;
        if ($hour12 == 0) $hour12 = 12;
        $ampm = $hour < 12 ? 'AM' : 'PM';
        return sprintf('%02d:%s %s', $hour12, $minute, $ampm);
    }
@endphp

@section('content')
<div class="space-y-6">
    <!-- فلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center justify-end space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-calendar-alt text-red-600"></i>
                    <span>السنة</span>
                </label>
                <select name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center justify-end space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-clock text-red-600"></i>
                    <span>الشهر</span>
                </label>
                <select name="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-search"></i>
                    <span>عرض التقرير</span>
                </button>
                <button type="button" onclick="printReport()" class="bg-green-600 text-white rounded-md py-2 px-4 hover:bg-green-700 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-print"></i>
                    <span>طباعة</span>
                </button>
            </div>
        </form>
    </div>

    <!-- تقارير الموظفين -->
    @if($employees->count() > 0)
        <div class="space-y-4">
            @foreach($employees as $employeeData)
                <div class="bg-white shadow rounded-lg p-6 border-r-8 border-gradient-to-b from-red-400 via-red-500 to-red-600">
                    <!-- معلومات الموظف -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 gap-3">
                                <div class="h-12 w-12 rounded-full ml-3 bg-red-600 flex items-center justify-center">
                                    <span class="text-white font-medium text-lg">
                                        {{ mb_substr($employeeData['employee']->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mr-4">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center justify-start space-x-2 rtl:space-x-reverse">
                                    <i class="fas fa-user text-red-500"></i>
                                    <span>{{ $employeeData['employee']->name }}</span>
                                </h3>
                                <p class="text-sm text-gray-600 flex items-center justify-start space-x-1 rtl:space-x-reverse">
                                    <i class="fas fa-id-badge text-red-500"></i>
                                    <span>{{ $employeeData['employee']->employee_id }} - {{ $employeeData['employee']->position }}</span>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center justify-start space-x-1 rtl:space-x-reverse">
                                    <i class="fas fa-building text-red-500"></i>
                                    <span>{{ $employeeData['employee']->department_name }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="text-2xl font-bold text-blue-600 flex items-center justify-start space-x-1 rtl:space-x-reverse">
                                <span>{{ $employeeData['attendance_percentage'] }}%</span>
                                <i class="fas fa-chart-line text-blue-600"></i>
                            </div>
                            <div class="text-sm text-gray-500">نسبة الحضور</div>
                        </div>
                    </div>

                    <!-- إحصائيات الموظف -->
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-xl font-bold text-blue-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-calendar-days"></i>
                                <span>{{ $employeeData['working_days'] }}</span>
                            </div>
                            <div class="text-xs text-gray-600">أيام العمل</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-user-check"></i>
                                <span>{{ $employeeData['attended_days'] }}</span>
                            </div>
                            <div class="text-xs text-gray-600">أيام الحضور</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-yellow-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-clock"></i>
                                <span>{{ $employeeData['on_time_days'] }}</span>
                            </div>
                            <div class="text-xs text-gray-600">في الموعد</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>{{ $employeeData['attended_days'] - $employeeData['on_time_days'] }}</span>
                            </div>
                            <div class="text-xs text-gray-600">متأخر</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-purple-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-clock"></i>
                                <span>{{ formatHoursMinutes($employeeData['total_hours']) }}</span>
                            </div>
                            <div class="text-xs text-gray-600">ساعات العمل</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-orange-600 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-hourglass-half"></i>
                                <span>{{ formatHoursMinutes($employeeData['overtime_hours']) }}</span>
                            </div>
                            <div class="text-xs text-gray-600">ساعات إضافية</div>
                        </div>
                    </div>

                    <!-- شرائط التقدم -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span class="flex items-center justify-end space-x-1 rtl:space-x-reverse">
                                    نسبة الحضور
                                    <i class="fas fa-user-check text-blue-600 mr-3"></i>
                                </span>
                                <span>{{ $employeeData['attendance_percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $employeeData['attendance_percentage'] }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span class="flex items-center justify-end space-x-1 rtl:space-x-reverse">
                                    الالتزام بالمواعيد
                                    <i class="fas fa-clock text-green-600 mr-3"></i>
                                </span>
                                <span>{{ $employeeData['punctuality_percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $employeeData['punctuality_percentage'] }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span class="flex items-center justify-end space-x-1 rtl:space-x-reverse">
                                    متوسط ساعات العمل
                                    <i class="fas fa-chart-area text-purple-600 mr-3"></i>
                                </span>
                                <span>{{ formatHoursMinutes($employeeData['average_daily_hours']) }}/8 ساعة</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($employeeData['average_daily_hours'] / 8) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- تقييم الأداء -->
                    <div class="mt-4 p-3 rounded-lg {{ $employeeData['attendance_percentage'] >= 95 ? 'bg-red-50 border border-red-600' : ($employeeData['attendance_percentage'] >= 85 ? 'bg-yellow-50 border border-yellow-400' : 'bg-red-50 border border-red-600') }}">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse text-sm font-semibold">
                            @if($employeeData['attendance_percentage'] >= 95)
                                <i class="fas fa-star text-red-600"></i>
                                <span class="text-red-600">أداء ممتاز - الموظف ملتزم جداً</span>
                            @elseif($employeeData['attendance_percentage'] >= 85)
                                <i class="fas fa-thumbs-up text-yellow-600"></i>
                                <span class="text-yellow-800">أداء جيد - يحتاج تحسين بسيط</span>
                            @else
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                <span class="text-red-600">يحتاج متابعة - أداء دون المطلوب</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد بيانات حضور</h3>
            <p class="text-sm text-gray-500">لم يتم تسجيل أي بيانات حضور للفترة المحددة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function printReport() {
    const printContent = document.querySelector('.space-y-6').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <title>تقرير الحضور - ${new Date().toLocaleDateString('ar-SA')}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .space-y-6 > * + * { margin-top: 1.5rem; }
                .grid { display: grid; gap: 1rem; }
                .grid-cols-6 { grid-template-columns: repeat(6, 1fr); }
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
                .text-blue-600 { color: #2563eb; }
                .text-green-600 { color: #16a34a; }
                .text-red-600 { color: #dc2626; }
                .text-purple-600 { color: #9333ea; }
                .text-orange-600 { color: #ea580c; }
                .text-yellow-600 { color: #ca8a04; }
                .bg-white { background: white; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem; }
                @media print {
                    .no-print { display: none; }
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            <h1 style="text-align: center; margin-bottom: 2rem;">تقرير الحضور الشهري</h1>
            <p style="text-align: center; margin-bottom: 2rem;">{{ Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush
@endsection
