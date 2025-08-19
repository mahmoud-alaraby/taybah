@extends('admin.layouts.app')

@section('title', 'تفاصيل المشروع')
@section('page-title', 'تفاصيل المشروع: ' . $project->name)
@section('page-subtitle', 'عرض تفاصيل المشروع والمهام والساعات')

@section('content')
<div class="space-y-6">
    <!-- معلومات المشروع -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start mb-6">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $project->name }}</h2>
                @if($project->client_name)
                    <p class="text-lg text-gray-600 mb-2">العميل: {{ $project->client_name }}</p>
                @endif
                @if($project->description)
                    <p class="text-gray-700">{{ $project->description }}</p>
                @endif
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.projects.edit', $project) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل المشروع
                </a>
                <a href="{{ route('admin.projects.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة
                </a>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500">تاريخ البداية:</span>
                <div class="font-medium">{{ $project->start_date->format('Y-m-d') }}</div>
            </div>
            <div>
                <span class="text-gray-500">تاريخ النهاية:</span>
                <div class="font-medium">{{ $project->end_date ? $project->end_date->format('Y-m-d') : 'غير محدد' }}</div>
            </div>
            <div>
                <span class="text-gray-500">الحالة:</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                    {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                       ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                       ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                    {{ $project->status === 'active' ? 'نشط' : 
                       ($project->status === 'completed' ? 'مكتمل' : 
                       ($project->status === 'on_hold' ? 'معلق' : 'ملغي')) }}
                </span>
            </div>
            <div>
                <span class="text-gray-500">تم الإنشاء:</span>
                <div class="font-medium">{{ $project->created_at->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
        <!-- إجمالي المهام -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي المهام</p>
                    <p class="text-2xl font-bold">{{ $projectStats['total_tasks'] }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>

        <!-- مهام مكتملة -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">مهام مكتملة</p>
                    <p class="text-2xl font-bold">{{ $projectStats['completed_tasks'] }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- قيد التنفيذ -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">قيد التنفيذ</p>
                    <p class="text-2xl font-bold">{{ $projectStats['in_progress_tasks'] }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- ساعات فعلية -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">ساعات فعلية</p>
                    <p class="text-2xl font-bold">{{ number_format($projectStats['total_hours'], 1) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <!-- ساعات مقدرة -->
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">ساعات مقدرة</p>
                    <p class="text-2xl font-bold">{{ number_format($projectStats['estimated_hours'], 1) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>


    <!-- شريط التقدم -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">نسبة الإنجاز</h3>
        <div class="flex items-center">
            <div class="flex-1">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>التقدم الحالي</span>
                    <span>{{ $project->completion_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $project->completion_percentage }}%"></div>
                </div>
            </div>
            <div class="mr-4 text-right">
                <div class="text-2xl font-bold text-green-600">{{ $project->completion_percentage }}%</div>
                <div class="text-sm text-gray-500">مكتمل</div>
            </div>
        </div>
    </div>

    <!-- إضافة مهمة جديدة -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">إضافة مهمة جديدة</h3>
            <button onclick="toggleAddTaskForm()" 
                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <i class="fas fa-plus ml-2"></i>
                مهمة جديدة
            </button>
        </div>

    <div id="addTaskForm" class="hidden border-t pt-4">
    <form action="{{ route('admin.projects.add-task', $project) }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اسم المهمة *</label>
                <input type="text" name="name" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                           focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الموظف المسؤول</label>
                <select name="assigned_to"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                           focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition">
                    <option value="">اختر الموظف</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الساعات المقدرة *</label>
                <input type="number" step="0.5" min="0" name="estimated_hours" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                           focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف المهمة</label>
                <textarea name="description" rows="1"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                           focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition"></textarea>
            </div>
        </div>
        <div class="mt-4 flex justify-end space-x-2 space-x-reverse">
            <button type="button" onclick="toggleAddTaskForm()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                إلغاء
            </button>
            <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                إضافة المهمة
            </button>
        </div>
    </form>
</div>

    </div>

 <div class="space-y-6">
  @foreach($project->tasks as $task)
    <div class="relative bg-white p-6 rounded-xl shadow-lg overflow-hidden group">
      <!-- Gradient Border from right -->
      <div class="absolute top-0 right-0 h-full w-2 rounded-l-xl bg-gradient-to-b from-red-400 via-pink-500 to-purple-600"></div>

      <div class="flex justify-between items-start space-x-4 space-x-reverse">
        <div class="flex-1">
          <h4 class="text-lg font-semibold text-gray-900 mb-1 flex items-center space-x-2 space-x-reverse">
            <!-- SVG Icon for Task -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
            </svg>
            <span>{{ $task->name }}</span>
          </h4>
          @if($task->description)
            <p class="text-sm text-gray-600">{{ $task->description }}</p>
          @endif
        </div>

        <div class="flex items-center space-x-3 space-x-reverse">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
               ($task->status === 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 
               ($task->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
            {{ $task->status === 'completed' ? 'مكتملة' : 
               ($task->status === 'in_progress' ? 'قيد التنفيذ' : 
               ($task->status === 'paused' ? 'متوقفة' : 'معلقة')) }}
          </span>
          <button onclick="editTask({{ $task->id }})" aria-label="تعديل المهمة"
            class="text-indigo-600 hover:text-indigo-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15.232 5.232l3.536 3.536M9 11l6 6L4 21l2-7 7-7z"/>
            </svg>
          </button>
          <button onclick="deleteTask({{ $task->id }})" aria-label="حذف المهمة"
            class="text-red-600 hover:text-red-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 7L5 21M5 7l14 14"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm text-gray-700">
        <div>
          <div class="flex items-center space-x-1 space-x-reverse mb-1 text-gray-500 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"/>
              <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>الموظف المسؤول:</span>
          </div>
          <div class="font-medium">{{ $task->assignedEmployee ? $task->assignedEmployee->name : 'غير محدد' }}</div>
        </div>
        <div>
          <div class="flex items-center space-x-1 space-x-reverse mb-1 text-gray-500 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>الساعات المقدرة:</span>
          </div>
          <div class="font-medium">{{ $task->estimated_hours }} ساعة</div>
        </div>
        <div>
          <div class="flex items-center space-x-1 space-x-reverse mb-1 text-gray-500 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z"/>
              <path d="M12 22c4.418 0 8-1.79 8-4v-4c0-2.21-3.582-4-8-4s-8 1.79-8 4v4c0 2.21 3.582 4 8 4z"/>
            </svg>
            <span>الساعات الفعلية:</span>
          </div>
          <div class="font-medium {{ $task->is_over_estimate ? 'text-red-600' : 'text-green-600' }}">
            {{ number_format($task->total_tracked_hours, 1) }} ساعة
            @if($task->is_over_estimate)
              <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4 text-red-500 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01"/>
              </svg>
            @endif
          </div>
        </div>
        <div>
          <div class="flex items-center space-x-1 space-x-reverse mb-1 text-gray-500 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>تاريخ الإكمال:</span>
          </div>
          <div class="font-medium">{{ $task->completed_at ? $task->completed_at->format('Y-m-d') : 'غير مكتملة' }}</div>
        </div>
      </div>

      @if($task->estimated_hours > 0)
        <div class="mt-4">
          <div class="flex justify-between text-xs text-gray-600 mb-1">
            <span>تقدم المهمة</span>
            <span>{{ number_format(min(100, ($task->total_tracked_hours / $task->estimated_hours) * 100), 1) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full {{ $task->total_tracked_hours > $task->estimated_hours ? 'bg-red-500' : 'bg-indigo-600' }}" 
                 style="width: {{ min(100, ($task->total_tracked_hours / $task->estimated_hours) * 100) }}%"></div>
          </div>
        </div>
      @endif
    </div>
  @endforeach

  @if($project->tasks->count() == 0)
    <div class="text-center py-12">
      <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 w-16 h-16 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 17v-6h13v6m-5-6V4H6v7H2l7 8 7-8"/>
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد مهام</h3>
      <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة مهام لهذا المشروع</p>
      <button onclick="toggleAddTaskForm()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16m8-8H4"/></svg>
        إضافة أول مهمة
      </button>
    </div>
  @endif
</div>

<!-- مودال تعديل المهمة -->
<div id="editTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
   <div class="relative top-20 mx-auto p-5 border border-gray-300 rounded-lg w-11/12 max-w-md shadow-lg bg-white">
       <div class="mt-3">
           <h3 class="text-lg font-medium text-gray-900 mb-4">تعديل المهمة</h3>
           <form id="editTaskForm" method="POST">
               @csrf
               @method('PUT')
               <div class="space-y-4">
                   <div>
                       <label class="block text-sm font-medium text-gray-700">اسم المهمة</label>
                       <input type="text" name="name" id="edit_task_name" required
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                                     focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition" />
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الموظف المسؤول</label>
                       <select name="assigned_to" id="edit_task_assigned_to"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                                      focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition">
                           <option value="">اختر الموظف</option>
                           @foreach($employees as $employee)
                               <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                           @endforeach
                       </select>
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الساعات المقدرة</label>
                       <input type="number" step="0.5" min="0" name="estimated_hours" id="edit_task_estimated_hours" required
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                                     focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition" />
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الحالة</label>
                       <select name="status" id="edit_task_status"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                                      focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition">
                           <option value="pending">معلقة</option>
                           <option value="in_progress">قيد التنفيذ</option>
                           <option value="completed">مكتملة</option>
                           <option value="paused">متوقفة</option>
                       </select>
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الوصف</label>
                       <textarea name="description" id="edit_task_description" rows="3"
                                 class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                                        focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition"></textarea>
                   </div>
               </div>
               <div class="flex items-center justify-end space-x-2 space-x-reverse mt-6">
                   <button type="button" onclick="closeEditTaskModal()"
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                       إلغاء
                   </button>
                   <button type="submit"
                           class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                       حفظ التغييرات
                   </button>
               </div>
           </form>
       </div>
   </div>
</div>


@push('scripts')
<script>
function toggleAddTaskForm() {
   const form = document.getElementById('addTaskForm');
   form.classList.toggle('hidden');
}

function editTask(taskId) {
   // جلب بيانات المهمة وملء النموذج
   const tasks = @json($project->tasks);
   const task = tasks.find(t => t.id === taskId);
   
   if (task) {
       document.getElementById('edit_task_name').value = task.name;
       document.getElementById('edit_task_assigned_to').value = task.assigned_to || '';
       document.getElementById('edit_task_estimated_hours').value = task.estimated_hours;
       document.getElementById('edit_task_status').value = task.status;
       document.getElementById('edit_task_description').value = task.description || '';
       
       document.getElementById('editTaskForm').action = `/admin/project-tasks/${taskId}`;
       document.getElementById('editTaskModal').classList.remove('hidden');
   }
}

function closeEditTaskModal() {
   document.getElementById('editTaskModal').classList.add('hidden');
}

function deleteTask(taskId) {
   confirmDelete('حذف المهمة', 'هل أنت متأكد من حذف هذه المهمة؟ سيتم حذف جميع البيانات المرتبطة بها!')
       .then((result) => {
           if (result.isConfirmed) {
               const form = document.createElement('form');
               form.method = 'POST';
               form.action = `/admin/project-tasks/${taskId}`;
               
               const csrfField = document.createElement('input');
               csrfField.type = 'hidden';
               csrfField.name = '_token';
               csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
               form.appendChild(csrfField);
               
               const methodField = document.createElement('input');
               methodField.type = 'hidden';
               methodField.name = '_method';
               methodField.value = 'DELETE';
               form.appendChild(methodField);
               
               document.body.appendChild(form);
               form.submit();
           }
       });
}

// إغلاق المودال عند الضغط خارجه
document.getElementById('editTaskModal').addEventListener('click', function(e) {
   if (e.target === this) {
       closeEditTaskModal();
   }
});
</script>
@endpush
@endsection