{{-- resources/views/admin/project-tracking/components/projects-list.blade.php --}}
<div class="bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold">المشاريع النشطة</h3>
                    <p class="text-indigo-100">{{ $activeProjects->count() }} مشروع نشط</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $activeProjects->count() }}</div>
                <div class="text-sm text-indigo-200">مشروع</div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        @if ($activeProjects->count() > 0)
            <div class="space-y-6">
                @foreach ($activeProjects as $project)
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.02]">
                        <!-- Project Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse mb-2">
                                    <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-project-diagram text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-900">{{ $project->name }}</h4>
                                        @if ($project->client_name)
                                            <p class="text-sm text-blue-600 font-medium flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fas fa-user text-blue-500"></i>
                                                <span>العميل: {{ $project->client_name }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if ($project->description)
                                    <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                                        {{ Str::limit($project->description, 100) }}
                                    </p>
                                @endif

                                <!-- Project Stats -->
                                <div class="flex items-center space-x-6 rtl:space-x-reverse text-sm">
                                    <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-700 font-medium">{{ $project->tasks->count() }} مهمة</span>
                                    </div>
                                    <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-gray-700 font-medium">{{ $project->tasks->where('status', 'completed')->count() }} مكتملة</span>
                                    </div>
                                    <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                                        <span class="text-gray-700 font-medium">{{ $project->tasks->where('status', 'in_progress')->count() }} قيد التنفيذ</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Status Badge -->
                            <div class="text-right space-y-2">
                                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800 animate-pulse">
                                    <div class="w-2 h-2 bg-green-500 rounded-full ml-2"></div>
                                    نشط
                                </span>
                                
                                @if($project->end_date)
                                    <div class="text-xs text-gray-500">
                                        ينتهي: {{ $project->end_date->format('Y-m-d') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Project Progress -->
                        @if($project->tasks->count() > 0)
                            @php
                                $completedTasks = $project->tasks->where('status', 'completed')->count();
                                $totalTasks = $project->tasks->count();
                                $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                            @endphp
                            
                            <div class="mb-4 p-3 bg-white/70 rounded-lg">
                                <div class="flex justify-between text-sm text-gray-700 mb-2">
                                    <span class="font-medium">تقدم المشروع</span>
                                    <span class="font-bold">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                         style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                        @endif

                        <!-- Tasks List -->
                        @if ($project->tasks->count() > 0)
                            <div class="space-y-3">
                                <h5 class="font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-tasks text-indigo-600 ml-2"></i>
                                    المهام ({{ $project->tasks->count() }})
                                </h5>
                                
                                <div class="grid gap-3 max-h-60 overflow-y-auto custom-scrollbar">
                                    @foreach ($project->tasks->take(5) as $task)
                                        <div class="bg-white p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 group">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 rtl:space-x-reverse mb-2">
                                                        <div class="h-8 w-8 {{ $task->status === 'completed' ? 'bg-green-500' : ($task->status === 'in_progress' ? 'bg-blue-500' : 'bg-gray-400') }} rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-{{ $task->status === 'completed' ? 'check' : ($task->status === 'in_progress' ? 'play' : 'clock') }} text-white text-sm"></i>
                                                        </div>
                                                        <h6 class="font-medium text-gray-900 group-hover:text-indigo-700 transition-colors">
                                                            {{ $task->name }}
                                                        </h6>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                            <i class="fas fa-hourglass-start text-blue-400"></i>
                                                            <span>مقدر: {{ formatHoursToHoursMinutes($task->estimated_hours) }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                            <i class="fas fa-clock text-green-400"></i>
                                                            <span>فعلي: {{ formatHoursToHoursMinutes($task->actual_hours) }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Task Progress Bar -->
                                                    @if($task->estimated_hours > 0)
                                                        @php
                                                            $taskProgress = min(100, round(($task->actual_hours / $task->estimated_hours) * 100));
                                                        @endphp
                                                        <div class="mt-2">
                                                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                                <span>التقدم</span>
                                                                <span>{{ $taskProgress }}%</span>
                                                            </div>
                                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-1.5 rounded-full transition-all duration-500" 
                                                                     style="width: {{ $taskProgress }}%"></div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Task Actions -->
                                                <div class="text-right space-y-2">
                                                    <!-- Status Badge -->
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold
                                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                           ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $task->status === 'completed' ? 'مكتملة' : ($task->status === 'in_progress' ? 'قيد التنفيذ' : 'معلقة') }}
                                                    </span>
                                                    
                                                    <!-- Start Timer Button -->
                                                    @if($task->status !== 'completed')
                                                        <button @click="startTaskTimer({{ $project->id }}, {{ $task->id }})"
                                                                class="block w-full px-3 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg text-xs font-semibold hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-sm"
                                                                {{ $activeTimer ? 'disabled' : '' }}>
                                                            <i class="fas fa-play ml-1"></i>
                                                            <span>{{ $activeTimer ? 'عداد نشط' : 'بدء العداد' }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if ($project->tasks->count() > 5)
                                        <div class="text-center py-3">
                                            <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
                                                <i class="fas fa-ellipsis-h ml-1"></i>
                                                و {{ $project->tasks->count() - 5 }} مهام أخرى...
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-xl">
                                <i class="fas fa-tasks text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 font-medium">لا توجد مهام في هذا المشروع</p>
                                <button @click="showAddTaskModal(); newTask.project_id = '{{ $project->id }}'"
                                        class="mt-3 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إضافة مهمة
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Show More Projects -->
            @if($activeProjects->count() > 3)
                <div class="mt-6 text-center">
                    <button class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-semibold hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-chevron-down ml-2"></i>
                        عرض المزيد من المشاريع
                    </button>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mb-6">
                    <div class="h-24 w-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد مشاريع نشطة</h3>
                    <p class="text-gray-500">ابدأ بإنشاء مشروع جديد لتتمكن من تتبع المهام والوقت</p>
                </div>
                
                <div class="space-y-3">
                    <button @click="showCreateProjectModal()"
                            class="px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-plus-circle ml-3 text-lg"></i>
                        إنشاء مشروع جديد
                    </button>
                    
                    <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            <i class="fas fa-lightbulb text-yellow-500"></i>
                            <span>نصائح سريعة:</span>
                        </div>
                        <span>أضف مشروعاً → أضف مهام → ابدأ التتبع</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>