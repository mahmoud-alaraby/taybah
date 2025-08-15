@extends('admin.layouts.app')
@section('title', 'تقارير العمل')

@section('content')
<div class="p-6 space-y-6">

    {{-- فلاتر --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end bg-red-50 p-4 rounded-lg shadow border border-red-200">
        <div>
            <label class="block text-xs font-bold text-red-700 mb-1">نوع التقرير</label>
            <select name="type" class="border border-red-300 rounded p-2 text-sm focus:ring-red-500 focus:border-red-500">
                <option value="daily" {{ request('type')=='daily' ? 'selected':'' }}>يومي</option>
                <option value="weekly" {{ request('type')=='weekly' ? 'selected':'' }}>أسبوعي</option>
                <option value="monthly" {{ request('type')=='monthly' ? 'selected':'' }}>شهري</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-red-700 mb-1">التاريخ</label>
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" 
                   class="border border-red-300 rounded p-2 text-sm focus:ring-red-500 focus:border-red-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-red-700 mb-1">الموظف</label>
            <select name="employee_id" class="border border-red-300 rounded p-2 text-sm focus:ring-red-500 focus:border-red-500">
                <option value="">الكل</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected':'' }}>
                        {{ $emp->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-red-700 mb-1">المشروع</label>
            <select name="project_id" class="border border-red-300 rounded p-2 text-sm focus:ring-red-500 focus:border-red-500">
                <option value="">الكل</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id')==$proj->id ? 'selected':'' }}>
                        {{ $proj->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">عرض</button>
        <a href="{{ route('admin.work-reports.index') }}" class="bg-gray-300 px-4 py-2 rounded shadow">إعادة ضبط</a>

        <button type="button" onclick="window.print()" 
                class="ml-auto bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
            <i class="fas fa-print ml-2"></i> طباعة
        </button>
    </form>

    {{-- عرض النتائج --}}
    <div id="reportArea" class="grid gap-4 md:grid-cols-2">
        @forelse($data as $empData)
            <div class="bg-white rounded-lg shadow p-4 border-t-4 border-red-500 hover:shadow-lg transition">

                {{-- رأس الكارت --}}
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-circle text-red-500 text-3xl ml-3"></i>
                    <h3 class="text-lg font-bold">{{ $empData['employee']->name ?? 'موظف غير محدد' }}</h3>
                </div>

                {{-- حساب التارجيت --}}
                @php
                    if($type == 'daily'){
                        $target = 7;
                        $total = $empData['projects']->flatMap->tasks->sum('hours');
                    } elseif($type == 'weekly'){
                        $target = 42;
                        $total = $empData['total_hours'];
                    } else {
                        $target = 182;
                        $total = $empData['total_hours'];
                    }
                    $achievement = $target > 0 ? round(($total / $target) * 100, 1) : 0;
                    $overtime = max(0, $total - $target);
                @endphp

                {{-- بلوك التارجيت --}}
                <div class="bg-red-50 p-3 rounded mb-3 text-sm space-y-1 border border-red-100">
                    <div>🎯 التارجيت: <span class="font-bold text-red-700">{{ $target }} ساعات</span></div>
                    <div>⌛ إجمالي ساعات العمل: <span class="font-bold text-gray-700">{{ $total }}</span></div>
                    <div>📊 نسبة الإنجاز: <span class="font-bold text-green-600">{{ $achievement }}%</span></div>
                    <div>⏱ الساعات الإضافية: <span class="font-bold text-red-600">{{ $overtime }}</span></div>
                </div>

                {{-- تفاصيل المشاريع والمهام --}}
                @if($type == 'daily')
                    @foreach($empData['projects'] as $project)
                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-red-600 mb-1">
                                <i class="fas fa-project-diagram ml-1"></i> {{ $project['project']->name ?? 'بدون مشروع' }}
                            </h4>
                            @foreach($project['tasks'] as $task)
                                <div class="pl-4 border-l-2 border-red-100 mb-1">
                                    <div class="text-gray-800 font-medium text-sm">{{ $task['task']->name ?? 'بدون مهمة' }}</div>
                                    <div class="text-xs text-gray-500">
                                        ⌛ ساعات: {{ $task['hours'] }} | 
                                        🎯 الهدف: {{ $task['achievement'] }}% | 
                                        ⏱ إضافي: {{ $task['overtime'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    @foreach($empData['tasks'] as $task)
                        <div class="text-sm mt-1">
                            <i class="fas fa-tasks text-yellow-500 ml-1"></i> 
                            {{ $task['task']->name ?? 'بدون مهمة' }} - {{ $task['hours'] }} ساعة
                        </div>
                    @endforeach
                @endif
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">لا توجد بيانات</p>
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
