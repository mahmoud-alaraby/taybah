{{-- resources/views/admin/tasks/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'قائمة المهام اليومية')

@section('content')
<div class="max-w-5xl mx-auto p-3 lg:p-4">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">📝 قائمة المهام اليومية</h1>
            <div class="text-sm lg:text-base text-gray-500 bg-gray-50 px-3 py-2 rounded-lg">
                {{ now()->format('Y/m/d') }} - {{ now()->format('l') }}
            </div>
        </div>

        <!-- نموذج إضافة مهمة محسن -->
        <div class="bg-white p-4 lg:p-5 rounded-lg border border-gray-200 shadow-sm mb-4">
            <h3 class="text-lg lg:text-xl font-semibold text-gray-800 mb-4">➕ إضافة مهمة جديدة</h3>
            
            <form method="POST" action="{{ route('admin.tasks.store') }}" class="space-y-4">
                @csrf
                
                <!-- العنوان -->
                <div>
                    <label class="block text-sm lg:text-base font-medium text-gray-700 mb-2">عنوان المهمة *</label>
                    <input name="title" required
                           class="w-full px-3 py-2 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                           placeholder="أكتب عنوان المهمة هنا...">
                </div>

                <!-- التفاصيل -->
                <div>
                    <label class="block text-sm lg:text-base font-medium text-gray-700 mb-2">تفاصيل المهمة (اختياري)</label>
                    <textarea name="details" rows="3"
                              class="w-full px-3 py-2 lg:px-4 lg:py-3 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                              placeholder="اكتب تفاصيل إضافية للمهمة إذا كنت تريد..."></textarea>
                </div>

                <!-- زر الإضافة -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-2 lg:px-8 lg:py-3 text-sm lg:text-base bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                        ➕ إضافة المهمة
                    </button>
                </div>
            </form>
        </div>

        <!-- رسائل النجاح والخطأ -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 text-sm lg:text-base">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm lg:text-base">
                ❌ {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- إحصائيات محسنة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6">
        <!-- إجمالي المهام -->
        <div class="bg-blue-600 rounded-lg p-4 lg:p-6 transform hover:scale-105 transition-transform duration-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold text-white">{{ $tasks->count() }}</div>
                    <div class="text-sm lg:text-base text-blue-100 font-medium mt-1">إجمالي المهام</div>
                </div>
                <div class="text-white">
                    <svg class="w-8 h-8 lg:w-10 lg:h-10" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- قيد التنفيذ -->
        <div class="bg-yellow-500 rounded-lg p-4 lg:p-6 transform hover:scale-105 transition-transform duration-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold text-white">{{ $tasks->where('status', 'pending')->count() }}</div>
                    <div class="text-sm lg:text-base text-yellow-100 font-medium mt-1">قيد التنفيذ</div>
                </div>
                <div class="text-white">
                    <svg class="w-8 h-8 lg:w-10 lg:h-10" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- مكتملة -->
        <div class="bg-green-600 rounded-lg p-4 lg:p-6 transform hover:scale-105 transition-transform duration-200 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl lg:text-3xl font-bold text-white">{{ $tasks->where('status', 'completed')->count() }}</div>
                    <div class="text-sm lg:text-base text-green-100 font-medium mt-1">مكتملة</div>
                </div>
                <div class="text-white">
                    <svg class="w-8 h-8 lg:w-10 lg:h-10" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المهام المحسنة -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 lg:p-5 border-b border-gray-200">
            <h2 class="text-lg lg:text-xl font-semibold text-gray-900">📋 مهام اليوم</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
            <div class="p-4 lg:p-5 hover:bg-gray-50 transition-all duration-200">
                <div class="flex items-start justify-between gap-3 lg:gap-4">
                    <div class="flex items-start space-x-3 lg:space-x-4 space-x-reverse flex-1">
                        <!-- Checkbox -->
                        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" 
                              class="mt-1" id="form-{{ $task->id }}">
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
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs lg:text-sm font-medium bg-green-100 text-green-800 w-fit">
                                        ✅ مكتملة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs lg:text-sm font-medium bg-yellow-100 text-yellow-800 w-fit">
                                        ⏳ قيد التنفيذ
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

                            <!-- معلومات إضافية -->
                            <div class="flex flex-wrap items-center gap-3 lg:gap-4 text-xs lg:text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    ⏰ {{ $task->created_at->format('H:i') }}
                                </span>
                                
                                @if($task->creatorAdmin)
                                    <span class="flex items-center gap-1">
                                        👤 {{ $task->creatorAdmin->name }}
                                    </span>
                                @elseif($task->creatorEmployee)
                                    <span class="flex items-center gap-1">
                                        👤 {{ $task->creatorEmployee->name }}
                                    </span>
                                @endif

                                @if($task->completed_at)
                                    <span class="flex items-center gap-1 text-green-600">
                                        ✅ اكتملت: {{ $task->completed_at->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-red-600 hover:text-white hover:bg-red-600 border border-red-600 rounded-lg transition-all duration-200 transform hover:scale-110 shadow-sm"
                                    title="حذف المهمة">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 lg:p-10 text-center">
                <div class="text-gray-300 text-6xl lg:text-7xl mb-4">📝</div>
                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 mb-2">لا توجد مهام لليوم</h3>
                <p class="text-gray-500 text-sm lg:text-base">ابدأ بإضافة مهمة جديدة باستخدام النموذج أعلاه</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- معلومات الترحيل -->
    <div class="mt-6 p-4 lg:p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <span class="text-blue-600 text-lg lg:text-xl">ℹ️</span>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2 text-sm lg:text-base">معلومات مهمة</h4>
                <p class="text-blue-800 leading-relaxed text-xs lg:text-sm">
                    المهام غير المكتملة يتم ترحيلها تلقائياً لليوم التالي عند فتح الصفحة.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
