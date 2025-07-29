{{-- resources/views/admin/tasks/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'قائمة المهام اليومية')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-900">📝 قائمة المهام اليومية</h1>
            <div class="text-sm text-gray-500">
                {{ now()->format('Y/m/d') }} - {{ now()->format('l') }}
            </div>
        </div>

        <!-- إضافة مهمة جديدة -->
        <form method="POST" action="{{ route('admin.tasks.store') }}" class="flex gap-3 mb-4">
            @csrf
            <input name="title" required
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="أكتب المهمة الجديدة هنا...">
            
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                ➕ إضافة
            </button>
        </form>

        <!-- إضافة تفاصيل (اختياري) -->
        <div x-data="{ showDetails: false }" class="mb-4">
            <button @click="showDetails = !showDetails" 
                    class="text-sm text-blue-600 hover:text-blue-800">
                📝 إضافة تفاصيل للمهمة
            </button>
            
            <div x-show="showDetails" x-transition class="mt-2">
                <form method="POST" action="{{ route('admin.tasks.store') }}" class="space-y-3">
                    @csrf
                    <input name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="عنوان المهمة">
                    
                    <textarea name="details" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="تفاصيل إضافية للمهمة..."></textarea>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        إضافة مهمة مفصلة
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-blue-700">{{ $tasks->count() }}</div>
            <div class="text-sm text-blue-600">إجمالي المهام</div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-yellow-700">{{ $tasks->where('status', 'pending')->count() }}</div>
            <div class="text-sm text-yellow-600">قيد التنفيذ</div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-green-700">{{ $tasks->where('status', 'completed')->count() }}</div>
            <div class="text-sm text-green-600">مكتملة</div>
        </div>
    </div>

    <!-- قائمة المهام -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">مهام اليوم</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <!-- Checkbox -->
                        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" 
                              class="mt-1" id="form-{{ $task->id }}">
                            @csrf
                            @method('PATCH')
                            <input type="checkbox" 
                                   class="w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                   @if($task->status === 'completed') checked @endif
                                   onchange="document.getElementById('form-{{ $task->id }}').submit();">
                        </form>

                        <!-- محتوى المهمة -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <h3 class="font-medium {{ $task->status === 'completed' ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                    {{ $task->title }}
                                </h3>
                                
                                @if($task->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ مكتملة
                                    </span>
                                @endif
                            </div>

                            @if($task->details)
                            <p class="mt-1 text-sm text-gray-600 {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                {{ $task->details }}
                            </p>
                            @endif

                            <!-- معلومات إضافية -->
                            <div class="mt-2 flex items-center space-x-4 space-x-reverse text-xs text-gray-500">
                                <span>⏰ {{ $task->created_at->format('H:i') }}</span>
                                
                                @if($task->creatorAdmin)
                                    <span>👤 {{ $task->creatorAdmin->name }}</span>
                                @elseif($task->creatorEmployee)
                                    <span>👤 {{ $task->creatorEmployee->name }}</span>
                                @endif

                                @if($task->completed_at)
                                    <span>✅ اكتملت: {{ $task->completed_at->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">📝</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مهام لليوم</h3>
                <p class="text-gray-500">ابدأ بإضافة مهمة جديدة أعلاه</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- معلومات الترحيل -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center space-x-2 space-x-reverse">
            <span class="text-blue-600">ℹ️</span>
            <p class="text-sm text-blue-800">
                <strong>ملاحظة:</strong> المهام غير المكتملة يتم ترحيلها تلقائياً لليوم التالي عند فتح الصفحة.
            </p>
        </div>
    </div>
</div>

<!-- Alpine.js for interactive elements -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
