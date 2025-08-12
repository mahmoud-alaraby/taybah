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
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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

    <!-- إحصائيات المشروع -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $projectStats['total_tasks'] }}</div>
            <div class="text-sm text-gray-600">إجمالي المهام</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-green-600">{{ $projectStats['completed_tasks'] }}</div>
            <div class="text-sm text-gray-600">مهام مكتملة</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $projectStats['in_progress_tasks'] }}</div>
            <div class="text-sm text-gray-600">قيد التنفيذ</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($projectStats['total_hours'], 1) }}</div>
            <div class="text-sm text-gray-600">ساعات فعلية</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($projectStats['estimated_hours'], 1) }}</div>
            <div class="text-sm text-gray-600">ساعات مقدرة</div>
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
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الموظف المسؤول</label>
                        <select name="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الساعات المقدرة *</label>
                        <input type="number" step="0.5" min="0" name="estimated_hours" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">وصف المهمة</label>
                        <textarea name="description" rows="1"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2 space-x-reverse">
                    <button type="button" onclick="toggleAddTaskForm()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        إضافة المهمة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة المهام -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">مهام المشروع ({{ $project->tasks->count() }})</h3>
        </div>
        
        @if($project->tasks->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($project->tasks as $task)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">{{ $task->name }}</h4>
                                @if($task->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                       ($task->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $task->status === 'completed' ? 'مكتملة' : 
                                       ($task->status === 'in_progress' ? 'قيد التنفيذ' : 
                                       ($task->status === 'paused' ? 'متوقفة' : 'معلقة')) }}
                                </span>
                                <button onclick="editTask({{ $task->id }})" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteTask({{ $task->id }})" 
                                        class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">الموظف المسؤول:</span>
                                <div class="font-medium">
                                    {{ $task->assignedEmployee ? $task->assignedEmployee->name : 'غير محدد' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">الساعات المقدرة:</span>
                                <div class="font-medium">{{ $task->estimated_hours }} ساعة</div>
                            </div>
                            <div>
<span class="text-gray-500">الساعات الفعلية:</span>
                               <div class="font-medium {{ $task->is_over_estimate ? 'text-red-600' : 'text-green-600' }}">
                                   {{ number_format($task->total_tracked_hours, 1) }} ساعة
                                   @if($task->is_over_estimate)
                                       <i class="fas fa-exclamation-triangle text-red-500 ml-1" title="تجاوز التقدير"></i>
                                   @endif
                               </div>
                           </div>
                           <div>
                               <span class="text-gray-500">تاريخ الإكمال:</span>
                               <div class="font-medium">
                                   {{ $task->completed_at ? $task->completed_at->format('Y-m-d') : 'غير مكتملة' }}
                               </div>
                           </div>
                       </div>

                       <!-- شريط تقدم المهمة -->
                       @if($task->estimated_hours > 0)
                           <div class="mt-3">
                               <div class="flex justify-between text-xs text-gray-600 mb-1">
                                   <span>تقدم المهمة</span>
                                   <span>{{ number_format(min(100, ($task->total_tracked_hours / $task->estimated_hours) * 100), 1) }}%</span>
                               </div>
                               <div class="w-full bg-gray-200 rounded-full h-2">
                                   <div class="h-2 rounded-full {{ $task->total_tracked_hours > $task->estimated_hours ? 'bg-red-500' : 'bg-blue-600' }}" 
                                        style="width: {{ min(100, ($task->total_tracked_hours / $task->estimated_hours) * 100) }}%"></div>
                               </div>
                           </div>
                       @endif
                   </div>
               @endforeach
           </div>
       @else
           <div class="text-center py-12">
               <i class="fas fa-tasks text-6xl text-gray-400 mb-4"></i>
               <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مهام</h3>
               <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة مهام لهذا المشروع</p>
               <button onclick="toggleAddTaskForm()" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                   <i class="fas fa-plus ml-2"></i>
                   إضافة أول مهمة
               </button>
           </div>
       @endif
   </div>

   <!-- سجل الوقت للمشروع -->
   <div class="bg-white shadow rounded-lg">
       <div class="px-6 py-4 border-b border-gray-200">
           <h3 class="text-lg font-medium text-gray-900">سجل أوقات العمل</h3>
       </div>
       
       @if($project->timeTracking->count() > 0)
           <div class="overflow-x-auto">
               <table class="min-w-full divide-y divide-gray-200">
                   <thead class="bg-gray-50">
                       <tr>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المهمة</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت البداية</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت النهاية</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدة</th>
                           <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                       </tr>
                   </thead>
                   <tbody class="bg-white divide-y divide-gray-200">
                       @foreach($project->timeTracking->take(20) as $timeEntry)
                           <tr class="hover:bg-gray-50">
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                   {{ $timeEntry->employee->name }}
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                   {{ $timeEntry->task->name }}
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                   {{ $timeEntry->date->format('Y-m-d') }}
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                   {{ $timeEntry->start_time->format('H:i') }}
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                   {{ $timeEntry->end_time ? $timeEntry->end_time->format('H:i') : 'جاري' }}
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                   {{ $timeEntry->formatted_duration }}
                               </td>
                               <td class="px-6 py-4 text-sm text-gray-500">
                                   {{ Str::limit($timeEntry->description, 50) }}
                               </td>
                           </tr>
                       @endforeach
                   </tbody>
               </table>
           </div>
           
           @if($project->timeTracking->count() > 20)
               <div class="px-6 py-3 bg-gray-50 text-center">
                   <p class="text-sm text-gray-500">عرض أحدث 20 إدخال. المجموع: {{ $project->timeTracking->count() }} إدخال</p>
               </div>
           @endif
       @else
           <div class="text-center py-8">
               <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
               <p class="text-sm text-gray-500">لا يوجد سجل أوقات لهذا المشروع بعد</p>
           </div>
       @endif
   </div>
</div>

<!-- مودال تعديل المهمة -->
<div id="editTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
   <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
       <div class="mt-3">
           <h3 class="text-lg font-medium text-gray-900 mb-4">تعديل المهمة</h3>
           <form id="editTaskForm" method="POST">
               @csrf
               @method('PUT')
               <div class="space-y-4">
                   <div>
                       <label class="block text-sm font-medium text-gray-700">اسم المهمة</label>
                       <input type="text" name="name" id="edit_task_name" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الموظف المسؤول</label>
                       <select name="assigned_to" id="edit_task_assigned_to" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                           <option value="">اختر الموظف</option>
                           @foreach($employees as $employee)
                               <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                           @endforeach
                       </select>
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الساعات المقدرة</label>
                       <input type="number" step="0.5" min="0" name="estimated_hours" id="edit_task_estimated_hours" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الحالة</label>
                       <select name="status" id="edit_task_status" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                           <option value="pending">معلقة</option>
                           <option value="in_progress">قيد التنفيذ</option>
                           <option value="completed">مكتملة</option>
                           <option value="paused">متوقفة</option>
                       </select>
                   </div>
                   <div>
                       <label class="block text-sm font-medium text-gray-700">الوصف</label>
                       <textarea name="description" id="edit_task_description" rows="3"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                   </div>
               </div>
               <div class="flex items-center justify-end space-x-2 space-x-reverse mt-6">
                   <button type="button" onclick="closeEditTaskModal()" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                       إلغاء
                   </button>
                   <button type="submit" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
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