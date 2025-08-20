@extends('employee.layouts.app')

@section('title', 'تقارير العمل')
@section('page-title', 'تقارير العمل')
@section('page-subtitle', 'التقارير اليومية والأسبوعية والشهرية لساعات العمل')

@section('content')
<div class="space-y-6">
    <!-- نوع التقرير والفلاتر -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التقرير</label>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="daily" {{ request('type', 'daily') == 'daily' ? 'selected' : '' }}>يومي</option>
                    <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                    <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                    <option value="project" {{ request('type') == 'project' ? 'selected' : '' }}>حسب المشروع</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ/الفترة</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <button type="submit" class="w-full bg-red-600 text-white rounded-md py-2 hover:bg-red-700">
                    <i class="fas fa-search ml-1"></i>
                    عرض التقرير
                </button>
            </div>
            <div>
                <a href="{{ route('employee.work-reports.print', request()->query()) }}" target="_blank"
                   class="w-full bg-green-600 text-white rounded-md py-2 hover:bg-green-700 flex items-center justify-center">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </a>
            </div>
            <div>
                <a href="{{ route('employee.project-tracking.index') }}"
                   class="w-full bg-gray-600 text-white rounded-md py-2 hover:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-arrow-right ml-1"></i>
                    العودة
                </a>
            </div>
        </form>
    </div>

    <!-- محتوى التقرير سيتم تحميله بـ AJAX -->
    <div id="report-content">
        <!-- سيتم ملء هذا القسم بمحتوى التقرير -->
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadReport();
    
    // تحديث التقرير عند تغيير النوع
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        loadReport();
    });
});

// دالة لتحويل الوقت العشري إلى "ساعات ودقائق"
function formatHoursDecimalToHM(hoursDecimal) {
    const totalMinutes = Math.round(hoursDecimal * 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${hours}:${minutes.toString().padStart(2, '0')}`;
}


async function loadReport() {
    const type = document.querySelector('select[name="type"]').value;
    const date = document.querySelector('input[name="date"]').value;

    try {
        const response = await fetch(`{{ route('employee.project-tracking.reports') }}?type=${type}&date=${date}`);
        const data = await response.json();

        displayReport(data);
    } catch (error) {
        console.error('Error loading report:', error);
    }
}


function displayReport(data) {
    const container = document.getElementById('report-content');

    switch(data.type) {
        case 'daily':
            container.innerHTML = renderDailyReport(data);
            break;
        case 'weekly':
            container.innerHTML = renderWeeklyReport(data);
            break;
        case 'monthly':
            container.innerHTML = renderMonthlyReport(data);
            break;
        default:
            container.innerHTML = '<div class="text-center py-8 text-gray-500">اختر نوع التقرير</div>';
    }
}

function renderDailyReport(data) {
    return `
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">التقرير اليومي - ${data.date}</h3>
            
            ${data.summary ? `
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg text-center">
                        <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.summary.total_work_hours)}</div>
                        <div class="text-sm opacity-90">ساعات العمل</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg text-center">
                        <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.summary.overtime_hours)}</div>
                        <div class="text-sm opacity-90">ساعات إضافية</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg text-center">
                        <div class="text-2xl font-bold">${data.summary.daily_target_percentage}%</div>
                        <div class="text-sm opacity-90">نسبة التارجت</div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg text-center">
                        <div class="text-2xl font-bold">${data.summary.projects_worked ? data.summary.projects_worked.length : 0}</div>
                        <div class="text-sm opacity-90">مشاريع</div>
                    </div>
                </div>
                
                ${data.summary.projects_worked && data.summary.projects_worked.length > 0 ? `
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 mb-2">المشاريع المنجزة:</h4>
                        ${data.summary.projects_worked.map(project => `
                            <div class="border border-gray-300 rounded-2xl rounded p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h5 class="font-semibold flex items-center"> 
                                    <svg width="30" height="30" class="ml-2" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                           <linearGradient id="gradProjects" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                               <stop offset="0%" stop-color="#4A90E2"/>
                                               <stop offset="100%" stop-color="#50E3C2"/>
                                           </linearGradient>
                                        </defs>
                                        <rect x="8" y="12" width="48" height="40" rx="6" fill="url(#gradProjects)"/>
                                        <path d="M16 28H48" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M16 36H48" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                        <path d="M16 44H32" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                    </svg>
                                    ${project.project_name}</h5>
                                    <span class="text-sm text-gray-600">${formatHoursDecimalToHM(project.hours)}</span>
                                </div>
                                <div class="space-y-1">
                                    ${project.tasks.map(task => `
                                        <div class="flex justify-between text-sm text-gray-700">
                                            <span class="flex items-center"> 
                                            <svg width="25" height="25" class="ml-2" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <defs>
                                                   <linearGradient id="gradTasks" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                                       <stop offset="0%" stop-color="#F5A623"/>
                                                       <stop offset="100%" stop-color="#F8E71C"/>
                                                   </linearGradient>
                                                </defs>
                                                <rect x="12" y="16" width="40" height="32" rx="4" fill="url(#gradTasks)"/>
                                                <path d="M20 28L28 36L44 20" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            ${task.task_name}
                                            </span>
                                            <span>${formatHoursDecimalToHM(task.hours)}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            ` : `
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-calendar-times text-5xl mb-4"></i>
                    <p class="text-lg">لا توجد بيانات لهذا اليوم</p>
                </div>
            `}
        </div>
    `;
}

function renderWeeklyReport(data) {
    return `
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">التقرير الأسبوعي - ${data.period}</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-6 text-center">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.weekly_stats.total_hours)}</div>
                    <div class="text-sm opacity-90">إجمالي الساعات</div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.weekly_stats.overtime_hours)}</div>
                    <div class="text-sm opacity-90">ساعات إضافية</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.weekly_stats.average_daily_hours)}</div>
                    <div class="text-sm opacity-90">متوسط يومي</div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${data.weekly_stats.days_worked}</div>
                    <div class="text-sm opacity-90">أيام العمل</div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${data.weekly_stats.target_achievement}%</div>
                    <div class="text-sm opacity-90">تحقق التارجت</div>
                </div>
            </div>
            
            ${data.daily_summaries && data.daily_summaries.length > 0 ? `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">التاريخ</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">ساعات العمل</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">ساعات إضافية</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">نسبة التارجت</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            ${data.daily_summaries.map(day => `
                                <tr>
                                    <td class="px-4 py-2 text-sm">${day.date}</td>
                                    <td class="px-4 py-2 text-sm">${formatHoursDecimalToHM(day.total_work_hours)}</td>
                                    <td class="px-4 py-2 text-sm">${formatHoursDecimalToHM(day.overtime_hours)}</td>
                                    <td class="px-4 py-2 text-sm">${day.daily_target_percentage}%</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            ` : ''}
        </div>
    `;
}

function renderMonthlyReport(data) {
    return `
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">التقرير الشهري - ${data.period}</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-4 mb-6 text-center">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.monthly_stats.total_hours)}</div>
                    <div class="text-sm opacity-90">إجمالي الساعات</div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.monthly_stats.overtime_hours)}</div>
                    <div class="text-sm opacity-90">ساعات إضافية</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${data.monthly_stats.days_worked}</div>
                    <div class="text-sm opacity-90">أيام العمل</div>
                </div>
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${data.monthly_stats.on_time_days}</div>
                    <div class="text-sm opacity-90">أيام في الموعد</div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${data.monthly_stats.punctuality_percentage}%</div>
                    <div class="text-sm opacity-90">نسبة الالتزام</div>
                </div>
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="text-2xl font-bold">${formatHoursDecimalToHM(data.monthly_stats.average_daily_hours)}</div>
                    <div class="text-sm opacity-90">متوسط يومي</div>
                </div>
            </div>
            
            <!-- رسم بياني للأداء الشهري -->
            <div class="mb-6">
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>
            
            ${data.daily_summaries && data.daily_summaries.length > 0 ? `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">التاريخ</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">ساعات العمل</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">ساعات إضافية</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">نسبة التارجت</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">المشاريع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            ${data.daily_summaries.map(day => `
                                <tr>
                                    <td class="px-4 py-2 text-sm">${day.date}</td>
                                    <td class="px-4 py-2 text-sm">${formatHoursDecimalToHM(day.total_work_hours)}</td>
                                    <td class="px-4 py-2 text-sm">${formatHoursDecimalToHM(day.overtime_hours)}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <div class="flex items-center">
                                            <span class="mr-2">${day.daily_target_percentage}%</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${Math.min(100, day.daily_target_percentage)}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-sm">${day.projects_worked ? day.projects_worked.length : 0}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            ` : ''}
        </div>
    `;
}
</script>
@endpush



@endsection