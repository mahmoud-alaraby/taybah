@extends('admin.layouts.app')
@section('title', 'تقارير العمل')

@section('content')
<div class="p-6 space-y-6">

    {{-- فلاتر --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-6 rounded-lg shadow-md border border-gray-200">
        <div class="flex flex-col" style="min-width: 150px;">
            <label class="block text-xs font-semibold text-gray-700 mb-2">نوع التقرير</label>
            <select name="type" 
                class="border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition h-12">
                <option value="daily" {{ request('type')=='daily' ? 'selected':'' }}>يومي</option>
                <option value="weekly" {{ request('type')=='weekly' ? 'selected':'' }}>أسبوعي</option>
                <option value="monthly" {{ request('type')=='monthly' ? 'selected':'' }}>شهري</option>
            </select>
        </div>

        <div class="flex flex-col" style="min-width: 150px;">
            <label class="block text-xs font-semibold text-gray-700 mb-2">التاريخ</label>
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" 
                   class="border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition h-12" />
        </div>

        <div class="flex flex-col" style="min-width: 200px;">
            <label class="block text-xs font-semibold text-gray-700 mb-2">الموظف</label>
            <select name="employee_id" 
                class="border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition h-12">
                <option value="">الكل</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected':'' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col" style="min-width: 200px;">
            <label class="block text-xs font-semibold text-gray-700 mb-2">المشروع</label>
            <select name="project_id" 
                class="border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition h-12">
                <option value="">الكل</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id')==$proj->id ? 'selected':'' }}>
                        {{ $proj->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" 
            class="bg-red-600 text-white px-4 py-3 rounded-lg shadow hover:bg-red-700 transition h-12">
            عرض
        </button>

        <a href="{{ route('admin.work-reports.index') }}" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg shadow hover:bg-gray-400 transition h-12 flex items-center justify-center">
            إعادة ضبط
        </a>

        <button type="button" onclick="window.print()" 
                class="ml-auto bg-green-600 text-white px-4 py-3 rounded-lg shadow hover:bg-green-700 transition flex items-center gap-2 h-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2M7 17H5a2 2 0 01-2-2v-4a2 2 0 012-2h2m10-4H7a2 2 0 00-2 2v3h14v-3a2 2 0 00-2-2z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 21h14a2 2 0 002-2v-2H3v2a2 2 0 002 2z" />
            </svg>
            طباعة
        </button>
    </form>

<div id="reportArea" class="grid gap-6 md:grid-cols-2">
    @forelse($data as $empData)
        @php
            if ($type == 'daily') {
                $target = 7;
                $total = $empData['projects']->flatMap->tasks->sum('hours');
            } elseif ($type == 'weekly') {
                $target = 42;
                $total = $empData['total_hours'];
            } else {
                $target = 182;
                $total = $empData['total_hours'];
            }
            $achievement = $target > 0 ? round(($total / $target) * 100, 1) : 0;
            $overtime = max(0, $total - $target);
        @endphp

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition">
            
            {{-- رأس الكارد --}}
            <div class="flex items-center gap-4 mb-5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-11 w-11 text-purple-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A5.002 5.002 0 0112 15a5.002 5.002 0 016.879 2.804M12 11a3 3 0 100-6 3 3 0 000 6z" />
</svg>

                <h3 class="text-lg font-semibold text-gray-800">{{ $empData['employee']->name ?? 'موظف غير محدد' }}</h3>
            </div>

            {{-- بلوكات الملخص --}}
            <div class="space-y-3 text-sm">

                <div class="flex items-center p-3 bg-orange-50 border-l-4 border-orange-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold mr-1">التارجيت:</span> {{ $target }} ساعات
                </div>

                <div class="flex items-center p-3 bg-blue-50 border-l-4 border-blue-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-semibold mr-1">إجمالي ساعات العمل:</span> {{ $total }}
                </div>

                <div class="flex items-center p-3 bg-green-50 border-l-4 border-green-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold mr-1">نسبة الإنجاز:</span> {{ $achievement }}%
                </div>

                <div class="flex items-center p-3 bg-red-50 border-l-4 border-red-400 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold mr-1">الساعات الإضافية:</span> {{ $overtime }}
                </div>
            </div>

            {{-- عنوان المهام --}}
            <div class="flex items-center gap-2 mt-6 mb-3  pl-3 font-semibold text-purple-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2v-1M9 11h6" />
                </svg>
                المشاريع والمهام
            </div>

            {{-- تفاصيل المشاريع والمهام --}}
            @if($type == 'daily')
                @foreach($empData['projects'] as $project)
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-purple-600 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                            </svg>
                            {{ $project['project']->name ?? 'بدون مشروع' }}
                        </h4>
                        @foreach($project['tasks'] as $task)
                            <div class="pl-5 border-l-4 border-gray-200 mb-3  ">
                                <h4 class="text-gray-800 font-medium flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 ml-2 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7 16h10M7 8h10M7 4h10" />
</svg>

                                 {{ $task['task']->name ?? 'بدون مهمة' }}
        </h4>
                                <div class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $task['hours'] }} ساعات
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                                        </svg>
                                        {{ $task['achievement'] }}%
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                                        </svg>
                                        {{ $task['overtime'] }} إضافي
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                @foreach($empData['tasks'] as $task)
                    <div class="text-sm mt-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        </svg>
                        {{ $task['task']->name ?? 'بدون مهمة' }} - {{ $task['hours'] }} ساعة
                    </div>
                @endforeach
            @endif

        </div>
    @empty
        <p class="col-span-full text-center text-gray-500 mt-10 font-medium">لا توجد بيانات</p>
    @endforelse
</div>



</div>
@endsection

@push('styles')
<style>
@media print {
    form, nav, header, footer { display: none !important; }
    #reportArea { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
@endpush
