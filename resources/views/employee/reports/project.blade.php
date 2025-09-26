@extends('employee.layouts.app')

@section('title', 'تقرير المشاريع')
@section('page-title', 'تقرير المشاريع')
@section('page-subtitle', 'تقرير العمل حسب المشاريع من ' . $startDate . ' إلى ' . $endDate)

@section('content')
<div class="space-y-6">
    <!-- العودة والفلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('employee.work-reports.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للتقارير
            </a>
            
            <a href="{{ route('employee.work-reports.print', array_merge(request()->query(), ['type' => 'project'])) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-print ml-2"></i>
                طباعة التقرير
            </a>
        </div>

        <!-- فلاتر التقرير -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="type" value="project">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                <select name="project_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-red-600 text-white rounded-md py-2 hover:bg-red-700">
                    <i class="fas fa-filter ml-1"></i>
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- ملخص إجمالي -->
    @if($projectBreakdown->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">الملخص الإجمالي</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ number_format($timeEntries->sum('hours'), 2) }}</div>
                <div class="text-sm opacity-90">إجمالي الساعات</div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ $projectBreakdown->count() }}</div>
                <div class="text-sm opacity-90">عدد المشاريع</div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ $timeEntries->count() }}</div>
                <div class="text-sm opacity-90">عدد الجلسات</div>
            </div>
        </div>
    </div>

    <!-- تفاصيل المشاريع -->
    <div class="space-y-6">
        @foreach($projectBreakdown as $breakdown)
        <div class="bg-white shadow rounded-lg p-6">
            <!-- عنوان المشروع -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-project-diagram text-blue-500 ml-3 text-xl"></i>
                    {{ $breakdown['project']->name ?? 'مشروع غير محدد' }}
                </h3>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ number_format($breakdown['total_hours'], 2) }} ساعة
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        {{ $breakdown['sessions_count'] }} جلسة
                    </span>
                </div>
            </div>

            <!-- إحصائيات المشروع -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-blue-600">{{ number_format($breakdown['total_hours'], 2) }}</div>
                    <div class="text-xs text-gray-600">إجمالي الساعات</div>
                </div>
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-green-600">{{ $breakdown['sessions_count'] }}</div>
                    <div class="text-xs text-gray-600">عدد الجلسات</div>
                </div>
                <div class="border border-gray-200 rounded-lg p-3 text-center">
                    <div class="text-lg font-bold text-purple-600">
                        {{ $breakdown['sessions_count'] > 0 ? number_format($breakdown['total_hours'] / $breakdown['sessions_count'], 2) : 0 }}
                    </div>
                    <div class="text-xs text-gray-600">متوسط/جلسة</div>
                </div>
            </div>

            <!-- المهام -->
            @if($breakdown['tasks']->count() > 0)
            <div>
                <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tasks text-orange-500 ml-2"></i>
                    المهام ({{ $breakdown['tasks']->count() }})
                </h4>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">المهمة</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">الساعات</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">الجلسات</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">متوسط/جلسة</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">النسبة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($breakdown['tasks'] as $task)
                            @php
                                $taskPercentage = $breakdown['total_hours'] > 0 
                                    ? ($task['hours'] / $breakdown['total_hours']) * 100 
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                                        {{ $task['task']->name ?? 'مهمة غير محددة' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <span class="font-medium">{{ number_format($task['hours'], 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $task['sessions'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $task['sessions'] > 0 ? number_format($task['hours'] / $task['sessions'], 2) : 0 }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <span class="ml-2">{{ number_format($taskPercentage, 1) }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 style="width: {{ $taskPercentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @if($projectBreakdown->count() == 0)
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <i class="fas fa-project-diagram text-5xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد بيانات</h3>
        <p class="text-gray-600">لم يتم العثور على بيانات مشاريع للفترة المحددة</p>
        <div class="mt-4">
            <a href="{{ route('employee.project-tracking.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <i class="fas fa-plus ml-2"></i>
                بدء تتبع مشروع جديد
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحريك أشرطة التقدم
    const progressBars = document.querySelectorAll('.bg-blue-600');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        if (width) {
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        }
    });
    
    // إضافة tooltips للمشاريع
    const projectHeaders = document.querySelectorAll('h3[class*="text-lg"]');
    projectHeaders.forEach(header => {
        header.addEventListener('mouseenter', function() {
            // يمكن إضافة معلومات إضافية عند التمرير
        });
    });
});
</script>
@endpush

@endsection