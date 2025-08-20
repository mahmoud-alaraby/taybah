@extends('admin.layouts.app')

@section('title', 'إدارة المشاريع')
@section('page-title', 'إدارة المشاريع')
@section('page-subtitle', 'متابعة وإدارة جميع المشاريع والمهام')

@section('content')
<div class="space-y-6">
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- إجمالي المشاريع -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي المشاريع</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-project-diagram text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- المشاريع النشطة -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">المشاريع النشطة</p>
                    <p class="text-2xl font-bold">{{ $stats['active'] }}</p>
                </div>
                <i class="fas fa-play text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- المشاريع المكتملة -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">المشاريع المكتملة</p>
                    <p class="text-2xl font-bold">{{ $stats['completed'] }}</p>
                </div>
                <i class="fas fa-check text-2xl opacity-80"></i>
            </div>
        </div>

        <!-- إجمالي الساعات -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي الساعات</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_hours'], 0) }}</p>
                </div>
                <i class="fas fa-clock text-2xl opacity-80"></i>
            </div>
        </div>
    </div>
</div>


    <!-- أزرار الإجراءات -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('admin.projects.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                <i class="fas fa-plus ml-2"></i>
                مشروع جديد
            </a>
            <!-- <a href="{{ route('admin.projects.reports') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                <i class="fas fa-chart-line ml-2"></i>
                التقارير
            </a> -->
        </div>
    </div>

 <!-- فلاتر البحث -->
<div class="bg-white shadow rounded-lg p-6">
    <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="البحث بالاسم أو العميل..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                          focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
            <select name="status" 
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm 
                           focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>
        <div class="flex justify-end items-end space-x-2 space-x-reverse">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                <i class="fas fa-search ml-1"></i> بحث
            </button>
            <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                <i class="fas fa-times ml-1"></i> إلغاء
            </a>
        </div>
    </form>
</div>


@php
    // دالة تحويل القيمة العشرية للساعات إلى صيغة hh:mm (ساعات:دقائق)
    function formatHoursMinutes($hoursFloat) {
        $totalMinutes = round($hoursFloat * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
@endphp

<!-- قائمة المشاريع -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
            @foreach($projects as $project)
                <div class="border bg-white py-6 border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow relative shadow-lg" >
                    <!-- رأس المشروع -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center space-x-2 rtl:space-x-reverse">
                                <!-- أيقونة المشروع -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                                <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-red-600">
                                    {{ $project->name }}
                                </a>
                            </h3>
                            @if($project->client_name)
                                <p class="text-sm text-gray-600 flex items-center space-x-1 rtl:space-x-reverse">
                                    <!-- أيقونة العميل -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.88 0 5.54.993 7.877 2.655m-6.12-6.121a4 4 0 118.486 5.131L19.36 17.3a1.375 1.375 0 01-1.97 0L14.757 16.03z"/>
                                    </svg>
                                    <span>العميل: {{ $project->client_name }}</span>
                                </p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                            {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                              ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                              ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                            {{ $project->status === 'active' ? 'نشط' : 
                              ($project->status === 'completed' ? 'مكتمل' :
                              ($project->status === 'on_hold' ? 'معلق' : 'ملغي')) }}
                        </span>
                    </div>

                    <!-- إحصائيات المشروع -->
                    <div class="grid grid-cols-2 gap-4 mb-4 text-sm ">
                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            <!-- أيقونة المهام -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            </svg>
                            <span class="text-gray-500">المهام:</span>
                            <span class="font-medium text-gray-900">{{ $project->tasks->count() }}</span>
                        </div>
                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            <!-- أيقونة الساعات -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            </svg>
                            <span class="text-gray-500">الساعات:</span>
                            <span class="font-medium text-gray-900">{{ formatHoursMinutes($project->total_hours) }}</span>
                        </div>
                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            <!-- أيقونة التقدم -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                            </svg>
                            <span class="text-gray-500">التقدم:</span>
                            <span class="font-medium text-gray-900">{{ $project->completion_percentage }}%</span>
                        </div>
                        <div class="flex items-center space-x-1 rtl:space-x-reverse">
                            <!-- أيقونة التاريخ -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-5H3v5a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-500">التاريخ:</span>
                            <span class="font-medium text-gray-900">{{ $project->start_date->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <!-- شريط التقدم -->
                    <div class="mb-5">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>نسبة الإنجاز</span>
                            <span>{{ $project->completion_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $project->completion_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center space-x-1 rtl:space-x-reverse" title="عرض المشروع">
                                <i class="fas fa-eye"></i>
                                <span>عرض</span>
                            </a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-green-600 hover:text-green-800 text-sm flex items-center space-x-1 rtl:space-x-reverse" title="تعديل المشروع">
                                <i class="fas fa-edit"></i>
                                <span>تعديل</span>
                            </a>
                            <button onclick="deleteProject({{ $project->id }})" class="text-red-600 hover:text-red-800 text-sm flex items-center space-x-1 rtl:space-x-reverse" title="حذف المشروع">
                                <i class="fas fa-trash"></i>
                                <span>حذف</span>
                            </button>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $project->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $projects->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-project-diagram text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مشاريع</h3>
            <p class="text-sm text-gray-500 mb-6">ابدأ بإضافة مشروع جديد للنظام</p>
            <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                مشروع جديد
            </a>
        </div>
    @endif
</div>

</div>

@push('scripts')
<script>
function deleteProject(projectId) {
    confirmDelete('حذف المشروع', 'هل أنت متأكد من حذف هذا المشروع؟ سيتم حذف جميع المهام والبيانات المرتبطة به!')
        .then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/projects/${projectId}`;
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfField);
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
}
</script>
@endpush
@endsection