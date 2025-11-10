{{-- resources/views/employee/tasks/index.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'قائمة المهام اليومية')

@section('content')
<div class="max-w-6xl mx-auto p-3 lg:p-4">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center gap-2">
                <span>📝</span>
                <span>مهامي اليومية</span>
            </h1>
            <div class="text-sm lg:text-base text-gray-500 bg-gray-50 px-3 py-2 rounded-lg">
                {{ now()->format('Y/m/d') }} - {{ now()->format('l') }}
            </div>
        </div>

        <!-- رسائل النجاح والخطأ -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm lg:text-base flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm lg:text-base flex items-center gap-2">
                <span>❌</span> {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- إجمالي المهام -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-4 lg:p-6 text-white shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-200">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold">{{ $tasks->total() }}</div>
                    <div class="text-sm lg:text-base text-blue-100 font-medium mt-1">إجمالي المهام</div>
                </div>
                <svg class="w-8 h-8 lg:w-10 lg:h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>

            <!-- قيد التنفيذ -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 lg:p-6 text-white shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-200">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold">{{ $tasks->where('status', 'pending')->count() }}</div>
                    <div class="text-sm lg:text-base text-yellow-100 font-medium mt-1">قيد التنفيذ</div>
                </div>
                <svg class="w-8 h-8 lg:w-10 lg:h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- مكتملة -->
            <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl p-4 lg:p-6 text-white shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-200">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold">{{ $tasks->where('status', 'completed')->count() }}</div>
                    <div class="text-sm lg:text-base text-green-100 font-medium mt-1">مكتملة</div>
                </div>
                <svg class="w-8 h-8 lg:w-10 lg:h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- نموذج إضافة مهمة محسن -->
    <div class="bg-white p-4 lg:p-5 rounded-lg border border-gray-200 shadow-sm mb-4">
        <h3 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">➕ إضافة مهمة جديدة</h3>
        <form method="POST" action="{{ route('employee.tasks.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm lg:text-base font-medium text-gray-700 mb-2">عنوان المهمة *</label>
                <input name="title" required
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                    placeholder="أكتب عنوان المهمة هنا..." value="{{ old('title') }}">
            </div>

            <div>
                <label class="block text-sm lg:text-base font-medium text-gray-700 mb-2">تفاصيل المهمة (اختياري)</label>
                <textarea name="details" rows="3"
                    class="w-full px-3 py-2 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                    placeholder="اكتب تفاصيل إضافية للمهمة إذا كنت تريد...">{{ old('details') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 lg:px-8 lg:py-3 text-sm lg:text-base bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                    إضافة المهمة
                </button>
            </div>
        </form>
    </div>

    <form method="GET" action="{{ route('employee.tasks.index') }}" class="flex flex-wrap items-end gap-4 mb-6 p-4 bg-white border border-gray-300 rounded-lg shadow-sm">
    {{-- الحالة --}}
    <div>
        <label class="block mb-1 text-gray-700 font-semibold">الحالة</label>
        <select name="status" class="border border-gray-300 rounded-md p-2 focus:outline-none">
            <option value="">الكل</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مهام مكتملة</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>مهام غير مكتملة</option>
        </select>
    </div>
    {{-- التاريخ --}}
    <div>
        <label class="block mb-1 text-gray-700 font-semibold">تاريخ المهمة</label>
        <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-md p-2 focus:outline-none" />
    </div>
    {{-- البحث --}}
    <div class="flex-1">
        <label class="block mb-1 text-gray-700 font-semibold">بحث</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالعنوان أو التفاصيل" class="border border-gray-300 rounded-md p-2 focus:outline-none w-full" />
    </div>
    {{-- زر البحث --}}
    <button type="submit" class="bg-gray-700 text-white px-6 py-2 rounded-md font-bold hover:bg-gray-800 transition">
        بحث
    </button>
    {{-- زر الطباعة --}}
    <a href="{{ route('employee.tasks.print', request()->all()) }}"
       target="_blank"
       class="bg-green-600 text-white px-6 py-2 rounded-md font-bold hover:bg-green-700 transition">
        طباعة
    </a>
</form>

    <!-- قائمة المهام المحسنة -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 lg:p-5 border-b border-gray-200">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center gap-2">📋 مهام اليوم</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
                <div class="p-4 lg:p-5 hover:bg-gray-50 transition-all duration-200 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <!-- Checkbox -->
                        <form method="POST" action="{{ route('employee.tasks.update', $task) }}" id="form-{{ $task->id }}">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox"
                                class="w-5 h-5 lg:w-6 lg:h-6 text-green-600 border-2 border-gray-300 rounded-lg focus:ring-green-500 cursor-pointer transition-all duration-200"
                                @if($task->status === 'completed') checked @endif
                                onchange="document.getElementById('form-{{ $task->id }}').submit();">
                        </form>

                        <!-- محتوى المهمة -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-3 mb-2">
                                <h3 class="text-base lg:text-lg font-semibold {{ $task->status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                    {{ $task->title }}
                                </h3>

                                @if($task->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs lg:text-sm font-medium bg-green-100 text-green-800 w-fit gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                                        مكتملة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs lg:text-sm font-medium bg-yellow-100 text-yellow-800 w-fit gap-1">
                                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" stroke-opacity=".25"></circle><path d="M4 12a8 8 0 018-8v8z"></path></svg>
                                        قيد التنفيذ
                                    </span>
                                @endif
                            </div>

                            @if($task->details)
                                <div class="bg-gray-50 p-3 lg:p-4 rounded-lg mb-3">
                                    <p class="text-sm lg:text-base text-gray-700 leading-relaxed {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                        {{ $task->details }}
                                    </p>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-3 lg:gap-4 text-xs lg:text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                        stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"></path><circle cx="12" cy="12" r="10"
                                            stroke-opacity=".25"></circle></svg>
                                    {{ $task->created_at->format('H:i') }}
                                </span>

                                @if($task->completed_at)
                                    <span class="flex items-center gap-1 text-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
                                        اكتملت: {{ $task->completed_at->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center gap-2">
                        @if($task->created_by_employee === auth('employee')->id())
                            <!-- زر التعديل -->
                            <button onclick="openEditModal({{ $task->id }})"
                                class="p-2 text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-600 rounded-lg transition-all duration-200 transform hover:scale-110 shadow-sm"
                                title="تعديل المهمة">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                            </button>

                            <!-- زر الحذف -->
                            <form method="POST" action="{{ route('employee.tasks.destroy', $task) }}"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded-lg transition-all duration-200 transform hover:scale-110 shadow-sm"
                                    title="حذف المهمة">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 lg:p-10 text-center text-gray-500">
                    <div class="text-gray-300 text-6xl lg:text-7xl mb-4">📝</div>
                    <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-2">لا توجد مهام لليوم</h3>
                    <p class="text-gray-600 text-sm lg:text-base">ابدأ بإضافة مهمة جديدة باستخدام النموذج أعلاه</p>
                </div>
            @endforelse
        </div>

        <!-- روابط البـagination -->
        <div class="px-6 py-4">
            {{ $tasks->links() }}
        </div>
    </div>

    <!-- Modal تعديل المهمة -->
    <div id="editTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>

            <!-- Modal content -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                تعديل المهمة
                            </h3>
                            <div class="mt-4">
                                <form id="editTaskForm" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المهمة *</label>
                                        <input type="text" id="editTitle" name="title" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="أكتب عنوان المهمة...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">تفاصيل المهمة</label>
                                        <textarea id="editDetails" name="details" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="اكتب تفاصيل إضافية..."></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" onclick="updateTask()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        حفظ التعديلات
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات مهمة -->
    <div class="mt-6 p-4 lg:p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg flex items-start gap-3">
        <span class="text-blue-600 text-lg lg:text-xl">ℹ️</span>
        <div>
            <h4 class="font-semibold text-blue-900 mb-2 text-sm lg:text-base">معلومات مهمة</h4>
            <p class="text-blue-800 leading-relaxed text-xs lg:text-sm">
                المهام غير المكتملة يتم ترحيلها تلقائياً لليوم التالي عند فتح الصفحة.
                يمكنك حذف أو تعديل المهام التي قمت بإنشائها فقط.
            </p>
        </div>
    </div>
</div>
<script>
let currentTaskId = null;

// فتح modal التعديل
function openEditModal(taskId) {
    currentTaskId = taskId;
    
    // جلب بيانات المهمة
    fetch(`/employee/tasks/${taskId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                showMessage(data.error, 'error');
                return;
            }
            
            document.getElementById('editTitle').value = data.title;
            document.getElementById('editDetails').value = data.details || '';
            document.getElementById('editTaskModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('حدث خطأ في تحميل بيانات المهمة', 'error');
        });
}

// إغلاق modal التعديل
function closeEditModal() {
    document.getElementById('editTaskModal').classList.add('hidden');
    currentTaskId = null;
    document.getElementById('editTaskForm').reset();
}

// تحديث المهمة
function updateTask() {
    if (!currentTaskId) return;
    
    const title = document.getElementById('editTitle').value.trim();
    if (!title) {
        showMessage('عنوان المهمة مطلوب', 'error');
        return;
    }
    
    // إظهار حالة التحميل
    const updateBtn = document.querySelector('[onclick="updateTask()"]');
    const originalText = updateBtn.innerText;
    updateBtn.innerText = 'جاري الحفظ...';
    updateBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('title', title);
    formData.append('details', document.getElementById('editDetails').value.trim());
    
    fetch(`/employee/tasks/${currentTaskId}/edit`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            showMessage(data.error, 'error');
        } else if (data.success) {
            showMessage(data.message || 'تم تحديث المهمة بنجاح', 'success');
            closeEditModal();
            
            // تحديث المهمة في الصفحة بدون إعادة تحميل كامل
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('حدث خطأ في تحديث المهمة', 'error');
    })
    .finally(() => {
        // إرجاع حالة الزر الطبيعية
        updateBtn.innerText = originalText;
        updateBtn.disabled = false;
    });
}

// دالة لإظهار الرسائل
function showMessage(message, type = 'success') {
    // إزالة الرسائل القديمة
    const oldMessages = document.querySelectorAll('.temp-message');
    oldMessages.forEach(msg => msg.remove());
    
    // إنشاء عنصر الرسالة
    const messageDiv = document.createElement('div');
    messageDiv.className = `temp-message fixed top-4 left-1/2 transform -translate-x-1/2 z-[60] px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' 
            ? 'bg-green-50 border border-green-200 text-green-800' 
            : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    
    messageDiv.innerHTML = `
        <div class="flex items-center gap-2">
            <span>${type === 'success' ? '✅' : '❌'}</span>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    // إضافة الرسالة للصفحة
    document.body.appendChild(messageDiv);
    
    // إزالة الرسالة بعد 3 ثوانٍ
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translate(-50%, -20px)';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 300);
    }, 3000);
}

// التحقق من وجود CSRF token وإضافته إذا لم يكن موجود
document.addEventListener('DOMContentLoaded', function() {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});

// إغلاق modal عند الضغط على Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

// إغلاق modal عند الضغط خارجه
document.addEventListener('click', function(e) {
    const modal = document.getElementById('editTaskModal');
    if (e.target === modal) {
        closeEditModal();
    }
});

// تحسين تجربة المستخدم - التركيز على الحقل الأول عند فتح Modal
function focusFirstInput() {
    setTimeout(() => {
        const firstInput = document.getElementById('editTitle');
        if (firstInput) {
            firstInput.focus();
            firstInput.select();
        }
    }, 100);
}

// تحديث دالة فتح Modal لتتضمن التركيز
const originalOpenEditModal = openEditModal;
openEditModal = function(taskId) {
    originalOpenEditModal(taskId);
    focusFirstInput();
};

// إضافة إمكانية حفظ بـ Ctrl+Enter
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const modal = document.getElementById('editTaskModal');
        if (!modal.classList.contains('hidden')) {
            updateTask();
        }
    }
});

// تحسين validation في الوقت الفعلي
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('editTitle');
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            const updateBtn = document.querySelector('[onclick="updateTask()"]');
            if (this.value.trim()) {
                updateBtn.disabled = false;
                updateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                updateBtn.disabled = true;
                updateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }
});
</script>

@endsection