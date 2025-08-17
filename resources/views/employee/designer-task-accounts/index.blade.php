@extends('employee.layouts.app')

@section('title', 'مهامي')

@section('content')

{{-- كاردات اليوم، الأسبوع، الشهر --}}
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
                <p class="text-sm">عدد المهام: <span class="font-bold text-xl">{{ $stats[$period]['count'] }}</span></p>
                <p class="text-sm">إجمالي المبالغ: <span class="font-bold text-xl">{{ number_format($stats[$period]['sum'], 2) }} ر.س</span></p>
            </div>
            <div>
                @if($period == 'day')
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @elseif($period == 'week')
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2" ry="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v2M8 3v2M3 9h18"/>
                    </svg>
                @else
                    <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- نموذج الفلتر --}}
<div class="max-w-7xl px-8">
<form method="GET" action=""
      class="mx-auto px-4 sm:px-6 lg:px-8 mb-6 flex flex-wrap gap-4 items-center bg-white p-4 rounded-lg border border-gray-300 shadow-sm">
    <input type="date" name="date" value="{{ old('date', $filterDate ?? now()->toDateString()) }}"
           class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" />

    <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-md shadow hover:bg-red-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" >
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1011 18.5a7.5 7.5 0 005.65-1.85z"/>
        </svg>
        بحث
    </button>

    <a href="{{ url()->current() }}"
       class="flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md shadow transition cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        إلغاء الفلتر
    </a>
</form>
</div>

{{-- عرض المهام بشكل كولابس --}}

@if($tasksToday->isEmpty())
    <p class="text-center text-gray-600">ليس لديك أي مهام خلال هذا اليوم.</p>
@else

    <div class="space-y-6">
    @foreach($tasksToday as $account)
        {{-- كولابس المهمة --}}
        <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm max-w-6xl mx-auto">
            <button @click="open = !open"
                class="w-full flex justify-between items-center text-right text-lg font-bold mb-4 text-gray-800 focus:outline-none">
                <div class="flex gap-3 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c4 0 6 2 6 4v1H6v-1c0-2 2-4 6-4z"/>
                    </svg>
                    الموظف:
              
                                                 <h4 class="font-semibold text-lg">: {{ $account->designer->name ?? 'غير محدد' }}</h4>

                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center text-gray-500 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        التاريخ: {{ $account->task_date }}
                    </span>
                    @if($account->admin)
                        <span class="flex items-center text-gray-500 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c4 0 6 2 6 4v1H6v-1c0-2 2-4 6-4z"/>
                            </svg>
                            تمت الإضافة بواسطة: {{ $account->admin->name }}
                        </span>
                    @endif
                    {{-- سهم الكولابس --}}
                    <svg :class="{'rotate-90': open}" class="w-6 h-6 transition-transform text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>
            <div x-show="open" x-transition class="border-t border-gray-200 pt-4 space-y-6 overflow-hidden">
                @for($i = 1; $i <= 5; $i++)
                    @if($account["task$i"])
                        <div class="bg-white border border-gray-300 rounded-md p-4 mb-3 shadow-sm">
                            <h5 class="font-semibold mb-1 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                                </svg>
                                عنوان المهمة: {{ $account["task$i"] }}
                            </h5>
                            @if($account["desc$i"])
                                <p class="text-gray-600 mb-1 whitespace-pre-line flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                                    </svg>
                                    الوصف: {{ $account["desc$i"] }}
                                </p>
                            @endif
                            <p class="flex items-center gap-2">
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
        </div>
    @endforeach
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{ $tasksToday->links() }}
    </div>
@endif

{{-- لازم يكون alpinejs موجود --}}
<script src="//unpkg.com/alpinejs" defer></script>

@endsection
