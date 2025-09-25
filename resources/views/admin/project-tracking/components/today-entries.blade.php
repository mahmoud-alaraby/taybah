{{-- resources/views/admin/project-tracking/components/today-entries.blade.php --}}
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">سجل العمل اليوم</h3>
                    <p class="text-indigo-100 text-sm" x-text="`إجمالي: ${todayEntries.length} جلسة`"></p>
                </div>
            </div>
            <button @click="loadTodayEntries()" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Entries List -->
        <div x-show="todayEntries.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
            <template x-for="(entry, index) in todayEntries" :key="entry.id">
                <div class="bg-gradient-to-l from-gray-50 to-white p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200"
                     :class="entry.is_active ? 'ring-2 ring-yellow-400 ring-opacity-50' : ''">
                    
                    <!-- Entry Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse mb-1">
                                <i class="fas fa-tasks text-indigo-600"></i>
                                <h4 class="font-semibold text-gray-900" x-text="entry.task.name"></h4>
                                <span x-show="entry.session_number > 1" 
                                      class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium"
                                      x-text="`جلسة #${entry.session_number}`"></span>
                            </div>
                            
                            <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm text-gray-600 mb-2">
                                <i class="fas fa-project-diagram text-purple-500"></i>
                                <span x-text="entry.project.name"></span>
                            </div>

                            <div class="flex items-center space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas fa-play text-green-500"></i>
                                    <span x-text="entry.start_time"></span>
                                </div>
                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas fa-stop text-red-500" :class="entry.is_active ? 'animate-pulse' : ''"></i>
                                    <span x-text="entry.end_time"></span>
                                </div>
                                
                                <!-- Pause indicators -->
                                <div x-show="entry.pause_count > 0" class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas fa-pause text-orange-500"></i>
                                    <span x-text="`${entry.pause_count} توقف`" class="text-orange-600"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Duration and Actions -->
                        <div class="text-right space-y-2">
                            <div class="text-lg font-bold" :class="entry.is_active ? 'text-yellow-600' : 'text-indigo-700'" 
                                 x-text="entry.formatted_duration"></div>
                            <div class="text-xs text-gray-500" x-text="`${entry.hours.toFixed(2)} ساعة`"></div>
                            
                            <!-- Action buttons -->
                            <div class="flex space-x-2 rtl:space-x-reverse">
                                <!-- Restart button -->
                                <button x-show="entry.can_restart && !entry.is_active" 
                                        @click="restartTimer(entry.id)" 
                                        class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition-colors"
                                        title="إعادة تشغيل">
                                    <i class="fas fa-redo"></i>
                                </button>
                                
                                <!-- Edit button -->
                                <button x-show="entry.is_editable" 
                                        @click="showEditModal(entry.id)" 
                                        class="px-3 py-1 bg-gray-500 text-white rounded text-xs hover:bg-gray-600 transition-colors"
                                        title="تعديل الوقت">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Details button -->
                                <button @click="showTimerDetails(entry.id)" 
                                        class="px-3 py-1 bg-purple-500 text-white rounded text-xs hover:bg-purple-600 transition-colors"
                                        title="التفاصيل">
                                    <i class="fas fa-info"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Status indicators -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <span x-show="entry.is_active && entry.is_paused" 
                                  class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium animate-pulse">
                                متوقف مؤقتاً
                            </span>
                            <span x-show="entry.is_active && !entry.is_paused" 
                                  class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium animate-pulse">
                                نشط
                            </span>
                            <span x-show="!entry.is_active" 
                                  class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-medium">
                                مكتمل
                            </span>
                            <span x-show="entry.is_edited" 
                                  class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium"
                                  title="تم تعديل الوقت">
                                <i class="fas fa-edit ml-1"></i>معدل
                            </span>
                        </div>
                        
                        <!-- Progress indicator -->
                        <div class="text-xs text-gray-400" x-text="`${index + 1} من ${todayEntries.length}`"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="todayEntries.length === 0" class="text-center py-12">
            <div class="mb-4">
                <i class="fas fa-clock text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">لم يبدأ العمل بعد اليوم</h3>
                <p class="text-gray-500">ابدأ بتشغيل العداد لتسجيل ساعات العمل</p>
            </div>
            <button @click="showStartModal = true" 
                    class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                <i class="fas fa-play ml-2"></i>
                بدء العداد الآن
            </button>
        </div>

        <!-- Summary Footer -->
        <div x-show="todayEntries.length > 0" class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center text-sm">
                <div class="flex space-x-4 rtl:space-x-reverse">
                    <span class="text-gray-600">
                        <i class="fas fa-clock ml-1 text-indigo-500"></i>
                        إجمالي: <span class="font-semibold" x-text="formatHours(total_hours)"></span>
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-target ml-1 text-green-500"></i>
                        التقدم: <span class="font-semibold" x-text="target_percentage.toFixed(1) + '%'"></span>
                    </span>
                </div>
                <button @click="loadTodayEntries()" 
                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-sync-alt ml-1"></i>
                    تحديث
                </button>
            </div>
        </div>
    </div>
</div>