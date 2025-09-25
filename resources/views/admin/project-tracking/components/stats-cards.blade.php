{{-- resources/views/admin/project-tracking/components/stats-cards.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- ساعات اليوم -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-100 p-6 rounded-xl shadow-lg border border-green-200 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="p-3 bg-green-500 rounded-full">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-green-900">ساعات اليوم</h3>
                    <p class="text-sm text-green-600">الهدف: 7 ساعات</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-green-900 mb-1">
                    {{ formatHoursToHoursMinutes($todayStats['total_hours']) }}
                </div>
                <div class="text-sm text-green-700 font-semibold">
                    {{ round($todayStats['target_percentage'], 1) }}%
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-green-700 mb-2">
                <span>التقدم</span>
                <span>{{ round($todayStats['target_percentage'], 1) }}%</span>
            </div>
            <div class="w-full bg-green-200 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-1000 ease-out" 
                     style="width: {{ min(100, $todayStats['target_percentage']) }}%"></div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/50 rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-green-800">{{ $todayStats['projects_worked'] }}</div>
                <div class="text-xs text-green-600">مشاريع اليوم</div>
            </div>
            <div class="bg-white/50 rounded-lg p-3 text-center">
                <div class="text-lg font-bold text-green-800">{{ round(7 - $todayStats['total_hours'], 1) }}</div>
                <div class="text-xs text-green-600">ساعات متبقية</div>
            </div>
        </div>
    </div>

    <!-- المشاريع النشطة -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-100 p-6 rounded-xl shadow-lg border border-purple-200 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <div class="p-3 bg-purple-500 rounded-full">
                    <i class="fas fa-project-diagram text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-purple-900">المشاريع</h3>
                    <p class="text-sm text-purple-600">المشاريع النشطة</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-purple-900 mb-1">{{ $activeProjects->count() }}</div>
                <div class="text-sm text-purple-700 font-semibold">مشروع</div>
            </div>
        </div>

        <!-- Projects Overview -->
        @if($activeProjects->count() > 0)
            <div class="space-y-2">
                @foreach($activeProjects->take(3) as $project)
                    <div class="bg-white/50 rounded-lg p-3 flex justify-between items-center">
                        <div>
                            <div class="text-sm font-semibold text-purple-800 truncate">{{ Str::limit($project->name, 20) }}</div>
                            <div class="text-xs text-purple-600">{{ $project->tasks->count() }} مهمة</div>
                        </div>
                        <div class="text-right">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ $project->tasks->count() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($activeProjects->count() > 3)
                    <div class="text-center text-xs text-purple-600 bg-purple-100 py-2 rounded-lg">
                        و {{ $activeProjects->count() - 3 }} مشاريع أخرى...
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open text-purple-300 text-3xl mb-2"></i>
                <p class="text-purple-600">لا توجد مشاريع نشطة</p>
            </div>
        @endif
    </div>
</div>