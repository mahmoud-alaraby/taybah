@extends('admin.layouts.app')

@section('title', 'متابعة المشاريع والمهام')
@section('page-title', 'متابعة المشاريع والمهام')
@section('page-subtitle', 'نظام متابعة المشاريع مع الاستوب ووتش والبصمة - لوحة الإدارة')

@push('styles')
    <style>
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

        /* تحسين إدخال البيانات */
        .form-input {
            transition: all 0.2s ease;
        }

        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6" x-data="adminProjectTracking()" x-init="init()">

        @php
            if (!function_exists('formatHoursToHoursMinutes')) {
                function formatHoursToHoursMinutes($hoursFloat)
                {
                    $totalMinutes = round($hoursFloat * 60);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    return sprintf('%02d:%02d', $hours, $minutes);
                }
            }

            if (!function_exists('formatLateTime')) {
                function formatLateTime($minutes)
                {
                    $minutes = abs($minutes);
                    $hours = floor($minutes / 60);
                    $mins = $minutes % 60;
                    return sprintf('%02d:%02d', $hours, $mins);
                }
            }

            if (!function_exists('formatTime12h')) {
                function formatTime12h($carbonTime)
                {
                    if (is_string($carbonTime)) {
                        $carbonTime = \Carbon\Carbon::parse($carbonTime);
                    }
                    $hour = $carbonTime->hour;
                    $minute = $carbonTime->minute;
                    $suffix = $hour >= 12 ? 'م' : 'ص';
                    $hour12 = $hour % 12;
                    if ($hour12 == 0) {
                        $hour12 = 12;
                    }
                    return sprintf('%02d:%02d %s', $hour12, $minute, $suffix);
                }
            }
        @endphp

        <!-- شريط الحالة العلوي -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- بصمة الأدمن -->
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-500 attendance-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-base font-semibold text-blue-900 flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="w-6 h-6 text-blue-700 ml-2" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>حالة البصمة</span>
                        </p>
                        <p class="text-xs text-blue-600 mt-1" x-text="currentTime"></p>
                    </div>
                </div>

                @if ($todayAttendance)
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <div class="text-sm text-blue-700">
                            <p class="flex items-center justify-between">
                                <span>دخول:</span>
                                <span class="font-semibold">{{ formatTime12h($todayAttendance->check_in_time) }}</span>
                            </p>

                            <!-- عرض حالة الانصراف المؤقت -->
                            @if ($todayAttendance->is_temp_out)
                                <p class="flex items-center justify-between mt-1 text-orange-600">
                                    <span>انصراف مؤقت:</span>
                                    <span
                                        class="font-semibold">{{ formatTime12h($todayAttendance->temp_checkout_time) }}</span>
                                </p>
                                <p class="text-xs text-orange-500 mt-1">
                                    المدة: {{ $todayAttendance->temp_out_duration ?? 'جاري...' }}
                                </p>
                            @elseif($todayAttendance->temp_checkout_time && $todayAttendance->temp_checkin_time)
                                <p class="flex items-center justify-between mt-1 text-gray-600">
                                    <span>آخر انصراف مؤقت:</span>
                                    <span class="font-semibold text-xs">
                                        {{ formatTime12h($todayAttendance->temp_checkout_time) }} -
                                        {{ formatTime12h($todayAttendance->temp_checkin_time) }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    المدة: {{ $todayAttendance->temp_out_duration }}
                                    ({{ $todayAttendance->temp_checkout_count }} مرات)
                                </p>
                            @endif

                            @if ($todayAttendance->check_out_time)
                                <p class="flex items-center justify-between mt-1">
                                    <span>خروج نهائي:</span>
                                    <span class="font-semibold">{{ formatTime12h($todayAttendance->check_out_time) }}</span>
                                </p>
                                <p class="flex items-center justify-between mt-1">
                                    <span>الإجمالي:</span>
                                    <span
                                        class="font-semibold">{{ formatHoursToHoursMinutes($todayAttendance->total_hours) }}</span>
                                </p>
                            @endif

                            @if ($todayAttendance->is_late)
                                <p class="flex items-center justify-between mt-2 text-red-600 font-medium">
                                    <span>تأخير:</span>
                                    <span>{{ formatLateTime($todayAttendance->late_minutes) }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار البصمة -->
                    <div class="space-y-2">
                        @if ($todayAttendance->check_out_time && $todayAttendance->checkout_type === 'final')
                            <!-- انصراف نهائي -->
                            <div class="flex items-center justify-center p-3 bg-gray-100 rounded-lg">
                                <span class="text-gray-800 font-semibold flex items-center">
                                    <i class="fas fa-check-circle text-green-600 ml-2"></i>
                                    تم الانصراف النهائي
                                </span>
                            </div>
                        @elseif($todayAttendance->is_temp_out)
                            <!-- في انصراف مؤقت -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-center p-2 bg-orange-100 rounded-lg">
                                    <span class="text-orange-800 text-sm font-medium">
                                        <i class="fas fa-clock text-orange-600 ml-1"></i>
                                        في انصراف مؤقت
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="tempCheckIn()"
                                        class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-sign-in-alt ml-1"></i>
                                        عودة
                                    </button>
                                    <button @click="checkOut('final')"
                                        class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors duration-200">
                                        <i class="fas fa-times ml-1"></i>
                                        انصراف نهائي
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- حاضر - يمكن الانصراف -->
                            <div class="grid grid-cols-2 gap-2">
                                <button @click="tempCheckOut()"
                                    class="px-3 py-2 bg-yellow-600 text-white rounded-lg text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                                    <i class="fas fa-pause ml-1"></i>
                                    انصراف مؤقت
                                </button>
                                <button @click="checkOut('final')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt ml-1"></i>
                                    انصراف نهائي
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- زر الحضور -->
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">لم يتم تسجيل الحضور اليوم</p>
                        <button @click="checkIn()"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-sign-in-alt ml-2"></i>
                            تسجيل حضور
                        </button>
                    </div>
                @endif
            </div>

            <!-- العداد النشط للأدمن -->
            <div
                class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-yellow-500 {{ $activeTimer ? 'active-timer' : '' }}">
                <p
                    class="text-base font-semibold text-yellow-800 mb-4 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <svg class="w-6 h-6 text-yellow-700 ml-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    العداد الشخصي
                </p>
                @if ($activeTimer)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-900 font-mono mb-2" x-text="activeTimerDisplay">00:00
                        </div>
                        <p class="text-sm text-yellow-700 mb-1">{{ $activeTimer->project->name ?? 'مشروع محذوف' }}</p>
                        <p class="text-xs text-yellow-600 mb-4">{{ $activeTimer->task->name ?? 'مهمة محذوفة' }}</p>
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <button @click="stopMyTimer()"
                                class="flex-1 px-3 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors duration-200">
                                <i class="fas fa-stop"></i> إيقاف
                            </button>
                            <button @click="pauseMyTimer()"
                                class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded-lg text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                                <i class="fas fa-pause"></i> توقف
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-900 mb-4">متوقف</div>
                        <button @click="showStartModal = true"
                            class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                            <i class="fas fa-play ml-2"></i> بدء العداد
                        </button>
                    </div>
                @endif
            </div>

            <!-- إحصائيات اليوم -->
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                <p
                    class="text-base font-semibold text-green-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <svg class="w-6 h-6 text-green-700 ml-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                    ساعات اليوم
                </p>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-900 mb-2">
                        {{ formatHoursToHoursMinutes($todayStats['total_hours']) }}</div>
                    <div class="progress-bar h-3 mb-2">
                        <div class="progress-fill transition-all duration-300"
                            style="width: {{ min(100, $todayStats['target_percentage']) }}%"></div>
                    </div>
                    <p class="text-sm text-green-700">{{ round($todayStats['target_percentage'], 1) }}% من الهدف اليومي</p>
                </div>
            </div>

            <!-- المشاريع النشطة -->
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-purple-500">
                <p
                    class="text-base font-semibold text-purple-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <svg class="w-6 h-6 text-purple-700 ml-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    المشاريع النشطة
                </p>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-900 mb-2">{{ $activeProjects->count() }}</div>
                    <div class="text-sm text-purple-700 font-medium">{{ $todayStats['projects_worked'] }} مشاريع اليوم
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات السريعة -->
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="flex flex-wrap gap-3">
                <button @click="showCreateProjectModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    مشروع جديد
                </button>
                <button @click="showAddTaskModal()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-tasks ml-2"></i>
                    مهمة جديدة
                </button>
                @if (!$activeTimer)
                    <button @click="showStartModal = true"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                        <i class="fas fa-play ml-2"></i>
                        بدء عداد
                    </button>
                @endif
            </div>
        </div>

        <!-- قائمة المشاريع والمهام -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- المشاريع -->
            <div class="bg-white rounded-lg shadow-lg project-card">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                    <h3 class="text-xl font-semibold text-blue-900 flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-folder-open text-blue-600 text-lg ml-2"></i>
                        <span>المشاريع النشطة</span>
                    </h3>
                </div>
                <div class="p-4">
                    @if ($activeProjects->count() > 0)
                        <div class="space-y-4">
                            @foreach ($activeProjects as $project)
                                <div
                                    class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4
                                                class="font-semibold text-gray-900 text-base flex items-center space-x-2 rtl:space-x-reverse">
                                                <i class="fas fa-project-diagram text-blue-600 ml-2"></i>
                                                <span>{{ $project->name }}</span>
                                            </h4>
                                            @if ($project->client_name)
                                                <p
                                                    class="text-sm text-gray-600 mt-1 flex items-center space-x-1 rtl:space-x-reverse">
                                                    <i class="fas fa-user text-gray-400 ml-1"></i>
                                                    <span>العميل: {{ $project->client_name }}</span>
                                                </p>
                                            @endif
                                            @if ($project->description)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ Str::limit($project->description, 80) }}</p>
                                            @endif
                                        </div>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                    </div>

                                    <!-- مهام المشروع -->
                                    @if ($project->tasks->count() > 0)
                                        <div class="space-y-2 mt-3">
                                            @foreach ($project->tasks->take(3) as $task)
                                                <div
                                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg task-item">
                                                    <div class="flex-1">
                                                        <p
                                                            class="text-sm font-medium text-gray-900 flex items-center space-x-2 rtl:space-x-reverse">
                                                            <i class="fas fa-tasks text-yellow-600 ml-2"></i>
                                                            <span>{{ $task->name }}</span>
                                                        </p>
                                                        <div
                                                            class="flex items-center space-x-4 rtl:space-x-reverse text-xs text-gray-600 mt-1">
                                                            <span class="flex items-center space-x-1 rtl:space-x-reverse">
                                                                <i class="fas fa-hourglass-start text-blue-400"></i>
                                                                <span>مقدر:
                                                                    {{ formatHoursToHoursMinutes($task->estimated_hours) }}</span>
                                                            </span>
                                                            <span class="flex items-center space-x-1 rtl:space-x-reverse">
                                                                <i class="fas fa-clock text-green-400"></i>
                                                                <span>فعلي:
                                                                    {{ formatHoursToHoursMinutes($task->actual_hours) }}</span>
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                        {{ $task->status === 'completed'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($task->status === 'in_progress'
                                                                ? 'bg-blue-100 text-blue-800'
                                                                : 'bg-gray-100 text-gray-800') }}">
                                                                {{ $task->status === 'completed' ? 'مكتملة' : ($task->status === 'in_progress' ? 'قيد التنفيذ' : 'معلقة') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex space-x-1 rtl:space-x-reverse">
                                                        <button
                                                            @click="startTaskTimer({{ $project->id }}, {{ $task->id }})"
                                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition-colors duration-200"
                                                            {{ $activeTimer ? 'disabled' : '' }}>
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($project->tasks->count() > 3)
                                                <p class="text-xs text-gray-500 text-center py-2">
                                                    و {{ $project->tasks->count() - 3 }} مهام أخرى...
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-center py-4 text-sm">لا توجد مهام في هذا المشروع</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-folder-open text-6xl mb-4 text-gray-300"></i>
                            <p class="text-lg mb-2">لا توجد مشاريع نشطة</p>
                            <button @click="showCreateProjectModal()"
                                class="mt-3 text-blue-600 hover:text-blue-800 text-base font-medium underline">
                                إنشاء مشروع جديد
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- سجل اليوم -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                    <h3 class="text-xl font-semibold text-green-900 flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-history text-green-600 text-lg ml-2"></i>
                        <span>سجل العمل اليوم</span>
                    </h3>
                </div>
                <div class="p-4">
                    <div x-show="todayEntries.length > 0" class="space-y-3">
                        <template x-for="entry in todayEntries" :key="entry.id">
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-r-4 border-green-400 hover:shadow-md transition-shadow duration-200">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-semibold text-green-800 mb-1 flex items-center space-x-2 rtl:space-x-reverse">
                                        <i class="fas fa-tasks text-yellow-700 ml-2"></i>
                                        <span x-text="entry.task.name"></span>
                                    </p>
                                    <p class="text-sm text-green-700 mb-1 flex items-center space-x-1 rtl:space-x-reverse">
                                        <i class="fas fa-project-diagram ml-1"></i>
                                        <span x-text="entry.project.name"></span>
                                    </p>
                                    <p class="text-xs text-green-600 flex items-center space-x-1 rtl:space-x-reverse">
                                        <i class="fas fa-clock text-red-600 ml-1"></i>
                                        <span x-text="entry.start_time + ' - ' + (entry.end_time || 'جاري')"></span>
                                    </p>
                                </div>
                                <div class="text-left">
                                    <p class="text-base font-bold text-green-700" x-text="entry.formatted_duration"></p>
                                    <p class="text-xs text-green-500" x-text="entry.hours.toFixed(2) + ' ساعة'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="todayEntries.length === 0" class="text-center py-16 text-green-400">
                        <svg class="mx-auto w-20 h-20 mb-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor"></circle>
                            <polyline points="12 6 12 12 16 14" stroke="currentColor"></polyline>
                        </svg>
                        <p class="text-lg">لم يبدأ العمل بعد اليوم</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- مودال بدء العداد -->
        <div x-show="showStartModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showStartModal = false">
            <div class="bg-white p-6 border w-96 shadow-xl rounded-xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-play text-yellow-600 ml-2"></i>
                    بدء العداد
                </h3>
                <div class="space-y-4">
                    <!-- اختيار المشروع -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                        <select x-model="selectedProject" @change="loadProjectTasks()"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                            <option value="">اختر المشروع</option>
                            @foreach ($activeProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار المهمة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المهمة</label>
                        <select x-model="selectedTask"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                            <option value="">اختر المهمة</option>
                            <template x-for="task in projectTasks" :key="task.id">
                                <option :value="task.id" x-text="task.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- الوصف -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف (اختياري)</label>
                        <textarea x-model="timerDescription" rows="2"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition resize-none"></textarea>
                    </div>

                    <!-- الأزرار -->
                    <div class="flex justify-end space-x-3 rtl:space-x-reverse pt-4">
                        <button @click="showStartModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                            إلغاء
                        </button>
                        <button @click="startTimer()"
                            class="px-5 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 shadow transition-colors duration-200">
                            <i class="fas fa-play ml-2"></i>
                            بدء العداد
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- مودال إنشاء مشروع -->
        <div x-show="showCreateModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false">
            <div class="bg-white p-6 border w-96 shadow-xl rounded-xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-plus text-blue-600 ml-2"></i>
                    مشروع جديد
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المشروع *</label>
                        <input type="text" x-model="newProject.name"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
                        <input type="text" x-model="newProject.client_name"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea x-model="newProject.description" rows="3"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية *</label>
                            <input type="date" x-model="newProject.start_date"
                                class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ النهاية</label>
                            <input type="date" x-model="newProject.end_date"
                                class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 rtl:space-x-reverse pt-4">
                        <button @click="showCreateModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                            إلغاء
                        </button>
                        <button @click="createProject()"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition-colors duration-200">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء المشروع
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- مودال إضافة مهمة -->
        <div x-show="showTaskModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showTaskModal = false">
            <div class="bg-white p-6 border w-96 shadow-xl rounded-xl" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tasks text-green-600 ml-2"></i>
                    مهمة جديدة
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المشروع *</label>
                        <select x-model="newTask.project_id"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 transition">
                            <option value="">اختر المشروع</option>
                            @foreach ($activeProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المهمة *</label>
                        <input type="text" x-model="newTask.name"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea x-model="newTask.description" rows="3"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 transition resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الساعات المقدرة *</label>
                        <input type="number" step="0.5" min="0" x-model="newTask.estimated_hours"
                            class="form-input mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 transition">
                    </div>

                    <div class="flex justify-end space-x-3 rtl:space-x-reverse pt-4">
                        <button @click="showTaskModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                            إلغاء
                        </button>
                        <button @click="addTask()"
                            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow transition-colors duration-200">
                            <i class="fas fa-plus ml-2"></i>
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
                    @if ($activeTimer)
                        myActiveTimer: @json($activeTimer),
                        myTimerStartTime: new Date('{{ $activeTimer->start_time->toISOString() }}'),
                    @else
                        myActiveTimer: null,
                        myTimerStartTime: null,
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

                        // Update current time every minute
                        setInterval(() => {
                            this.updateCurrentTime();
                        }, 60000);

                        // Update timer display every second for accuracy
                        if (this.myActiveTimer && this.myTimerStartTime) {
                            this.updateMyTimerDisplay(); // Initial update
                            this.timerInterval = setInterval(() => {
                                this.updateMyTimerDisplay();
                            }, 1000); // Every second
                        }
                    },

                    updateCurrentTime() {
                        const now = new Date();
                        this.currentTime = now.toLocaleTimeString('ar-SA', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    },

                    updateMyTimerDisplay() {
                        if (this.myActiveTimer && this.myTimerStartTime) {
                            const now = new Date();
                            const diffInSeconds = Math.floor((now - this.myTimerStartTime) / 1000);
                            this.activeTimerDisplay = this.formatSeconds(diffInSeconds);
                        }
                    },

                    async loadTodayEntries() {
                        try {
                            const response = await fetch('{{ route('admin.project-tracking.today-entries') }}');
                            if (!response.ok) throw new Error('Network response was not ok');
                            const data = await response.json();
                            this.todayEntries = data.entries || [];
                        } catch (error) {
                            console.error('Error loading today entries:', error);
                            this.todayEntries = [];
                        }
                    },

                    refreshTodayEntries() {
                        this.loadTodayEntries();
                    },

                    loadProjectTasks() {
                        const project = @json($activeProjects).find(p => p.id == this.selectedProject);
                        if (project && project.tasks) {
                            this.projectTasks = project.tasks;
                        } else {
                            this.projectTasks = [];
                        }
                        this.selectedTask = '';
                    },

                    async checkIn() {
                        try {
                            const response = await fetch('{{ route('admin.project-tracking.check-in') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم تسجيل الحضور بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Check-in error:', error);
                            this.showAlert('حدث خطأ أثناء تسجيل الحضور', 'error');
                        }
                    },
                    async tempCheckOut() {
                        try {
                            const response = await fetch('{{ route('admin.project-tracking.temp-check-out') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم الانصراف المؤقت بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Temp check-out error:', error);
                            this.showAlert('حدث خطأ أثناء الانصراف المؤقت', 'error');
                        }
                    },

                    async tempCheckIn() {
                        try {
                            const response = await fetch('{{ route('admin.project-tracking.temp-check-in') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم العودة من الانصراف المؤقت بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Temp check-in error:', error);
                            this.showAlert('حدث خطأ أثناء العودة من الانصراف', 'error');
                        }
                    },

                    async checkOut(type = 'final') {
                        // تأكيد للانصراف النهائي
                        if (type === 'final') {
                            const confirmed = await Swal.fire({
                                title: 'تأكيد الانصراف النهائي',
                                text: 'هل أنت متأكد من الانصراف النهائي؟ لن تتمكن من العودة اليوم',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'نعم، انصراف نهائي',
                                cancelButtonText: 'إلغاء',
                                reverseButtons: true
                            });

                            if (!confirmed.isConfirmed) {
                                return;
                            }
                        }

                        try {
                            const response = await fetch('{{ route('admin.project-tracking.check-out') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    type: type
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Check-out error:', error);
                            this.showAlert('حدث خطأ أثناء تسجيل الانصراف', 'error');
                        }
                    },

                    async checkOut() {
                        try {
                            const response = await fetch('{{ route('admin.project-tracking.check-out') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم تسجيل الانصراف بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Check-out error:', error);
                            this.showAlert('حدث خطأ أثناء تسجيل الانصراف', 'error');
                        }
                    },

                    async startTimer() {
                        if (!this.selectedProject || !this.selectedTask) {
                            this.showAlert('يرجى اختيار المشروع والمهمة', 'error');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('admin.project-tracking.start-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
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
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Start timer error:', error);
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
                        // Clear the timer interval
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                        }

                        try {
                            const response = await fetch('{{ route('admin.project-tracking.stop-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert(`تم إيقاف العداد - المدة: ${data.duration}`, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Stop timer error:', error);
                            this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
                        }
                    },

                    async pauseMyTimer() {
                        // Clear the timer interval
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                        }

                        try {
                            const response = await fetch('{{ route('admin.project-tracking.pause-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert(`تم إيقاف العداد مؤقتاً - المدة: ${data.duration}`, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Pause timer error:', error);
                            this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
                        }
                    },

                    showCreateProjectModal() {
                        this.showCreateModal = true;
                        const today = new Date();
                        this.newProject = {
                            name: '',
                            client_name: '',
                            description: '',
                            start_date: today.toISOString().split('T')[0],
                            end_date: ''
                        };
                    },

                    async createProject() {
                        if (!this.newProject.name || !this.newProject.start_date) {
                            this.showAlert('يرجى ملء الحقول المطلوبة', 'error');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('admin.project-tracking.create-project') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(this.newProject)
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showCreateModal = false;
                                this.showAlert('تم إنشاء المشروع بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert('حدث خطأ أثناء إنشاء المشروع', 'error');
                            }
                        } catch (error) {
                            console.error('Create project error:', error);
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
                            const response = await fetch('{{ route('admin.project-tracking.add-task') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(this.newTask)
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showTaskModal = false;
                                this.showAlert('تم إضافة المهمة بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
                            }
                        } catch (error) {
                            console.error('Add task error:', error);
                            this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
                        }
                    },

                    // Format seconds to HH:MM:SS
                    formatSeconds(totalSeconds) {
                        if (totalSeconds < 0) totalSeconds = 0;

                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                        const seconds = totalSeconds % 60;

                        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    },

                    // Format seconds to HH:MM (for backward compatibility)
                    formatMinutes(totalMinutes) {
                        if (totalMinutes < 0) totalMinutes = 0;

                        const hours = Math.floor(totalMinutes / 60);
                        const minutes = totalMinutes % 60;

                        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                    },

                    showAlert(message, type) {
                        if (typeof Swal !== 'undefined') {
                            if (type === 'success') {
                                Swal.fire({
                                    title: 'نجح!',
                                    text: message,
                                    icon: 'success',
                                    timer: 2500,
                                    showConfirmButton: false,
                                    position: 'top-end',
                                    toast: true
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonText: 'حسناً',
                                    confirmButtonColor: '#ef4444'
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
