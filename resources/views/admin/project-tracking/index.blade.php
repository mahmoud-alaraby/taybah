@extends('admin.layouts.app')

@section('title', 'متابعة المشاريع والمهام')
@section('page-title', 'متابعة المشاريع والمهام')
@section('page-subtitle', 'نظام متابعة المشاريع مع الاستوب ووتش والبصمة - لوحة الإدارة')

@section('content')
<div class="space-y-6" x-data="adminProjectTracking()" x-init="init()">
    
@php
if (!function_exists('formatHoursToHoursMinutes')) {
    function formatHoursToHoursMinutes($hoursFloat) {
        $totalMinutes = round($hoursFloat * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}

if (!function_exists('formatLateTime')) {
    function formatLateTime($minutes) {
        $minutes = abs($minutes);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}

if (!function_exists('formatTime12h')) {
    function formatTime12h($carbonTime) {
        if (is_string($carbonTime)) {
            $carbonTime = \Carbon\Carbon::parse($carbonTime);
        }
        $hour = $carbonTime->hour;
        $minute = $carbonTime->minute;
        $suffix = ($hour >= 12) ? 'PM' : 'AM';
        $hour12 = $hour % 12;
        if ($hour12 == 0) $hour12 = 12;
        return sprintf('%02d:%02d %s', $hour12, $minute, $suffix);
    }
}
@endphp

<style>
    /* أضف هذه الأنماط إلى ملف CSS الرئيسي */

/* تحسينات العدادات النشطة */
.active-timer {
    animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
    0% {
        border-color: rgba(234, 179, 8, 0.5);
    }
    50% {
        border-color: rgba(234, 179, 8, 1);
        box-shadow: 0 0 20px rgba(234, 179, 8, 0.3);
    }
    100% {
        border-color: rgba(234, 179, 8, 0.5);
    }
}

/* تحسينات البصمة */
.attendance-card {
    transition: all 0.3s ease;
}

.attendance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* تحسين شريط التقدم */
.progress-bar {
    position: relative;
    background: linear-gradient(90deg, #e5e7eb, #f3f4f6);
    border-radius: 9999px;
    overflow: hidden;
}

.progress-fill {
    transition: width 1s ease-in-out;
    background: linear-gradient(90deg, #10b981, #059669);
    height: 100%;
    border-radius: 9999px;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    animation: progress-shimmer 2s infinite;
}

@keyframes progress-shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* تحسينات الكروت */
.project-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.project-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.task-item {
    transition: all 0.2s ease;
}

.task-item:hover {
    background-color: #f8fafc;
    transform: translateX(4px);
}

/* تحسينات الأزرار */
.btn-action {
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.btn-action:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    transition: left 0.5s;
}

.btn-action:hover:before {
    left: 100%;
}

/* تحسين الموديلات */
.modal-content {
    backdrop-filter: blur(10px);
    animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* تحسين إدخال البيانات */
.form-input {
    transition: all 0.2s ease;
}

.form-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* تحسين الجداول */
.data-table {
    overflow-x: auto;
}

.data-table table {
    min-width: 600px;
}

.table-row {
    transition: background-color 0.2s ease;
}

.table-row:hover {
    background-color: #f8fafc;
}

/* تحسين الإحصائيات */
.stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
}

.stat-number {
    font-variant-numeric: tabular-nums;
}

/* تحسين الأيقونات */
.icon {
    transition: all 0.2s ease;
}

.icon:hover {
    transform: scale(1.1);
}

/* تحسين الحالات */
.status-badge {
    font-weight: 600;
    letter-spacing: 0.025em;
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* تحسينات الوقت */
.time-display {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    font-variant-numeric: tabular-nums;
}

/* تحسين التنبيهات */
.alert {
    animation: slideInDown 0.3s ease;
}

@keyframes slideInDown {
    from {
        transform: translate3d(0, -100%, 0);
        visibility: visible;
    }
    to {
        transform: translate3d(0, 0, 0);
    }
}

/* تحسينات الاستجابة */
@media (max-width: 768px) {
    .mobile-stack {
        flex-direction: column;
    }
    
    .mobile-full {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .mobile-hide {
        display: none;
    }
}

/* تحسين التمرير */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

/* تحسين الانتقالات */
.smooth-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* تحسين النصوص */
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* تحسين الظلال */
.shadow-soft {
    box-shadow: 0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04);
}

/* تحسين الحدود */
.border-gradient {
    border-image: linear-gradient(135deg, #667eea 0%, #764ba2
</style>
<!-- شريط الحالة العلوي -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <!-- بصمة الأدمن -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-blue-600 to-blue-400 bg-blue-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-base font-semibold text-blue-900 flex items-center space-x-2 rtl:space-x-reverse">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>حالة البصمة</span>
                </p>
                <p class="text-xs text-blue-600 mt-1" x-text="currentTime"></p>
            </div>
            <div class="flex space-x-2 rtl:space-x-reverse">
                @if($todayAttendance)
                    @if($todayAttendance->check_out_time)
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                            تم الانصراف
                        </span>
                    @else
                        <button @click="checkOut()" 
                                class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-semibold hover:bg-red-700">
                            انصراف
                        </button>
                    @endif
                @else
                    <button @click="checkIn()" 
                            class="px-3 py-1 bg-green-600 text-white rounded-full text-xs font-semibold hover:bg-green-700">
                        حضور
                    </button>
                @endif
            </div>
        </div>
        @if($todayAttendance)
            <div class="mt-3 text-xs text-blue-700">
                <p>دخول: {{ formatTime12h($todayAttendance->check_in_time) }}</p>
                @if($todayAttendance->check_out_time)
                    <p>خروج: {{ formatTime12h($todayAttendance->check_out_time) }}</p>
                    <p>الإجمالي: {{ formatHoursToHoursMinutes($todayAttendance->total_hours) }}</p>
                @endif
                @if($todayAttendance->is_late)
                    <p class="text-red-600">تأخير: {{ formatLateTime($todayAttendance->late_minutes) }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- العداد النشط للأدمن -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-yellow-500 to-yellow-300 text-center">
        <p class="text-base font-semibold text-yellow-800 mb-4 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <svg class="w-6 h-6 text-yellow-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            العداد الشخصي
        </p>
        @if($activeTimer)
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-900 font-mono" x-text="activeTimerDisplay">00:00:00</div>
                <p class="text-xs text-yellow-700 mt-1">{{ $activeTimer->project->name ?? 'مشروع محذوف' }}</p>
                <p class="text-xs text-yellow-600">{{ $activeTimer->task->name ?? 'مهمة محذوفة' }}</p>
                <div class="mt-3 space-x-2 rtl:space-x-reverse">
                    <button @click="stopMyTimer()" class="px-3 py-1 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700">
                        <i class="fas fa-stop"></i> إيقاف
                    </button>
                    <button @click="pauseMyTimer()" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm font-semibold hover:bg-yellow-700">
                        <i class="fas fa-pause"></i> توقف
                    </button>
                </div>
            </div>
        @else
            <div class="text-2xl font-bold text-yellow-900">متوقف</div>
            <div class="mt-4">
                <button @click="showStartModal = true" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-play"></i> بدء العداد
                </button>
            </div>
        @endif
    </div>

    <!-- إحصائيات اليوم -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-green-600 to-green-400 bg-green-50 text-center">
        <p class="text-base font-semibold text-green-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <svg class="w-6 h-6 text-green-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="5 15 12 8 19 15"></polyline>
                <line x1="12" y1="8" x2="12" y2="20"></line>
            </svg>
            ساعات اليوم
        </p>
        <div class="text-2xl font-bold text-green-900">{{ formatHoursToHoursMinutes($todayStats['total_hours']) }}</div>
        <div class="mt-2">
            <div class="bg-green-200 rounded-full h-2">
                <div class="bg-green-600 rounded-full h-2 transition-all duration-300" 
                     style="width: {{ min(100, $todayStats['target_percentage']) }}%"></div>
            </div>
            <p class="text-xs text-green-700 mt-1">{{ round($todayStats['target_percentage'], 1) }}% من الهدف</p>
        </div>
    </div>

    <!-- المشاريع النشطة -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-purple-600 to-purple-400 bg-purple-50 text-center">
        <p class="text-base font-semibold text-purple-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <svg class="w-6 h-6 text-purple-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            المشاريع النشطة
        </p>
        <div class="text-2xl font-bold text-purple-900">{{ $activeProjects->count() }}</div>
        <div class="text-xs text-purple-700 mt-2 font-semibold">{{ $todayStats['projects_worked'] }} مشاريع اليوم</div>
    </div>
</div>

<!-- أزرار الإجراءات السريعة -->
<div class="bg-white p-4 rounded-lg shadow">
    <div class="flex flex-wrap gap-2">
        <button @click="showCreateProjectModal()" 
                class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
            <i class="fas fa-plus ml-2"></i>
            مشروع جديد
        </button>
        <button @click="showAddTaskModal()" 
                class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
            <i class="fas fa-tasks ml-2"></i>
            مهمة جديدة
        </button>
        @if(!$activeTimer)
            <button @click="showStartModal = true" 
                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                <i class="fas fa-play ml-2"></i>
                بدء عداد
            </button>
        @endif
   
     
    </div>
</div>

<!-- قائمة المشاريع والمهام -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 rtl">
    <!-- المشاريع -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fas fa-folder-open text-green-600 text-lg"></i>
                <span>المشاريع النشطة</span>
            </h3>
        </div>
        <div class="p-4">
            @if($activeProjects->count() > 0)
                <div class="space-y-5">
                    @foreach($activeProjects as $project)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center space-x-2 rtl:space-x-reverse">
                                        <i class="fas fa-project-diagram text-blue-600"></i>
                                        <span>{{ $project->name }}</span>
                                    </h4>
                                    @if($project->client_name)
                                        <p class="text-sm text-gray-600 mt-1 flex items-center space-x-1 rtl:space-x-reverse">
                                            <i class="fas fa-user text-gray-400"></i>
                                            <span>العميل: {{ $project->client_name }}</span>
                                        </p>
                                    @endif
                                    @if($project->description)
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($project->description, 100) }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    نشط
                                </span>
                            </div>
                            
                            <!-- مهام المشروع -->
                            @if($project->tasks->count() > 0)
                                <div class="space-y-3">
                                    @foreach($project->tasks as $task)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                            <div class="flex-1">
                                                <p class="text-base font-medium text-gray-900 flex items-center space-x-2 rtl:space-x-reverse">
                                                    <i class="fas fa-tasks text-yellow-600"></i>
                                                    <span>{{ $task->name }}</span>
                                                </p>
                                                <div class="flex items-center space-x-4 rtl:space-x-reverse text-sm text-gray-600 mt-1">
                                                    <span class="flex items-center space-x-1 rtl:space-x-reverse">
                                                        <i class="fas fa-hourglass-start text-blue-400"></i>
                                                        <span>مقدر: {{ formatHoursToHoursMinutes($task->estimated_hours) }}</span>
                                                    </span>
                                                    <span class="flex items-center space-x-1 rtl:space-x-reverse">
                                                        <i class="fas fa-clock text-green-400"></i>
                                                        <span>فعلي: {{ formatHoursToHoursMinutes($task->actual_hours) }}</span>
                                                    </span>
                                                    <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-semibold
                                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                            ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')
                                                        }}">
                                                        {{ $task->status === 'completed' ? 'مكتملة' : 
                                                           ($task->status === 'in_progress' ? 'قيد التنفيذ' : 'معلقة') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-1 rtl:space-x-reverse">
                                                <button @click="startTaskTimer({{ $project->id }}, {{ $task->id }})" 
                                                        class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700"
                                                        {{ $activeTimer ? 'disabled' : '' }}>
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">لا توجد مهام في هذا المشروع</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500 flex flex-col items-center space-y-4">
                    <i class="fas fa-folder-open text-5xl"></i>
                    <p class="text-lg">لا توجد مشاريع نشطة</p>
                    <button @click="showCreateProjectModal()" 
                        class="mt-2 text-blue-600 hover:text-blue-800 text-base font-medium">
                        إنشاء مشروع جديد
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- سجل اليوم -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-4 border-b border-gray-300 bg-gradient-to-r from-green-50 via-green-100 to-green-50 relative">
            <h3 class="text-xl font-semibold text-green-900 flex items-center space-x-2 rtl:space-x-reverse">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-200 bg-opacity-30 rounded-full">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </span>
                <span>سجل العمل اليوم</span>
                <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-green-600 to-green-300 rounded-l-md"></span>
            </h3>
        </div>
        <div class="">
            <div class="p-4 m-4 border rounded-md border-gray-200" x-show="todayEntries.length > 0">
                <template x-for="entry in todayEntries" :key="entry.id">
                    <div class="flex mb-3 items-center justify-between p-3 bg-gray-50 rounded shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-green-400">
                        <div>
                            <p class="text-sm font-semibold text-green-800 mb-2 flex items-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-tasks text-yellow-700"></i>
                                <span x-text="entry.task.name"></span>
                            </p>
                            <p class="text-sm text-green-700 flex items-center space-x-1 rtl:space-x-reverse">
                                <i class="fas fa-project-diagram"></i>
                                <span x-text="entry.project.name"></span>
                            </p>
                            <p class="text-sm text-green-600 flex mb-2 items-center space-x-1 rtl:space-x-reverse">
                                <i class="fas fa-clock text-red-600"></i>
                                <span x-text="entry.start_time + ' - ' + (entry.end_time || 'جاري')"></span>
                            </p>
                        </div>
                        <div class="text-right flex flex-col items-end justify-center space-y-1 rtl:space-y-reverse">
                            <p class="text-base font-semibold text-green-700" x-text="entry.formatted_duration"></p>
                            <p class="text-sm text-green-500" x-text="entry.hours + ' ساعة'"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="todayEntries.length === 0" class="text-center py-14 text-green-400">
                <svg class="mx-auto w-20 h-20 mb-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" class="text-green-300" stroke="currentColor"></circle>
                    <line x1="12" y1="8" x2="12" y2="12" class="text-green-400" stroke="currentColor"></line>
                    <line x1="12" y1="16" x2="12" y2="16" class="text-green-400" stroke="currentColor"></line>
                </svg>
                <p class="text-lg">لم يبدأ العمل بعد اليوم</p>
            </div>
        </div>
    </div>
</div>

<!-- مودال بدء العداد -->
<div x-show="showStartModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     @click="showStartModal = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white" @click.stop>
        <h3 class="text-lg font-medium text-gray-900 mb-4">بدء العداد</h3>
        <div class="space-y-4">
            <!-- اختيار المشروع -->
            <div>
                <label class="block text-sm font-medium text-gray-700">المشروع</label>
                <select x-model="selectedProject" @change="loadProjectTasks()" 
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
                    <option value="">اختر المشروع</option>
                    @foreach($activeProjects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- اختيار المهمة -->
            <div>
                <label class="block text-sm font-medium text-gray-700">المهمة</label>
                <select x-model="selectedTask"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
                    <option value="">اختر المهمة</option>
                    <template x-for="task in projectTasks" :key="task.id">
                        <option :value="task.id" x-text="task.name"></option>
                    </template>
                </select>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-medium text-gray-700">وصف (اختياري)</label>
                <textarea x-model="timerDescription" rows="2"
                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition"></textarea>
            </div>

            <!-- الأزرار -->
            <div class="flex justify-end space-x-2 space-x-reverse">
                <button @click="showStartModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    إلغاء
                </button>
                <button @click="startTimer()" 
                        class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow">
                    بدء العداد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- مودال إنشاء مشروع -->
<div x-show="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     @click="showCreateModal = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white" @click.stop>
        <h3 class="text-lg font-medium text-gray-900 mb-4">مشروع جديد</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">اسم المشروع *</label>
                <input type="text" x-model="newProject.name" 
                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">اسم العميل</label>
                <input type="text" x-model="newProject.client_name" 
                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea x-model="newProject.description" rows="3" 
                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">تاريخ البداية *</label>
                    <input type="date" x-model="newProject.start_date" 
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">تاريخ النهاية</label>
                    <input type="date" x-model="newProject.end_date" 
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
                </div>
            </div>

            <div class="flex justify-end space-x-2 space-x-reverse">
                <button @click="showCreateModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    إلغاء
                </button>
                <button @click="createProject()" 
                        class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow">
                    إنشاء المشروع
                </button>
            </div>
        </div>
    </div>
</div>

<!-- مودال إضافة مهمة -->
<div x-show="showTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     @click="showTaskModal = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white" @click.stop>
        <h3 class="text-lg font-medium text-gray-900 mb-4">مهمة جديدة</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">المشروع *</label>
                <select x-model="newTask.project_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
                    <option value="">اختر المشروع</option>
                    @foreach($activeProjects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">اسم المهمة *</label>
                <input type="text" x-model="newTask.name"
                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea x-model="newTask.description" rows="3" 
                          class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">الساعات المقدرة *</label>
                <input type="number" step="0.5" min="0" x-model="newTask.estimated_hours"
                       class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition">
            </div>

            <div class="flex justify-end space-x-2 space-x-reverse">
                <button @click="showTaskModal = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    إلغاء
                </button>
                <button @click="addTask()" 
                        class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow">
                    إضافة المهمة
                </button>
            </div>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function adminProjectTracking() {
    return {
        // Timer state
        activeTimerDisplay: '00:00:00',
        @if($activeTimer)
            myActiveTimer: @json($activeTimer),
            myTimerStartSeconds: {{ $activeTimer->start_time->diffInSeconds(now()) }},
        @else
            myActiveTimer: null,
            myTimerStartSeconds: 0,
        @endif
        timerInterval: null,
        currentTime: '',

        // Modal states
        showStartModal: false,
        showCreateModal: false,
        showTaskModal: false,

        // Form data
        selectedProject: '',
        selectedTask: '',
        timerDescription: '',
        projectTasks: [],
        todayEntries: [],

        // New project/task data
        newProject: {
            name: '',
            client_name: '',
            description: '',
            start_date: '',
            end_date: ''
        },
        newTask: {
            project_id: '',
            name: '',
            description: '',
            estimated_hours: 1
        },

        init() {
            this.updateCurrentTime();
            this.loadTodayEntries();
            
            // Update time every second
            setInterval(() => {
                this.updateCurrentTime();
                this.updateMyTimerDisplay();
            }, 1000);
        },

        updateCurrentTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('ar-SA');
        },

        updateMyTimerDisplay() {
            if (this.myActiveTimer) {
                this.myTimerStartSeconds++;
                this.activeTimerDisplay = this.formatSeconds(this.myTimerStartSeconds);
            }
        },

        async loadTodayEntries() {
            try {
                const response = await fetch('{{ route("admin.project-tracking.today-entries") }}');
                const data = await response.json();
                this.todayEntries = data.entries;
            } catch (error) {
                console.error('Error loading today entries:', error);
            }
        },

        refreshTodayEntries() {
            this.loadTodayEntries();
        },

        loadProjectTasks() {
            const project = @json($activeProjects).find(p => p.id == this.selectedProject);
            if (project) {
                this.projectTasks = project.tasks;
            } else {
                this.projectTasks = [];
            }
            this.selectedTask = '';
        },

        async checkIn() {
            try {
                const response = await fetch('{{ route("admin.project-tracking.check-in") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showAlert('تم تسجيل الحضور بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء تسجيل الحضور', 'error');
            }
        },

        async checkOut() {
            try {
                const response = await fetch('{{ route("admin.project-tracking.check-out") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showAlert('تم تسجيل الانصراف بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء تسجيل الانصراف', 'error');
            }
        },

        async startTimer() {
            if (!this.selectedProject || !this.selectedTask) {
                this.showAlert('يرجى اختيار المشروع والمهمة', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.project-tracking.start-timer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        project_id: this.selectedProject,
                        task_id: this.selectedTask,
                        description: this.timerDescription
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showStartModal = false;
                    this.showAlert('تم بدء العداد بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء بدء العداد', 'error');
            }
        },

        startTaskTimer(projectId, taskId) {
            this.selectedProject = projectId;
            this.selectedTask = taskId;
            this.loadProjectTasks();
            this.timerDescription = '';
            this.startTimer();
        },

        async stopMyTimer() {
            try {
                const response = await fetch('{{ route("admin.project-tracking.stop-timer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showAlert(`تم إيقاف العداد - المدة: ${data.duration}`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
            }
        },

        async pauseMyTimer() {
            try {
                const response = await fetch('{{ route("admin.project-tracking.pause-timer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showAlert(`تم إيقاف العداد مؤقتاً - المدة: ${data.duration}`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
            }
        },

        showCreateProjectModal() {
            this.showCreateModal = true;
            this.newProject = {
                name: '',
                client_name: '',
                description: '',
                start_date: new Date().toISOString().split('T')[0],
                end_date: ''
            };
        },

        async createProject() {
            if (!this.newProject.name || !this.newProject.start_date) {
                this.showAlert('يرجى ملء الحقول المطلوبة', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.project-tracking.create-project") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newProject)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showCreateModal = false;
                    this.showAlert('تم إنشاء المشروع بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert('حدث خطأ أثناء إنشاء المشروع', 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء إنشاء المشروع', 'error');
            }
        },

        showAddTaskModal() {
            this.showTaskModal = true;
            this.newTask = {
                project_id: '',
                name: '',
                description: '',
                estimated_hours: 1
            };
        },

        async addTask() {
            if (!this.newTask.project_id || !this.newTask.name || !this.newTask.estimated_hours) {
                this.showAlert('يرجى ملء الحقول المطلوبة', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.project-tracking.add-task") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newTask)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showTaskModal = false;
                    this.showAlert('تم إضافة المهمة بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
            }
        },

        formatSeconds(seconds) {
            if (seconds < 0) seconds = 0;
            
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        showAlert(message, type) {
            if (typeof Swal !== 'undefined') {
                if (type === 'success') {
                    Swal.fire({
                        title: 'نجح!',
                        text: message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: message,
                        icon: 'error'
                    });
                }
            } else {
                alert(message);
            }
        }
    }
}
</script>
@endpush
@endsection