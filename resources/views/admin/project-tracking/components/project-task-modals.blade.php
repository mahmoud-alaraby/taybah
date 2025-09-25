{{-- resources/views/admin/project-tracking/components/project-task-modals.blade.php --}}

<!-- مودال إنشاء مشروع جديد -->
<div x-show="showCreateModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     @click="showCreateModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-screen overflow-y-auto"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <i class="fas fa-folder-plus text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">مشروع جديد</h3>
                        <p class="text-blue-100 text-sm">إضافة مشروع للمتابعة والتتبع</p>
                    </div>
                </div>
                <button @click="showCreateModal = false" class="hover:bg-white/20 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-5">
            <!-- اسم المشروع -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-project-diagram ml-2 text-blue-500"></i>
                    اسم المشروع <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="newProject.name" 
                       placeholder="أدخل اسم المشروع..."
                       class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-lg">
            </div>

            <!-- اسم العميل -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-user-tie ml-2 text-green-500"></i>
                    اسم العميل
                </label>
                <input type="text" x-model="newProject.client_name" 
                       placeholder="أدخل اسم العميل..."
                       class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <!-- وصف المشروع -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-align-left ml-2 text-purple-500"></i>
                    وصف المشروع
                </label>
                <textarea x-model="newProject.description" rows="4" 
                          placeholder="اكتب وصفاً مختصراً للمشروع..."
                          class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"></textarea>
            </div>

            <!-- تواريخ المشروع -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar-plus ml-2 text-green-500"></i>
                        تاريخ البداية <span class="text-red-500">*</span>
                    </label>
                    <input type="date" x-model="newProject.start_date"
                           class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar-times ml-2 text-red-500"></i>
                        تاريخ النهاية
                    </label>
                    <input type="date" x-model="newProject.end_date"
                           class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>

            <!-- ملاحظات -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">ملاحظة:</p>
                        <p>سيتم تخصيص هذا المشروع لك تلقائياً ويمكنك إضافة مهام إليه فوراً</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end space-x-3 rtl:space-x-reverse">
            <button @click="showCreateModal = false"
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-xl hover:bg-gray-400 transition-colors font-medium">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </button>
            <button @click="createProject()"
                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all font-bold shadow-lg transform hover:scale-105">
                <i class="fas fa-plus-circle ml-2"></i>
                إنشاء المشروع
            </button>
        </div>
    </div>
</div>

<!-- مودال إضافة مهمة جديدة -->
<div x-show="showTaskModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     @click="showTaskModal = false">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-screen overflow-y-auto"
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="p-2 bg-white/20 rounded-lg">
                        <i class="fas fa-tasks text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">مهمة جديدة</h3>
                        <p class="text-green-100 text-sm">إضافة مهمة لمشروع موجود</p>
                    </div>
                </div>
                <button @click="showTaskModal = false" class="hover:bg-white/20 p-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-5">
            <!-- اختيار المشروع -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-project-diagram ml-2 text-blue-500"></i>
                    المشروع <span class="text-red-500">*</span>
                </label>
                <select x-model="newTask.project_id"
                        class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-lg">
                    <option value="">اختر المشروع</option>
                    @foreach ($activeProjects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <p x-show="!newTask.project_id" class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle ml-1"></i>
                    إذا لم تجد المشروع المطلوب، أنشئ مشروعاً جديداً أولاً
                </p>
            </div>

            <!-- اسم المهمة -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-check-square ml-2 text-green-500"></i>
                    اسم المهمة <span class="text-red-500">*</span>
                </label>
                <input type="text" x-model="newTask.name" 
                       placeholder="أدخل اسم المهمة..."
                       class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-lg">
            </div>

            <!-- وصف المهمة -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-align-left ml-2 text-purple-500"></i>
                    وصف المهمة
                </label>
                <textarea x-model="newTask.description" rows="3" 
                          placeholder="اكتب وصفاً للمهمة وما يجب إنجازه..."
                          class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all resize-none"></textarea>
            </div>

            <!-- الساعات المقدرة -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-hourglass-half ml-2 text-orange-500"></i>
                    الساعات المقدرة <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" x-model="newTask.estimated_hours" 
                           step="0.5" min="0.5" max="100"
                           placeholder="1.0"
                           class="w-full p-4 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all text-lg pl-16">
                    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">
                        ساعة
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-4 gap-2">
                    <button type="button" @click="newTask.estimated_hours = 0.5"
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                        نصف ساعة
                    </button>
                    <button type="button" @click="newTask.estimated_hours = 1"
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                        ساعة
                    </button>
                    <button type="button" @click="newTask.estimated_hours = 4"
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                        4 ساعات
                    </button>
                    <button type="button" @click="newTask.estimated_hours = 8"
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                        8 ساعات
                    </button>
                </div>
            </div>

            <!-- ملاحظات -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-start space-x-2 rtl:space-x-reverse">
                    <i class="fas fa-lightbulb text-green-600 mt-1"></i>
                    <div class="text-sm text-green-800">
                        <p class="font-semibold mb-1">نصيحة:</p>
                        <p>ستتم إضافة هذه المهمة وتخصيصها لك، ويمكنك البدء بتشغيل العداد فوراً</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end space-x-3 rtl:space-x-reverse">
            <button @click="showTaskModal = false"
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-xl hover:bg-gray-400 transition-colors font-medium">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </button>
            <button @click="addTask()"
                    class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-bold shadow-lg transform hover:scale-105">
                <i class="fas fa-plus-circle ml-2"></i>
                إضافة المهمة
            </button>
        </div>
    </div>
</div>