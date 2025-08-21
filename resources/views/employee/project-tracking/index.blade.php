@extends('employee.layouts.app')

@section('title', 'متابعة المشاريع والمهام')
@section('page-title', 'متابعة المشاريع والمهام')
@section('page-subtitle', 'نظام متابعة المشاريع مع الاستوب ووتش والبصمة')

@section('content')
<div class="space-y-6" x-data="projectTracking()" x-init="init()">
    
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

// دالة الوقت بنظام 12 مع AM/PM كابيتال بعد الوقت
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
        // لاحظ أن AM/PM تأتي بعد الوقت مباشرة
        return sprintf('%02d:%02d %s', $hour12, $minute, $suffix);
    }
}
@endphp

<!-- شريط الحالة العلوي -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <!-- بصمة الحضور -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-blue-600 to-blue-400 bg-blue-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-base font-semibold text-blue-900 flex items-center space-x-2 rtl:space-x-reverse">
                    <!-- أيقونة بصمة -->
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                        <path d="M12 2a9 9 0 0 1 9 9c0 4.837-5 11-9 11S3 15.837 3 11a9 9 0 0 1 9-9z"></path>
                        <path d="M12 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
                    </svg>
                    <span>البصمة</span>
                </p>
                <p class="text-xs text-blue-600 mt-1" x-text="currentTime"></p>
            </div>
            <div class="text-right">
                @if($todayAttendance)
                    @if($todayAttendance->check_out_time)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-800">
                            <!-- أيقونة تحقق -->
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                            تم الانصراف
                        </span>
                    @else
                        <button @click="checkOut()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 transition-colors duration-200">
                            <!-- أيقونة خروج -->
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7" />
                                <path d="M3 12h4" />
                            </svg>
                            انصراف
                        </button>
                    @endif
                @else
                    <button @click="checkIn()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm font-semibold hover:bg-green-700 transition-colors duration-200">
                        <!-- أيقونة بصمة إصبع -->
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 11c0-3.314-3-6-7-6s-7 2.686-7 6 3 6 7 6 7-2.686 7-6z" />
                            <path d="M12 11v6" />
                            <path d="M16 10v5" />
                        </svg>
                        حضور
                    </button>
                @endif
            </div>
        </div>
        @if($todayAttendance)
            <div class="mt-4 text-xs font-semibold text-blue-800">
                <div class="flex justify-between mb-2">
                    <span>وقت الحضور:</span>
                    <span>{{ formatTime12h($todayAttendance->check_in_time->timezone('Asia/Riyadh')) }}</span>
                </div>
                @if($todayAttendance->is_late)
                    <div class="flex justify-between text-red-700 font-semibold">
                        <span>تأخير:</span>
                        <span>{{ formatLateTime($todayAttendance->late_minutes) }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-red-700 font-semibold">
                        <span>تأخير:</span>
                        <span>00:00</span>
                    </div>
                @endif
            </div>
        @endif
    </div>



    <!-- الاستوب ووتش -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-yellow-500 to-yellow-300  text-center">
        <p class="text-base font-semibold text-yellow-800  mb-4 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <!-- أيقونة ساعة توقيت -->
            <svg class="w-6 h-6 text-yellow-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            الاستوب ووتش
        </p>
        <div class="text-3xl font-mono font-semibold text-yellow-900" x-text="timerDisplay">00:00:00</div>
        <div class="mt-4 space-x-2 space-x-reverse">
            <button x-show="!isTimerActive" @click="showStartTimerModal()" class="px-3 py-1 bg-green-600 text-white rounded text-sm font-semibold hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-play"></i>
            </button>
            <button x-show="isTimerActive" @click="stopTimer()" class="px-3 py-1 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700 transition-colors duration-200">
                <i class="fas fa-stop"></i>
            </button>
            <button x-show="isTimerActive" @click="pauseTimer()" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm font-semibold hover:bg-yellow-700 transition-colors duration-200">
                <i class="fas fa-pause"></i>
            </button>
        </div>
        <div x-show="activeTimer" class="mt-3 text-sm font-semibold text-yellow-800">
            <p x-text="activeTimer?.project?.name"></p>
            <p x-text="activeTimer?.task?.name"></p>
        </div>
    </div>

    <!-- إحصائيات اليوم -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-blue-600 to-blue-400 bg-blue-50 text-center">
        <p class="text-base font-semibold text-blue-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <!-- أيقونة السهم الصاعد -->
            <svg class="w-6 h-6 text-blue-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <polyline points="5 15 12 8 19 15"></polyline>
                <line x1="12" y1="8" x2="12" y2="20"></line>
            </svg>
            ساعات اليوم
        </p>
        <div class="text-2xl font-bold text-blue-900">{{ formatHoursToHoursMinutes($todayStats['total_hours']) }}</div>
        <div class="w-full bg-blue-300 rounded-full h-2 mt-3">
            <div class="bg-blue-700 h-2 rounded-full" style="width: {{ min(100, $todayStats['target_percentage']) }}%"></div>
        </div>
        <p class="text-xs text-blue-700 mt-2 font-semibold">{{ number_format($todayStats['target_percentage'], 1) }}% من التارجت</p>
    </div>

    <!-- المشاريع النشطة -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gradient-to-b from-purple-600 to-purple-400 bg-purple-50 text-center">
        <p class="text-base font-semibold text-purple-900 mb-3 flex items-center justify-center space-x-2 rtl:space-x-reverse">
            <!-- أيقونة المشاريع -->
            <svg class="w-6 h-6 text-purple-700 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
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
            <a href="{{ route('employee.work-reports.index') }}" 
               class="inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded-md text-sm hover:bg-purple-700">
                <i class="fas fa-chart-line ml-2"></i>
                التقارير
            </a>
            <button @click="refreshTodayEntries()" 
                    class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">
                <i class="fas fa-sync-alt ml-2"></i>
                تحديث
            </button>
        </div>
    </div>

 @php
    // دالة لتحويل القيمة العشرية للساعات إلى صيغة ساعات:دقائق "hh:mm" بأصفار بادئة صحيحة
    function formatHoursToHoursMinutes($hoursFloat) {
        $totalMinutes = round($hoursFloat * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
@endphp

<!-- قائمة المشاريع والمهام -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 rtl">

    <!-- المشاريع -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fas fa-folder-open text-green-600 text-lg"></i>
                <span>مشاريعي النشطة</span>
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
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    {{ $project->completion_percentage }}%
                                </span>
                            </div>
                            
                            <!-- مهام المشروع -->
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
                                                    <span>مقدر: {{ formatHoursToHoursMinutes($task->estimated_hours) }} ساعة:دقيقة</span>
                                                </span>
                                                <span class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <i class="fas fa-clock text-green-400"></i>
                                                    <span>فعلي: {{ formatHoursToHoursMinutes($task->total_tracked_hours) }} ساعة:دقيقة</span>
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
                                        
                                        @if($task->status !== 'completed')
                                            <button @click="startTimerForTask({{ $project->id }}, {{ $task->id }})" 
                                                class=" px-3 py-1 rounded-md bg-green-600 text-white  text-sm hover:bg-green-700 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fas fa-play"></i>
                                                <span>ابدأ</span>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
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
                <!-- أيقونة ساعة محسنة مع خلفية نصف شفافة دائرية -->
                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-200 bg-opacity-30 rounded-full">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </span>
                <span>سجل العمل اليوم</span>
                <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-green-600 to-green-300 rounded-l-md"></span>
            </h3>
        </div>
        <div class="">
            <div class=" p-4 m-4 border rounded-md border-gray-200" x-show="todayEntries.length > 0">
                <template x-for="entry in todayEntries" :key="entry.id">
                    <div class="flex mb-3 items-center justify-between p-3 bg-gray-50 rounded shadow-sm hover:shadow-md transition-shadow duration-200 border-l-4 border-green-400">
                        <div>
                            <p class="text-base font-semibold text-green-900 mb-4 flex items-center space-x-2 rtl:space-x-reverse">
                                <i class="fas fa-tasks text-yellow-700"></i>
                                <span x-text="entry.task.name"></span>
                            </p>
                            <p class="text-sm text-green-700 flex items-center space-x-1 rtl:space-x-reverse">
                                <i class="fas fa-project-diagram"></i>
                                <span x-text="entry.project.name"></span>
                            </p>
                            <p class="text-sm text-green-600 flex mb-4 items-center space-x-1 rtl:space-x-reverse">
                                <i class="fas fa-clock text-red-600"></i>
                                <span x-text="entry.start_time + ' - ' + (entry.end_time || 'جاري')"></span>
                            </p>
                        </div>
                        <div class="text-right flex flex-col items-end justify-center space-y-1 rtl:space-y-reverse">
                            <p class="text-base font-semibold text-green-700" x-text="entry.formatted_duration"></p>
                            <p class="text-sm text-green-500" x-text="entry.hours + ' ساعة:دقيقة'"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="todayEntries.length === 0" class="text-center py-14 text-green-400">
                <!-- أيقونة ساعة توقف محسنة -->
                <svg class="mx-auto w-20 h-20 mb-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="10" class="text-green-300" stroke="currentColor"></circle>
                    <line x1="12" y1="8" x2="12" y2="12" class="text-green-400" stroke="currentColor"></line>
                    <line x1="12" y1="16" x2="12" y2="16" class="text-green-400" stroke="currentColor"></line>
                </svg>
                <p class="text-lg">لم تبدأ العمل بعد اليوم</p>
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
function projectTracking() {
    return {
        // Timer state
        isTimerActive: false,
        activeTimer: null,
        timerSeconds: 0,
        timerDisplay: '00:00:00',
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
            this.checkActiveTimer();
            this.loadTodayEntries();
            
            // Update time every second
            setInterval(() => {
                this.updateCurrentTime();
                if (this.isTimerActive) {
                    this.updateTimerDisplay();
                }
            }, 1000);
        },

        updateCurrentTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('ar-SA');
        },

      async checkActiveTimer() {
    try {
        const response = await fetch('{{ route("employee.project-tracking.active-timer") }}');
        const data = await response.json();
        
        if (data.active) {
            this.isTimerActive = true;
            this.activeTimer = data.timer;
            
            // فرض أن current_seconds يمكن أن تكون نص زمني أو رقم غير صحيح
            if (typeof data.current_seconds === 'string' && data.current_seconds.includes(':')) {
                const timeParts = data.current_seconds.split('.')[0].split(':');
                this.timerSeconds = (+timeParts) * 3600 + (+timeParts[1]) * 60 + (+timeParts[2]);
            } else {
                this.timerSeconds = Math.floor(Number(data.current_seconds));
            }
            
            this.updateTimerDisplay();
        }
    } catch (error) {
        console.error('Error checking active timer:', error);
    }
}
,

 updateTimerDisplay() {
    if (this.isTimerActive) {
        this.timerSeconds++;
    }
    
    const hours = Math.floor(this.timerSeconds / 3600);
    const minutes = Math.floor((this.timerSeconds % 3600) / 60);
    const seconds = this.timerSeconds % 60;
    
    // اعرض فقط بالشكل "00:00:00" (ساعات:دقائق:ثواني) بدون كسور ولا أية إضافات
    this.timerDisplay = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
},

        async loadTodayEntries() {
            try {
                const response = await fetch('{{ route("employee.project-tracking.today-entries") }}');
                const data = await response.json();
                this.todayEntries = data.entries;
            } catch (error) {
                console.error('Error loading today entries:', error);
            }
        },

        refreshTodayEntries() {
            this.loadTodayEntries();
        },

        async checkIn() {
            try {
                const response = await fetch('{{ route("employee.project-tracking.check-in") }}', {
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
                const response = await fetch('{{ route("employee.project-tracking.check-out") }}', {
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

        showStartTimerModal() {
            this.showStartModal = true;
            this.selectedProject = '';
            this.selectedTask = '';
            this.timerDescription = '';
            this.projectTasks = [];
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

        startTimerForTask(projectId, taskId) {
            this.selectedProject = projectId.toString();
            this.selectedTask = taskId.toString();
            this.timerDescription = '';
            
            // تحميل مهام المشروع
            const project = @json($activeProjects).find(p => p.id == projectId);
            if (project) {
                this.projectTasks = project.tasks;
            }
            
            // بدء العداد مباشرة
            this.startTimer();
        },

        async startTimer() {
            if (!this.selectedProject || !this.selectedTask) {
                this.showAlert('يرجى اختيار المشروع والمهمة', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("employee.project-tracking.start-timer") }}', {
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
                    this.isTimerActive = true;
                    this.activeTimer = data.timer;
                    this.timerSeconds = 0;
                    this.showStartModal = false;
                    this.showAlert('تم بدء العداد بنجاح', 'success');
                } else {
                    this.showAlert(data.message, 'error');
                }
            } catch (error) {
                this.showAlert('حدث خطأ أثناء بدء العداد', 'error');
            }
        },

        async stopTimer() {
            try {
                const response = await fetch('{{ route("employee.project-tracking.stop-timer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.isTimerActive = false;
                    this.activeTimer = null;
                    this.timerSeconds = 0;
                    this.timerDisplay = '00:00:00';
                    this.loadTodayEntries();
                    this.showAlert(`تم إيقاف العداد - المدة: ${data.duration}`, 'success');
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
                const response = await fetch('{{ route("employee.project-tracking.create-project") }}', {
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
               const response = await fetch('{{ route("employee.project-tracking.add-task") }}', {
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

       showAlert(message, type) {
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
       }
   }
}
</script>
@endpush
@endsection