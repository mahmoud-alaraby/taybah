{{-- resources/views/admin/operation-system/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تفاصيل المهمة')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white">تفاصيل المهمة</h1>
                            <p class="text-green-100 text-sm">{{ $task->employee_name }} - {{ Carbon\Carbon::parse($task->task_date)->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <a href="{{ route('admin.operation-system.edit', $task->id) }}" class="inline-flex items-center px-4 py-2 bg-white text-green-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل المهمة
                        </a>
                        <a href="{{ route('admin.operation-system.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-green-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Employee Info Card --}}
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 mb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0">
                @if($task->employee_avatar)
                    <img src="{{ asset('storage/' . $task->employee_avatar) }}" alt="{{ $task->employee_name }}" 
                         class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover ring-4 ring-green-200 shadow-lg">
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                        {{ substr($task->employee_name, 0, 1) }}
                    </div>
                @endif
                <div class="sm:mr-6 flex-1 text-center sm:text-right">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $task->employee_name }}</h2>
                    <p class="text-green-600 font-semibold text-lg mb-1">{{ $task->department }} - {{ $task->position }}</p>
                    @if($task->employee_id)
                        <p class="text-gray-600 text-sm">رقم الموظف: {{ $task->employee_id }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Task Details Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    المعلومات الأساسية
                </h3>
                
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">تاريخ المهمة</span>
                        <span class="text-sm font-semibold text-gray-900 mt-1 sm:mt-0">{{ Carbon\Carbon::parse($task->task_date)->translatedFormat('l، d F Y') }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">نوع المهمة</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 sm:mt-0 {{ $task->task_type === 'design' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($task->task_type === 'design')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                @endif
                            </svg>
                            {{ $task->task_type === 'design' ? 'تصميم' : 'تسويق' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Status Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    حالة المهمة
                </h3>
                
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">الحالة الحالية</span>
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'قيد الانتظار', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'in_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'قيد التنفيذ', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'مكتملة', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'ملغية', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                            $status = $statusConfig[$task->status] ?? $statusConfig['pending'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 sm:mt-0 {{ $status['bg'] }} {{ $status['text'] }}">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $status['icon'] }}"></path>
                            </svg>
                            {{ $status['label'] }}
                        </span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600">حالة الحجز</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1 sm:mt-0 {{ $task->is_reserved ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($task->is_reserved)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                @endif
                            </svg>
                            {{ $task->is_reserved ? 'محجوز' : 'غير محجوز' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Task Description --}}
        @if($task->task_description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    وصف المهمة
                </h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $task->task_description }}</p>
                </div>
            </div>
        @endif

        {{-- Additional Notes --}}
        @if($task->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    ملاحظات إضافية
                </h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $task->notes }}</p>
                </div>
            </div>
        @endif

        {{-- System Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                معلومات النظام
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <span class="block text-sm font-medium text-gray-600 mb-1">تاريخ الإنشاء</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ Carbon\Carbon::parse($task->created_at)->translatedFormat('l، d F Y - h:i A') }}
                    </span>
                </div>
                
                <div class="p-3 bg-gray-50 rounded-lg">
                    <span class="block text-sm font-medium text-gray-600 mb-1">آخر تحديث</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ Carbon\Carbon::parse($task->updated_at)->translatedFormat('l، d F Y - h:i A') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4 sm:space-x-reverse">
                <a href="{{ route('admin.operation-system.edit', $task->id) }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل المهمة
                </a>
                
                <form method="POST" action="{{ route('admin.operation-system.destroy', $task->id) }}" class="inline" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟\n\nسيتم حذف المهمة نهائياً ولا يمكن التراجع عن هذا الإجراء.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        حذف المهمة
                    </button>
                </form>
                
                <a href="{{ route('admin.operation-system.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
