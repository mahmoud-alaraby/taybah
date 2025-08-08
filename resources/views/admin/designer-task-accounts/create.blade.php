@extends('admin.layouts.app')

@section('title', 'إضافة مهمة')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- عنوان الصفحة --}}
    <h1 class="text-2xl font-bold mb-6">إضافة مهمة</h1>

    @if($totalTasksCount >= 5)
        <div class="mb-6 p-4 bg-red-100 border border-red-500 text-red-700 rounded font-semibold">
            لقد أضفت {{ $totalTasksCount }} مهمة لهذا اليوم والحد الأقصى 5 مهام فقط.
        </div>
    @endif

    {{-- الورقة البيضاء مع ظل وحدود واعتمادات التصميم --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    بيانات المهمة
                </h2>
                <a href="{{ route('admin.designer-task-accounts.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>

        {{-- بداية الفورم --}}
        <form method="POST" action="{{ route('admin.designer-task-accounts.store') }}" class="p-6 space-y-8">
            @csrf

            {{-- التاريخ واختيار المصمم --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="task_date" class="block mb-1 font-semibold text-gray-700">تاريخ المهمة <span class="text-red-500">*</span></label>
                    <input type="date" name="task_date" id="task_date" value="{{ old('task_date', $date) }}" required
                           class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('task_date') border-red-500 @enderror">
                    @error('task_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="designer_id" class="block mb-1 font-semibold text-gray-700">اختر المصمم <span class="text-red-500">*</span></label>
                    <select name="designer_id" id="designer_id" required
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('designer_id') border-red-500 @enderror">
                        <option value="">اختر المصمم</option>
                        @foreach($designers as $designer)
                            <option value="{{ $designer->id }}" @selected(old('designer_id') == $designer->id)>{{ $designer->name }}</option>
                        @endforeach
                    </select>
                    @error('designer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- المهام: عناوين، وصف، سعر --}}
            <div id="tasks-wrapper" class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    المهام (حتى 5 مهام)
                </h3>

                @php
                    $oldTasks = old('tasks', [
                        ['title' => '', 'description' => '', 'price' => ''],
                    ]);
                @endphp

                @foreach($oldTasks as $index => $task)
                    <div class="border border-gray-200 rounded-xl p-6 relative bg-gray-50 shadow-sm">
                        <div class="mb-4">
                            <label for="tasks_{{ $index }}_title" class="block mb-1 font-semibold text-gray-700">عنوان المهمة <span class="text-red-500">*</span></label>
                            <input type="text" id="tasks_{{ $index }}_title" name="tasks[{{ $index }}][title]" value="{{ $task['title'] }}" required
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            @error("tasks.$index.title") <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tasks_{{ $index }}_description" class="block mb-1 font-semibold text-gray-700">الوصف (اختياري)</label>
                            <textarea id="tasks_{{ $index }}_description" name="tasks[{{ $index }}][description]" rows="3"
                                      class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors">{{ $task['description'] }}</textarea>
                            @error("tasks.$index.description") <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tasks_{{ $index }}_price" class="block mb-1 font-semibold text-gray-700">السعر <span class="text-red-500">*</span></label>
                            <input id="tasks_{{ $index }}_price" type="number" step="0.01" min="0" name="tasks[{{ $index }}][price]" value="{{ $task['price'] }}" required
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            @error("tasks.$index.price") <p class="text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($index > 0)
                            <button type="button" class="absolute top-4 left-4 text-red-600 hover:text-red-800 remove-task" title="حذف المهمة" aria-label="حذف المهمة">&times;</button>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- زر إضافة مهمة جديدة --}}
            <button type="button" id="add-task-btn" 
                    class="bg-orange-600 hover:bg-red-700 text-white px-6 py-2 rounded-md shadow font-semibold transition mt-2">
                + إضافة مهمة جديدة
            </button>

            {{-- أزرار الحفظ والإلغاء --}}
            <div class="flex justify-end gap-4 pt-8 border-t border-gray-200">
                <a href="{{ route('admin.designer-task-accounts.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-lg shadow-sm transition-all duration-200 gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    إلغاء
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-green-800 text-white rounded-lg shadow-sm transition-all duration-200 gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                    </svg>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const maxTasks = 5;
    const tasksWrapper = document.getElementById('tasks-wrapper');
    const addTaskBtn = document.getElementById('add-task-btn');

    let currentTaskCount = tasksWrapper.querySelectorAll('.task-item').length || {{ count($oldTasks) }};

    addTaskBtn.addEventListener('click', () => {
        if (currentTaskCount >= maxTasks) {
            alert('لا يمكن إضافة أكثر من 5 مهام');
            return;
        }

        const newIndex = currentTaskCount;
        const newTaskDiv = document.createElement('div');
        newTaskDiv.classList.add('task-item', 'border', 'border-gray-200', 'rounded-xl', 'p-6', 'relative', 'bg-gray-50', 'shadow-sm', 'mb-6');
        newTaskDiv.innerHTML = `
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">عنوان المهمة <span class="text-red-500">*</span></label>
                <input type="text" name="tasks[${newIndex}][title]" required class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">الوصف (اختياري)</label>
                <textarea name="tasks[${newIndex}][description]" rows="3" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors"></textarea>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">السعر <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="tasks[${newIndex}][price]" required class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" />
            </div>
            <button type="button" class="absolute top-4 left-4 text-red-600 hover:text-red-800 remove-task" title="حذف المهمة" aria-label="حذف المهمة">&times;</button>
        `;
        tasksWrapper.appendChild(newTaskDiv);
        currentTaskCount++;

        newTaskDiv.querySelector('.remove-task').addEventListener('click', function () {
            newTaskDiv.remove();
            currentTaskCount--;
            reIndexTasks();
        });
    });

    tasksWrapper.querySelectorAll('.remove-task').forEach(button => {
        button.addEventListener('click', function () {
            this.closest('.task-item').remove();
            currentTaskCount--;
            reIndexTasks();
        });
    });

    function reIndexTasks() {
        const taskItems = tasksWrapper.querySelectorAll('.task-item');
        taskItems.forEach((item, idx) => {
            item.querySelectorAll('input, textarea').forEach(input => {
                if (input.name.includes('title')) {
                    input.name = `tasks[${idx}][title]`;
                } else if (input.name.includes('description')) {
                    input.name = `tasks[${idx}][description]`;
                } else if (input.name.includes('price')) {
                    input.name = `tasks[${idx}][price]`;
                }
            });
        });
    }
});
</script>

@endsection
