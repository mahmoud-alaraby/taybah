@extends('employee.layouts.app')

@section('title', 'التقرير الشهري')
@section('page-title', 'التقرير الشهري')
@section('page-subtitle', 'تقرير العمل الشهري - ' . $months[$month] . ' ' . $year)

@section('content')
<div class="space-y-6">
    <!-- العودة والطباعة -->
    <div class="flex justify-between items-center">
        <a href="{{ route('employee.work-reports.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للتقارير
        </a>
        
        <a href="{{ route('employee.work-reports.print', ['type' => 'monthly', 'year' => $year, 'month' => $month]) }}" 
           target="_blank"
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            <i class="fas fa-print ml-2"></i>
            طباعة التقرير
        </a>
    </div>

    <!-- إحصائيات الشهر -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">ملخص الشهر</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ number_format($monthlyStats['total_hours'], 2) }}</div>
                <div class="text-sm opacity-90">إجمالي الساعات</div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ number_format($monthlyStats['overtime_hours'], 2) }}</div>
                <div class="text-sm opacity-90">ساعات إضافية</div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ $monthlyStats['days_worked'] }}</div>
                <div class="text-sm opacity-90">أيام العمل</div>
            </div>
            
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ $monthlyStats['expected_working_days'] }}</div>
                <div class="text-sm opacity-90">أيام متوقعة</div>
            </div>
            
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ number_format($monthlyStats['target_achievement'], 1) }}%</div>
                <div class="text-sm opacity-90">تحقيق الهدف</div>
            </div>
            
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ number_format($monthlyStats['average_daily_hours'], 2) }}</div>
                <div class="text-sm opacity-90">متوسط يومي</div>
            </div>
        </div>

        <!-- نسبة الإنجاز -->
        <div class="mb-6">
            @php
                $completionRate = $monthlyStats['expected_working_days'] > 0 
                    ? ($monthlyStats['days_worked'] / $monthlyStats['expected_working_days']) * 100 
                    : 0;
            @endphp
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">نسبة إنجاز أيام العمل</span>
                <span class="text-sm font-medium text-gray-900">{{ number_format($completionRate, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-300"
                     style="width: {{ min(100, $completionRate) }}%"></div>
            </div>
        </div>
    </div>

    <!-- تفاصيل الأيام -->
    @if($summaries->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">تفاصيل الأيام</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اليوم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ساعات العمل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ساعات إضافية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نسبة الهدف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($summaries as $summary)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($summary->date)->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($summary->date)->locale('ar')->dayName }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $summary->total_work_hours >= 8 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ number_format($summary->total_work_hours, 2) }} ساعة
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($summary->overtime_hours > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ number_format($summary->overtime_hours, 2) }} ساعة
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="ml-2">{{ number_format($summary->daily_target_percentage, 1) }}%</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300
                                        {{ $summary->daily_target_percentage >= 100 ? 'bg-green-600' : 
                                           ($summary->daily_target_percentage >= 75 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                         style="width: {{ min(100, $summary->daily_target_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($summary->daily_target_percentage >= 100)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check ml-1"></i>
                                    مكتمل
                                </span>
                            @elseif($summary->daily_target_percentage >= 75)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock ml-1"></i>
                                    جيد
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times ml-1"></i>
                                    ناقص
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- إحصائيات المشاريع -->
    @if(isset($projectStats) && $projectStats->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">إحصائيات المشاريع للشهر</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projectStats as $stat)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-project-diagram text-blue-500 ml-2"></i>
                        {{ $stat['project']->name ?? 'مشروع غير محدد' }}
                    </h4>
                    <span class="text-lg font-bold text-blue-600">{{ number_format($stat['total_hours'], 2) }}</span>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-clock text-gray-400 ml-1"></i>
                            عدد الجلسات:
                        </span>
                        <span class="text-sm font-medium">{{ $stat['sessions_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-calendar-day text-gray-400 ml-1"></i>
                            أيام العمل:
                        </span>
                        <span class="text-sm font-medium">{{ $stat['days_worked'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 flex items-center">
                            <i class="fas fa-chart-line text-gray-400 ml-1"></i>
                            متوسط/يوم:
                        </span>
                        <span class="text-sm font-medium">
                            {{ $stat['days_worked'] > 0 ? number_format($stat['total_hours'] / $stat['days_worked'], 2) : 0 }} ساعة
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($summaries->count() == 0)
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <i class="fas fa-calendar-times text-5xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد بيانات</h3>
        <p class="text-gray-600">لم يتم العثور على بيانات عمل لهذا الشهر</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
// إضافة تأثيرات تفاعلية للمخططات
document.addEventListener('DOMContentLoaded', function() {
    // تحريك شريط التقدم
    const progressBars = document.querySelectorAll('.bg-gradient-to-r');
    progressBars.forEach(bar => {
        if (bar.style.width) {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        }
    });
});
</script>
@endpush

@endsection