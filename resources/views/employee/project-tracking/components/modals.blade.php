{{-- resources/views/employee/project-tracking/components/modals.blade.php --}}

<!-- مودال بدء العداد -->
<div x-show="showStartModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     @click="showStartModal = false">
    
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-screen overflow-y-auto"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-play text-xl"></i>
                    <h3 class="text-xl font-bold">بدء العداد</h3>
                </div>
                <button @click="showStartModal = false" class="hover:bg-white/20 p-1 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-5">
            <!-- اختيار المشروع -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-project-diagram ml-2 text-blue-500"></i>
                    المشروع <span class="text-red-500">*</span>
                </label>
                <select x-model="selectedProject" @change="loadProjectTasks()"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                    <option value="">اختر المشروع</option>
                    @foreach ($activeProjects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- اختيار المهمة -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tasks ml-2 text-green-500"></i>
                    المهمة <span class="text-red-500">*</span>
                </label>
                <select x-model="selectedTask"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all"
                        :disabled="!selectedProject">
                    <option value="">اختر المهمة</option>
                    <template x-for="task in projectTasks" :key="task.id">
                        <option :value="task.id" x-text="task.name"></option>
                    </template>
                </select>
                <p x-show="selectedProject && projectTasks.length === 0" class="text-sm text-gray-500 mt-1">
                    لا توجد مهام في هذا المشروع
                </p>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment ml-2 text-purple-500"></i>
                    ملاحظات (اختياري)
                </label>
                <textarea x-model="timerDescription" rows="3" placeholder="اكتب ملاحظات عن العمل..."
                          class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all resize-none"></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 rtl:space-x-reverse">
            <button @click="showStartModal = false"
                    class="px-5 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                إلغاء
            </button>
            <button @click="startTimer()"
                    class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all font-bold shadow-lg transform hover:scale-105">
                <i class="fas fa-play ml-2"></i>
                بدء العداد
            </button>
        </div>
    </div>
</div>

<!-- مودال تعديل الوقت -->
<div x-show="showEditModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     @click="showEditModal = false">
    
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-edit text-xl"></i>
                    <h3 class="text-xl font-bold">تعديل الوقت</h3>
                </div>
                <button @click="showEditModal = false" class="hover:bg-white/20 p-1 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-5">
            <div class="text-center text-gray-600 mb-4">
                <p class="text-sm">الوقت الحالي: <span x-text="currentEditTimer?.formatted_duration" class="font-bold"></span></p>
                <p x-show="currentEditTimer?.is_edited" class="text-xs text-blue-600">
                    الوقت الأصلي: <span x-text="currentEditTimer?.original_duration"></span>
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الساعات</label>
                    <input type="number" x-model="editHours" min="0" max="24" 
                           class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-center text-lg font-bold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الدقائق</label>
                    <input type="number" x-model="editMinutes" min="0" max="59" 
                           class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-center text-lg font-bold">
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle ml-2"></i>
                    سيتم تحديث الوقت في المهمة تلقائياً
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 rtl:space-x-reverse">
            <button @click="showEditModal = false"
                    class="px-5 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                إلغاء
            </button>
            <button @click="editTimer()"
                    class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all font-bold shadow-lg transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </div>
</div>

<!-- مودال تفاصيل التايمر -->
<div x-show="showDetailsModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     @click="showDetailsModal = false">
    
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-info-circle text-xl"></i>
                    <h3 class="text-xl font-bold">تفاصيل الجلسة</h3>
                </div>
                <button @click="showDetailsModal = false" class="hover:bg-white/20 p-1 rounded">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6" x-show="currentTimerDetails">
            <!-- Session Summary -->
            <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-200">
                <h4 class="font-bold text-purple-900 mb-3 flex items-center">
                    <i class="fas fa-chart-bar ml-2"></i>
                    ملخص الجلسات
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-2xl font-bold text-purple-700" x-text="currentTimerDetails.session_summary?.total_sessions || 0"></div>
                        <div class="text-xs text-gray-600">إجمالي الجلسات</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-2xl font-bold text-blue-700" x-text="formatHours((currentTimerDetails.session_summary?.total_time || 0) / 3600)"></div>
                        <div class="text-xs text-gray-600">إجمالي الوقت</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-2xl font-bold text-orange-700" x-text="currentTimerDetails.session_summary?.total_pauses || 0"></div>
                        <div class="text-xs text-gray-600">مرات التوقف</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-2xl font-bold text-green-700" x-text="currentTimerDetails.session_summary?.total_resumes || 0"></div>
                        <div class="text-xs text-gray-600">مرات الاستئناف</div>
                    </div>
                </div>
            </div>

            <!-- Sessions Details -->
            <div x-show="currentTimerDetails.session_summary?.sessions_details?.length > 1" class="mb-6">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-list ml-2"></i>
                    تفاصيل الجلسات
                </h4>
                <div class="space-y-3 max-h-60 overflow-y-auto">
                    <template x-for="session in currentTimerDetails.session_summary?.sessions_details || []" :key="session.session_number">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-gray-900" x-text="`جلسة #${session.session_number}`"></span>
                                <span class="text-sm font-bold" :class="session.is_active ? 'text-green-600' : 'text-gray-600'" 
                                      x-text="session.duration"></span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>البداية:</span>
                                    <span x-text="new Date(session.start_time).toLocaleTimeString('ar-SA', {hour12: true})"></span>
                                </div>
                                <div x-show="session.end_time" class="flex justify-between">
                                    <span>النهاية:</span>
                                    <span x-text="session.end_time ? new Date(session.end_time).toLocaleTimeString('ar-SA', {hour12: true}) : 'جاري'"></span>
                                </div>
                                <div x-show="session.pause_count > 0" class="flex justify-between">
                                    <span>التوقفات:</span>
                                    <span x-text="`${session.pause_count} توقف، ${session.resume_count} استئناف`"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>الحالة:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                          :class="session.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                          x-text="session.is_active ? 'نشط' : 'مكتمل'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Pause/Resume Log -->
            <div x-show="currentTimerDetails.pause_resume_log?.length > 0" class="mb-6">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-pause ml-2"></i>
                    سجل التوقف والاستئناف
                </h4>
                <div class="bg-gray-50 rounded-lg p-3 max-h-40 overflow-y-auto">
                    <div class="space-y-2">
                        <template x-for="(log, index) in currentTimerDetails.pause_resume_log || []" :key="index">
                            <div class="flex justify-between items-center text-sm">
                                <span class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <i :class="log.action === 'pause' ? 'fas fa-pause text-orange-500' : 'fas fa-play text-green-500'"></i>
                                    <span x-text="log.action === 'pause' ? 'توقف' : 'استئناف'"></span>
                                </span>
                                <span class="text-gray-600" x-text="new Date(log.time).toLocaleTimeString('ar-SA', {hour12: true})"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Timer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-blue-900 mb-2">معلومات المهمة</h5>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between">
                            <span>المشروع:</span>
                            <span class="font-medium" x-text="currentTimerDetails.timer?.project?.name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>المهمة:</span>
                            <span class="font-medium" x-text="currentTimerDetails.timer?.task?.name"></span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <h5 class="font-semibold text-green-900 mb-2">إحصائيات الوقت</h5>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between">
                            <span>الوقت الفعلي:</span>
                            <span class="font-bold" x-text="currentTimerDetails.timer?.formatted_duration"></span>
                        </div>
                        <div x-show="currentTimerDetails.total_pause_time" class="flex justify-between">
                            <span>وقت التوقف:</span>
                            <span class="text-orange-600" x-text="currentTimerDetails.total_pause_time"></span>
                        </div>
                        <div x-show="currentTimerDetails.is_edited" class="flex justify-between">
                            <span>الوقت الأصلي:</span>
                            <span class="text-gray-600" x-text="currentTimerDetails.original_duration"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end">
            <button @click="showDetailsModal = false"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                إغلاق
            </button>
        </div>
    </div>
</div>