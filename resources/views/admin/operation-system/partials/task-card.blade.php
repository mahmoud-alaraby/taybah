{{-- resources/views/admin/operation-system/partials/task-card.blade.php --}}
@php
    $statusColors = [
        'pending' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'قيد الانتظار'],
        'in_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'قيد التنفيذ'],
        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'مكتملة'],
        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'ملغية'],
    ];
    $status = $statusColors[$task->status] ?? $statusColors['pending'];
@endphp

<div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 {{ $task->is_reserved ? 'ring-2 ring-green-200 bg-green-50' : '' }}">
    <div class="p-3 sm:p-4">
        {{-- Employee Info --}}
        <div class="flex items-center mb-3">
            @if($task->employee_avatar)
                <img src="{{ asset('storage/' . $task->employee_avatar) }}" alt="{{ $task->employee_name }}" 
                     class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-white shadow-sm">
            @else
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-{{ $type === 'design' ? 'purple' : 'orange' }}-400 to-{{ $type === 'design' ? 'purple' : 'orange' }}-500 flex items-center justify-center text-white font-medium text-xs sm:text-sm">
                    {{ substr($task->employee_name, 0, 1) }}
                </div>
            @endif
            <div class="mr-2 sm:mr-3 flex-1 min-w-0">
                <h4 class="text-xs sm:text-sm font-semibold text-gray-900 truncate">{{ $task->employee_name }}</h4>
                <p class="text-xs text-gray-500 truncate">{{ $task->department }} - {{ $task->position }}</p>
            </div>
            
            {{-- Action Icons - أيقونات منفصلة بدلاً من منيو --}}
            <div class="flex items-center space-x-1 space-x-reverse">
                {{-- زرار العرض --}}
                <a href="{{ route('admin.operation-system.show', $task->id) }}" 
                   class="p-1.5 rounded-full hover:bg-green-100 text-green-600 hover:text-green-700 transition-colors" 
                   title="عرض التفاصيل">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>

                {{-- زرار التعديل --}}
                <a href="{{ route('admin.operation-system.edit', $task->id) }}" 
                   class="p-1.5 rounded-full hover:bg-blue-100 text-blue-600 hover:text-blue-700 transition-colors" 
                   title="تعديل المهمة">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>

                {{-- زرار الحذف --}}
                <form method="POST" action="{{ route('admin.operation-system.destroy', $task->id) }}" class="inline" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟\n\nسيتم حذف المهمة نهائياً ولا يمكن التراجع عن هذا الإجراء.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="p-1.5 rounded-full hover:bg-red-100 text-red-600 hover:text-red-700 transition-colors" 
                            title="حذف المهمة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Task Description --}}
        @if($task->task_description)
            <div class="mb-3 p-2 sm:p-3 bg-{{ $type === 'design' ? 'purple' : 'orange' }}-50 rounded-lg border border-{{ $type === 'design' ? 'purple' : 'orange' }}-100">
                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">{{ Str::limit($task->task_description, 80) }}</p>
            </div>
        @endif

        {{-- Status Badges --}}
        <div class="flex flex-wrap gap-1 sm:gap-2">
            {{-- Task Type --}}
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $type === 'design' ? 'purple' : 'orange' }}-100 text-{{ $type === 'design' ? 'purple' : 'orange' }}-800">
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($type === 'design')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    @endif
                </svg>
                {{ $type === 'design' ? 'تصميم' : 'تسويق' }}
            </span>

            {{-- Reserved Status --}}
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $task->is_reserved ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($task->is_reserved)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    @endif
                </svg>
                {{ $task->is_reserved ? 'محجوز' : 'حر' }}
            </span>

            {{-- Task Status --}}
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $status['bg'] }} {{ $status['text'] }}">
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($task->status)
                        @case('completed')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @break
                        @case('in_progress')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @break
                        @case('cancelled')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    @endswitch
                </svg>
                {{ $status['label'] }}
            </span>
        </div>
    </div>
</div>
