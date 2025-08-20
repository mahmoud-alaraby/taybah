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

<!-- ================================================================  -->
<!-- resources/views/employee/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - نظام الموظفين</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/project-tracking.css') }}">
    <script src="{{ asset('js/project-tracking.js') }}"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'cairo': ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        'taiba': {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#dc143c',
                            600: '#b91c1c',
                            700: '#991b1b',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
        }
        [x-cloak] { 
            display: none !important; 
        }
        .fade-out {
            transition: opacity 0.5s ease-in-out;
        }
        .fade-out.hidden {
            opacity: 0;
        }
        .prevent-flash {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-cairo">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
      <aside class="hidden md:flex md:flex-shrink-0 h-screen">
    <div class="flex flex-col w-64 overflow-y-auto" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%)">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4" style="background-color: #dddddd1a;">
            <div class="flex items-center">
                <div class="h-14 flex items-center justify-center ml-2">
                    <img src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة" class="h-14">
                </div>
            </div>
        </div>
        
        <div class="flex flex-col flex-1">
            <!-- Employee Info -->
            <div class="px-3 py-4">
                <div class="mb-6 p-3 rounded-lg" style="background-color: #374151;">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                <span class="text-white font-medium">
                                    {{ mb_substr(auth('employee')->user()->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-white">{{ auth('employee')->user()->name }}</p>
                            <p class="text-xs text-gray-300">{{ auth('employee')->user()->position }}</p>
                            <p class="text-xs text-gray-300">{{ auth('employee')->user()->employee_id }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="space-y-1">
                    <a href="{{ route('employee.dashboard') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.dashboard') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt ml-3 text-sm"></i>
                        الرئيسية
                    </a>

                         @if(auth('employee')->user()->hasPermission('customer_communication'))
                    <a href="{{ route('employee.customer-communication.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-communication*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-calendar-alt ml-3 text-sm"></i>
                      التواصل مع العملاء 
                    </a>

                    <a href="{{ route('employee.customer-communication.index') }}"
   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-communication.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-phone-alt ml-3 text-sm"></i>
    التواصل مع العملاء
</a>
                    @endif
 
                    <a href="{{ route('employee.tasks.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.tasks.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-tasks ml-3 text-sm"></i>
                        قائمة المهام
                    </a>

                    @if(auth('employee')->user()->hasPermission('renewal_dates'))
                    <a href="{{ route('employee.renewal-dates.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.renewal-dates*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-calendar-alt ml-3 text-sm"></i>
                        مواعيد التجديد
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('receipts_payments'))
                    <a href="{{ route('employee.receipts-payments') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.receipts-payments*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-money-bill-wave ml-3 text-sm"></i>
                        المقبوضات والمدفوعات
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('customer_movement'))
                    <a href="{{ route('employee.customer-movement') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-movement') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-users ml-3 text-sm"></i>
                        حركة العملاء
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('potential_customers'))
                    <a href="{{ route('employee.potential-customers') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.potential-customers') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-user-plus ml-3 text-sm"></i>
                        العملاء المحتملين
                    </a>
                    @endif

              

                    @if(auth('employee')->user()->hasPermission('general_operations'))
                    <a href="{{ route('employee.general-operations') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.general-operations') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-cogs ml-3 text-sm"></i>
                        التشغيل العام
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('photography_booking'))
                    <a href="{{ route('employee.photography-booking.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.photography-booking.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-camera ml-3 text-sm"></i>
                        <span class="flex-1">حجوزات التصوير والمونتاج</span>
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('designers_account'))
                    <a href="{{ route('employee.designer-task-accounts.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.designer-task-accounts.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-palette ml-3 text-sm"></i>
                        حساب المصممين
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('customer_response'))
                               <a href="{{ route('employee.customer-response.index') }}"
   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('employee.customer-response.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
   
   <!-- أيقونة جديدة مناسبة بدلاً من svg الفارغ -->
   <i class="fas fa-comments ml-3 text-sm "></i> 
   
   <span class="flex-1">قاموس الردود على العملاء</span>
</a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('task_list'))
                    <a href="{{ route('employee.task-list') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.task-list') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-tasks ml-3 text-sm"></i>
                        قائمة المهام
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('photography_costs'))
                    <a href="{{ route('employee.photography-costs.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.photography-costs') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-dollar-sign ml-3 text-sm"></i>
                        تكاليف التصوير
                    </a>
                    @endif

               

                    @if(auth('employee')->user()->hasPermission('design_follow_up'))
                    <a href="{{ route('employee.design-follow-up') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.design-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-pencil-ruler ml-3 text-sm"></i>
                        متابعة التصميم
                    </a>
                    @endif

                    @if(auth('employee')->user()->hasPermission('montage_follow_up'))
                    <a href="{{ route('employee.montage-follow-up') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.montage-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-video ml-3 text-sm"></i>
                        متابعة المونتاج
                    </a>
                    @endif


                    @if(auth('employee')->user()->hasPermission('project_tracking'))
<a href="{{ route('employee.project-tracking.index') }}" 
   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.project-tracking.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-tasks ml-3 text-sm"></i>
    متابعة المشاريع
</a>
@endif

@if(auth('employee')->user()->hasPermission('attendance_tracking'))
<a href="{{ route('employee.attendance.index') }}" 
   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.attendance.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-fingerprint ml-3 text-sm"></i>
    الحضور والانصراف
</a>
@endif

@if(auth('employee')->user()->hasPermission('work_reports'))
<a href="{{ route('employee.work-reports.index') }}" 
   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.work-reports.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-chart-line ml-3 text-sm"></i>
    تقارير العمل
</a>
@endif


                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <form method="POST" action="{{ route('employee.logout') }}">
                            @csrf
                            <button type="submit" class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-red-600 hover:text-white transition-colors prevent-flash">
                                <i class="fas fa-sign-out-alt ml-3 text-sm"></i>
                                تسجيل خروج
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</aside>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-sm text-gray-600">@yield('page-subtitle', 'مرحباً بك في نظام الموظفين')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- Profile dropdown -->
                        <div class="dropdown-container relative">
                            <button onclick="toggleDropdown()" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-1 prevent-flash">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ mb_substr(auth('employee')->user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down mr-2 text-sm text-gray-400"></i>
                            </button>
                            
                            <div id="profile-dropdown"
                                 class="hidden origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50 prevent-flash">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ auth('employee')->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth('employee')->user()->email }}</p>
                                    <p class="text-xs text-gray-500">{{ auth('employee')->user()->employee_id }}</p>
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الملف الشخصي</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الإعدادات</a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('employee.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        تسجيل خروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div id="success-alert" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md prevent-flash">
                                <div class="flex items-center justify-between">
                                    <div class="flex">
                                        <i class="fas fa-check-circle text-green-400 ml-2 mt-0.5"></i>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                    <button onclick="closeAlert('success-alert')" class="text-green-700 hover:text-green-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div id="error-alert" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md prevent-flash">
                                <div class="flex items-center justify-between">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-circle text-red-400 ml-2 mt-0.5"></i>
                                        <span>{{ session('error') }}</span>
                                    </div>
                                    <button onclick="closeAlert('error-alert')" class="text-red-700 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Global Scripts -->
    <script>
        // CSRF Token
        window.Laravel = { csrfToken: '{{ csrf_token() }}' };
        
        // SweetAlert Delete Confirmation
        function confirmDelete(title = 'هل أنت متأكد؟', text = 'لن تتمكن من التراجع عن هذا الإجراء!') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc143c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            });
        }
        
        // Success Alert
        function showSuccess(message) {
            Swal.fire({
                title: 'تم بنجاح!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#dc143c',
                confirmButtonText: 'موافق'
            });
        }
        
        // Dropdown toggle function
        function toggleDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = document.querySelector('.dropdown-container button');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Close alert function
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }
        }
        
        // Auto hide flash messages
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            if (successAlert) {
                closeAlert('success-alert');
            }
            if (errorAlert) {
                closeAlert('error-alert');
            }
        }, 8000);
        
        // منع أي انيميشن غير مرغوب فيه عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.prevent-flash');
            elements.forEach(element => {
                element.style.opacity = '1';
                element.style.visibility = 'visible';
            });
        });
    </script>
    
    <!-- تحميل Alpine.js في النهاية -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>
-------------------------------------------------------------------------------