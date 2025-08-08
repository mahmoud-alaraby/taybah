@extends('admin.layouts.app')

@section('title', 'قائمة المهام')

@section('content')

{{-- زر الإضافة الأحمر أعلى الصفحة --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 flex justify-end">
    <a href="{{ route('admin.designer-task-accounts.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md shadow font-semibold transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة مهمة جديدة
    </a>
</div>

{{-- كاردات اليوم، الأسبوع، الشهر مع جريدينت وألوان متجانسة --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @foreach(['day', 'week', 'month'] as $period)
        @php
            $colors = [
                'day' => 'from-red-500 to-red-600',
                'week' => 'from-green-500 to-green-600',
                'month' => 'from-purple-500 to-purple-600',
            ];
            $names = [
                'day' => 'اليوم',
                'week' => 'الأسبوع',
                'month' => 'الشهر',
            ];
        @endphp
        <div class="bg-gradient-to-r {{ $colors[$period] }} rounded-xl p-6 text-white shadow-lg flex justify-between items-center">
            <div class="text-right space-y-1">
                <h3 class="text-lg font-semibold">{{ $names[$period] }}</h3>
                {{-- تم إزالة التأثير الأزرق حول عدد المهام --}}
                <p class="text-sm">عدد المهام: <span class="font-bold text-xl">{{ $stats[$period]['count'] }}</span></p>
                <p class="text-sm">إجمالي المبالغ: <span class="font-bold text-xl">{{ number_format($stats[$period]['sum'], 2) }} ر.س</span></p>
            </div>
            <div>
                {{-- أيقونات مناسبة لكل فترة --}}
                @if($period == 'day')
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @elseif($period == 'week')
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2" ry="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v2M8 3v2M3 9h18"/>
                    </svg>
                @else {{-- month --}}
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- إحصائيات الموظف مع أفضل تصميم --}}
@if($designer_id)
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 bg-yellow-50 rounded-xl border border-yellow-300 p-6 shadow">
    <h2 class="font-bold text-xl mb-4">إحصائيات الموظف المختار</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach(['day', 'week', 'month'] as $period)
        @php
            $colors = [
                'day' => 'from-indigo-400 to-indigo-500',
                'week' => 'from-pink-400 to-pink-500',
                'month' => 'from-purple-400 to-purple-500',
            ];
            $names = ['day' => 'اليوم', 'week' => 'الأسبوع', 'month' => 'الشهر'];
        @endphp
        
        <div class="bg-gradient-to-r {{ $colors[$period] }} rounded-xl p-4 text-white shadow-md text-center">
            <h4 class="font-semibold text-lg mb-2">{{ $names[$period] }}</h4>
            <p>عدد المهام: <span class="font-bold text-xl">{{ $designerStats[$period]['count'] }}</span></p>
            <p>إجمالي المبالغ: <span class="font-bold text-xl">{{ number_format($designerStats[$period]['sum'], 2) }} ر.س</span></p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- نموذج الفلتر المحسن --}}
<div class="max-w-7xl px-8">
<form method="GET" action="{{ route('admin.designer-task-accounts.index') }}" 
      class=" mx-auto px-4 sm:px-6 lg:px-8 mb-6 flex flex-wrap gap-4 items-center bg-white p-4 rounded-lg border border-gray-300 shadow-sm">

    <select name="designer_id" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">اختر الموظف</option>
        @foreach($designers as $designer)
            <option value="{{ $designer->id }}" @selected(request('designer_id') == $designer->id)>{{ $designer->name }}</option>
        @endforeach
    </select>

    <input type="date" name="date" value="{{ request('date', now()->toDateString()) }}"
           class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />

    <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-md shadow hover:bg-red-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" >
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1011 18.5a7.5 7.5 0 005.65-1.85z"/>
        </svg>
        بحث
    </button>

    <a href="{{ route('admin.designer-task-accounts.index') }}" 
       class="flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md shadow transition cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        إلغاء الفلتر
    </a>
</form>
        </div>
{{-- قائمة المهام مع كولابس محسّن و أيقونات داخل المحتوى --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 mb-12">
    @foreach($tasksGroupedByDate as $date => $accounts)
        @php
            $totalTasksForDate = 0;
            foreach($accounts as $acc){
                for ($i=1; $i<=5; $i++) {
                    if ($acc["task$i"]) $totalTasksForDate++;
                }
            }
        @endphp

        <div x-data="{ open: false }" class="bg-white rounded-xl shadow p-5">
            <button @click="open = !open" 
                    class="flex justify-between items-center w-full text-right text-lg font-bold mb-4 text-gray-800 focus:outline-none   rounded-md">
                {{-- أيقونة التقويم مع النص --}}
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>تاريخ: {{ $date }} - عدد المهام: {{ $totalTasksForDate }}</span>
                </div>
                <svg :class="{'rotate-90': open}" class="w-6 h-6 transition-transform text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div x-show="open" x-transition 
                 class="border-t border-gray-200 pt-4 space-y-6 overflow-hidden">
                @foreach($accounts as $account)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                            <div class="text-gray-700 flex gap-3 items-center">
                                {{-- أيقونة مصمم --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c4 0 6 2 6 4v1H6v-1c0-2 2-4 6-4z"/>
                                </svg>
                                <h4 class="font-semibold text-lg">مصمم: {{ $account->designer->name ?? 'غير محدد' }}</h4>
                            </div>
                            <div class="text-gray-700 flex items-center gap-2">
                                {{-- أيقونة التاريخ --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm">التاريخ: {{ $account->task_date }}</p>
                            </div>
                            <div class="flex gap-4 text-sm text-blue-600 items-center">
                                {{-- أيقونة تعديل --}}
                                <a href="{{ route('admin.designer-task-accounts.edit', $account->id) }}" 
                                   class="hover:text-blue-800 font-semibold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                                    </svg>
                                    تعديل
                                </a>
                                {{-- أيقونة حذف --}}
                                <form method="POST" action="{{ route('admin.designer-task-accounts.destroy', $account->id) }}" 
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hover:text-red-700 font-semibold text-red-600 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4"/>
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- تفاصيل المهام --}}
                        @for($i = 1; $i <= 5; $i++)
                            @if($account["task$i"])
                                <div class="bg-white border border-gray-300 rounded-md p-4 mb-3 shadow-sm">
                                    <h5 class="font-semibold mb-1 flex items-center gap-2">
                                        {{-- أيقونة مهمة --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                                        </svg>
                                        عنوان المهمة: {{ $account["task$i"] }}
                                    </h5>
                                    @if($account["desc$i"])
                                        <p class="text-gray-600 mb-1 whitespace-pre-line flex items-start gap-2">
                                            {{-- أيقونة وصف --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                            </svg>
                                            الوصف: {{ $account["desc$i"] }}
                                        </p>
                                    @endif
                                    <p class="flex items-center gap-2">
                                        {{-- أيقونة السعر --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-3.3 0-6 1.3-6 3s2.7 3 6 3 6-1.3 6-3-2.7-3-6-3z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8"/>
                                        </svg>
                                        السعر: <span class="font-semibold">{{ number_format($account["price$i"] ?? 0, 2) }} ر.س</span>
                                    </p>
                                </div>
                            @endif
                        @endfor
                    </div>
                @endforeach

                @if($totalTasksForDate < 5)
                    <a href="{{ route('admin.designer-task-accounts.create', ['date' => $date]) }}"
                       class="block mt-4 text-center bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-semibold transition">
                        إضافة مهمة جديدة لهذا اليوم
                    </a>
                @else
                    <div class="mt-4 p-3 bg-red-100 text-red-700 border border-red-400 rounded-md text-center font-semibold">
                        لقد قمت بإضافة خمس مهام خلال هذا اليوم والحد الأقصى هو خمسة لكل يوم.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.designer-task-accounts.destroyAllTasksForDay') }}" 
                      onsubmit="return confirm('هل أنت متأكد من حذف جميع المهام لهذا اليوم؟');" 
                      class="mt-4">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md font-semibold transition flex justify-center items-center gap-2">
                        {{-- أيقونة حذف الجميع --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4"/>
                        </svg>
                        حذف جميع مهام هذا اليوم
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    @if($accountsPaginated->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $accountsPaginated->links() }}
        </div>
    @endif
</div>

<script src="//unpkg.com/alpinejs" defer></script>

@endsection
