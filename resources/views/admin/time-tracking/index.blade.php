@extends('admin.layouts.app')

@section('title', 'متابعة أوقات العمل')
@section('page-title', 'متابعة أوقات العمل')
@section('page-subtitle', 'متابعة ومراقبة أوقات عمل الموظفين وجلسات العمل')

@section('content')
<div class="space-y-6">
    <!-- فلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الموظف</label>
                <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع الموظفين</option>
                   @foreach($users as $user)
    <option value="{{ $user->id }}" {{ $employeeId == $user->id ? 'selected' : '' }}>
        {{ $user->name }} @if($user->type == 'admin') (أدمن) @endif
    </option>
@endforeach

                </select>
                
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المشروع</label>
                <select name="project_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 bg-red-600 text-white rounded-md py-2 hover:bg-red-700">
                    <i class="fas fa-search ml-1"></i>
                    عرض
                </button>
                <!-- <a href="{{ route('admin.time-tracking.reports') }}" 
                   class="bg-green-600 text-white rounded-md py-2 px-4 hover:bg-green-700">
                    <i class="fas fa-chart-line"></i>
                </a> -->
            </div>
            <div>
                <!-- <a href="{{ route('admin.time-tracking.daily-summary', ['date' => $date]) }}" 
                   class="w-full bg-purple-600 text-white rounded-md py-2 hover:bg-purple-700 flex items-center justify-center">
                    <i class="fas fa-calendar-day ml-1"></i>
                    ملخص اليوم
                </a> -->
            </div>
        </form>
    </div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- إجمالي الساعات -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي الساعات</p>
                    <p class="text-2xl font-bold">{{ number_format($dailyStats['total_hours'], 1) }}</p>
                </div>
                <i class="fas fa-clock text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- جلسات نشطة -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">جلسات نشطة</p>
                    <p class="text-2xl font-bold">{{ $dailyStats['active_sessions'] }}</p>
                </div>
                <i class="fas fa-play text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- موظفين يعملون -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">موظفين يعملون</p>
                    <p class="text-2xl font-bold">{{ $dailyStats['employees_working'] }}</p>
                </div>
                <i class="fas fa-users text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- مشاريع نشطة -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">مشاريع نشطة</p>
                    <p class="text-2xl font-bold">{{ $dailyStats['projects_active'] }}</p>
                </div>
                <i class="fas fa-project-diagram text-2xl opacity-80"></i>
            </div>
        </div>
    </div>
</div>


    <!-- جدول أوقات العمل -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                سجل أوقات العمل - {{ Carbon\Carbon::parse($date)->format('Y-m-d') }}
            </h3>
            <div class="text-sm text-gray-500">
                {{ $timeEntries->count() }} إدخال
            </div>
        </div>
        
        @if($timeEntries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المشروع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المهمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت البداية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وقت النهاية</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($timeEntries as $entry)
                            <tr class="hover:bg-gray-50 {{ $entry->is_active ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-700">
                                                 {{ mb_substr(optional($entry->user)->name ?? 'غير معروف', 0, 1) }}

                                                </span>
                                            </div>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900">{{ optional($entry->user)->name ?? 'غير معروف' }}
</div>
                                            <div class="text-sm text-gray-500">{{ optional($entry->user)->employee_id ?? '' }}
</div>
                                        </div>
                                    </div>
                                </td>
                             <td class="px-6 py-4 whitespace-nowrap">
    <div class="text-sm font-medium text-gray-900">
        {{ $entry->project->name ?? 'بدون مشروع' }}
    </div>

    @if(isset($entry->project) && $entry->project->client_name)
        <div class="text-sm text-gray-500">{{ $entry->project->client_name }}</div>
    @endif
</td>
<td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900">{{ $entry->task?->name ?? 'بدون مهمة' }}</div>

</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    {{ $entry->start_time->format('H:i:s') }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    {{ $entry->end_time ? $entry->end_time->format('H:i:s') : '-' }}
</td>
<td class="px-6 py-4 whitespace-nowrap">
    <div class="text-sm font-medium text-gray-900">{{ $entry->formatted_duration }}</div>
    <div class="text-sm text-gray-500">{{ number_format($entry->hours, 2) }} ساعة</div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
    @if($entry->is_active)
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 animate-pulse">
            <i class="fas fa-circle ml-1 text-xs"></i>
            نشط
        </span>
    @else
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            <i class="fas fa-check ml-1"></i>
            مكتمل
        </span>
    @endif
</td>
<td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
    {{ Str::limit($entry->description, 50) }}
</td>

                           </tr>
                       @endforeach
                   </tbody>
               </table>
           </div>
       @else
           <div class="text-center py-12">
               <i class="fas fa-clock text-6xl text-gray-400 mb-4"></i>
               <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد سجل أوقات</h3>
               <p class="text-sm text-gray-500">لم يتم تسجيل أي أوقات عمل في هذا التاريخ</p>
           </div>
       @endif
   </div>
</div>
@endsection