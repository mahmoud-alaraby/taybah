@extends('admin.layouts.app')

@section('title', 'متابعة أوقات العمل')
@section('page-title', 'متابعة أوقات العمل')
@section('page-subtitle', 'متابعة ومراقبة أوقات عمل الموظفين وجلسات العمل')

@section('content')
<div class="space-y-6">
    <!-- فلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الموظف</label>
                <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الموظفين</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $employeeId == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} @if($user->type == 'admin') (أدمن) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                <select name="project_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 bg-red-600 text-white rounded-md py-2 hover:bg-red-700">
                    <i class="fas fa-search ml-1"></i>
                    عرض
                </button>
            </div>
            <div></div>
        </form>
    </div>

    <!-- الإحصائيات -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي الساعات</p>
                        <p class="text-2xl font-bold">{{ number_format($dailyStats['total_hours'], 1) }}</p>
                    </div>
                    <i class="fas fa-clock text-2xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">جلسات نشطة</p>
                        <p class="text-2xl font-bold">{{ $dailyStats['active_sessions'] }}</p>
                    </div>
                    <i class="fas fa-play text-2xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">موظفين يعملون</p>
                        <p class="text-2xl font-bold">{{ $dailyStats['employees_working'] }}</p>
                    </div>
                    <i class="fas fa-users text-2xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مشاريع نشطة</p>
                        <p class="text-2xl font-bold">{{ $dailyStats['projects_active'] }}</p>
                    </div>
                    <i class="fas fa-project-diagram text-2xl opacity-80"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- سجل العمل المجمع -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                سجل أوقات العمل المجمع - {{ Carbon\Carbon::parse($date)->format('Y-m-d') }}
            </h3>
            <div class="text-sm text-gray-500">
                {{ $groupedEntries->count() }} مجموعة عمل
            </div>
        </div>
        
        @if($groupedEntries->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($groupedEntries as $group)
                    <div class="p-6" x-data="{ showDetails: false }">
                        <!-- رأس المجموعة -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- معلومات الموظف -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ mb_substr($group['user']->name ?? 'غير معروف', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mr-3">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $group['user']->name ?? 'غير معروف' }}</h4>
                                        <p class="text-sm text-gray-500">{{ $group['user']->employee_id ?? '' }}</p>
                                    </div>
                                </div>

                                <!-- معلومات المشروع والمهمة -->
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        <i class="fas fa-project-diagram text-blue-500 ml-1"></i>
                                        {{ $group['project']->name ?? 'بدون مشروع' }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-tasks text-green-500 ml-1"></i>
                                        {{ $group['task']->name ?? 'بدون مهمة' }}
                                    </div>
                                </div>
                            </div>

                            <!-- ملخص الوقت -->
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $group['summary']['formatted_total_duration'] }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($group['summary']['total_hours'], 2) }} ساعة
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse text-xs text-gray-400 mt-1">
                                    <span>{{ $group['summary']['sessions_count'] }} جلسة</span>
                                    @if($group['summary']['active_sessions'] > 0)
                                        <span class="text-green-600">{{ $group['summary']['active_sessions'] }} نشط</span>
                                    @endif
                                    @if($group['summary']['total_pauses'] > 0)
                                        <span class="text-orange-600">{{ $group['summary']['total_pauses'] }} إيقاف</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- زر عرض التفاصيل -->
                        <button @click="showDetails = !showDetails" 
                                class="flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-chevron-down ml-1" :class="{ 'transform rotate-180': showDetails }"></i>
                            <span x-text="showDetails ? 'إخفاء التفاصيل' : 'عرض تفاصيل الجلسات'"></span>
                        </button>

                        <!-- تفاصيل الجلسات -->
                        <div x-show="showDetails" x-collapse class="mt-4 bg-gray-50 rounded-lg p-4">
                            <div class="space-y-4">
                                @foreach($group['sessions'] as $session)
                                    <div class="bg-white rounded-lg p-4 border @if($session['is_active']) border-green-300 bg-green-50 @else border-gray-200 @endif">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 space-x-reverse">
                                                <!-- رقم الجلسة والحالة -->
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-500">جلسة</div>
                                                    <div class="text-lg font-bold text-gray-900">#{{ $session['session_number'] }}</div>
                                                </div>

                                                <!-- معلومات التوقيت -->
                                                <div>
                                                    <div class="flex items-center space-x-2 space-x-reverse text-sm">
                                                        <span class="text-gray-600">من:</span>
                                                        <span class="font-medium">{{ $session['start_time']->format('H:i:s') }}</span>
                                                        <span class="text-gray-600">إلى:</span>
                                                        <span class="font-medium">
                                                            {{ $session['end_time'] ? $session['end_time']->format('H:i:s') : 'جاري' }}
                                                        </span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $session['description'] }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <!-- المدة والحالة -->
                                                <div class="text-lg font-semibold text-gray-900">
                                                    {{ $session['formatted_duration'] }}
                                                </div>
                                                <div class="flex items-center space-x-2 space-x-reverse text-xs mt-1">
                                                    @if($session['is_active'])
                                                        @if($session['is_paused'])
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-orange-100 text-orange-800">
                                                                <i class="fas fa-pause ml-1"></i>
                                                                متوقف مؤقتاً
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 animate-pulse">
                                                                <i class="fas fa-play ml-1"></i>
                                                                نشط
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                                                            <i class="fas fa-check ml-1"></i>
                                                            مكتمل
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- تفاصيل الإيقاف والاستئناف -->
                                        @if(count($session['session_summary']) > 0)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="text-xs text-gray-600 mb-2">سجل الجلسة:</div>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($session['session_summary'] as $log)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                            {{ $log }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- إحصائيات الجلسة -->
                                        @if($session['pause_count'] > 0 || $session['resume_count'] > 0)
                                            <div class="mt-3 pt-3 border-t border-gray-100">
                                                <div class="flex items-center space-x-4 space-x-reverse text-xs text-gray-500">
                                                    @if($session['pause_count'] > 0)
                                                        <span>
                                                            <i class="fas fa-pause ml-1"></i>
                                                            {{ $session['pause_count'] }} إيقاف
                                                        </span>
                                                    @endif
                                                    @if($session['resume_count'] > 0)
                                                        <span>
                                                            <i class="fas fa-play ml-1"></i>
                                                            {{ $session['resume_count'] }} استئناف
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clock text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد سجل أوقات</h3>
                <p class="text-sm text-gray-500">لم يتم تسجيل أي أوقات عمل في هذا التاريخ</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection