<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير المشاريع - {{ $employee->name }} - {{ $startDate }} إلى {{ $endDate }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'cairo': ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
        }
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            body { font-size: 12px; }
        }
        .header-logo {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }
    </style>
</head>
<body class="bg-white font-cairo">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="header-logo text-white p-6 rounded-lg mb-6 text-center">
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة" class="h-16 ml-4">
                <div>
                    <h1 class="text-2xl font-bold">شركة طيبة للتسويق الإلكتروني</h1>
                    <p class="text-sm opacity-90">تقرير المشاريع وساعات العمل</p>
                </div>
            </div>
        </div>

        <!-- معلومات التقرير -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-2">معلومات الموظف</h3>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium">الاسم:</span> {{ $employee->name }}</p>
                    <p><span class="font-medium">الرقم الوظيفي:</span> {{ $employee->employee_id }}</p>
                    <p><span class="font-medium">المنصب:</span> {{ $employee->position }}</p>
                    <p><span class="font-medium">البريد الإلكتروني:</span> {{ $employee->email }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-2">معلومات التقرير</h3>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium">الفترة:</span> {{ $startDate }} إلى {{ $endDate }}</p>
                    <p><span class="font-medium">تاريخ الإنشاء:</span> {{ now()->format('Y-m-d H:i') }}</p>
                    <p><span class="font-medium">نوع التقرير:</span> تقرير المشاريع</p>
                    <p><span class="font-medium">عدد المشاريع:</span> {{ $projectBreakdown->count() }}</p>
                </div>
            </div>
        </div>

        <!-- الإحصائيات الإجمالية -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">الإحصائيات الإجمالية</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($timeEntries->sum('hours'), 2) }}</div>
                    <div class="text-sm text-blue-700">إجمالي الساعات</div>
                </div>
                
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $projectBreakdown->count() }}</div>
                    <div class="text-sm text-green-700">عدد المشاريع</div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $timeEntries->count() }}</div>
                    <div class="text-sm text-purple-700">عدد جلسات العمل</div>
                </div>
            </div>
        </div>

        <!-- تفاصيل المشاريع -->
        @if($projectBreakdown->count() > 0)
        @foreach($projectBreakdown as $breakdown)
        <div class="mb-6 print-break">
            <div class="bg-white border border-gray-300 rounded-lg">
                <!-- عنوان المشروع -->
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-300">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            🏗️ {{ $breakdown['project']->name ?? 'مشروع غير محدد' }}
                        </h3>
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ number_format($breakdown['total_hours'], 2) }} ساعة
                            </span>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $breakdown['sessions_count'] }} جلسة
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- إحصائيات المشروع -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-xl font-bold text-blue-600">{{ number_format($breakdown['total_hours'], 2) }}</div>
                            <div class="text-sm text-gray-600">إجمالي الساعات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600">{{ $breakdown['sessions_count'] }}</div>
                            <div class="text-sm text-gray-600">عدد الجلسات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-purple-600">
                                {{ $breakdown['sessions_count'] > 0 ? number_format($breakdown['total_hours'] / $breakdown['sessions_count'], 2) : 0 }}
                            </div>
                            <div class="text-sm text-gray-600">متوسط ساعات/جلسة</div>
                        </div>
                    </div>

                    <!-- المهام -->
                    @if($breakdown['tasks']->count() > 0)
                    <div>
                        <h4 class="font-medium text-gray-800 mb-4">المهام ({{ $breakdown['tasks']->count() }})</h4>
                        
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">اسم المهمة</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">الساعات</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">الجلسات</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">متوسط/جلسة</th>
                                    <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">النسبة من المشروع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($breakdown['tasks'] as $task)
                                @php
                                    $taskPercentage = $breakdown['total_hours'] > 0 
                                        ? ($task['hours'] / $breakdown['total_hours']) * 100 
                                        : 0;
                                @endphp
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="border border-gray-300 px-4 py-2 text-sm">
                                        ✅ {{ $task['task']->name ?? 'مهمة غير محددة' }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-medium">
                                        {{ number_format($task['hours'], 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm">
                                        {{ $task['sessions'] }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm">
                                        {{ $task['sessions'] > 0 ? number_format($task['hours'] / $task['sessions'], 2) : 0 }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm">
                                        {{ number_format($taskPercentage, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-200">
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-bold">المجموع</td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                        {{ number_format($breakdown['total_hours'], 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                        {{ $breakdown['sessions_count'] }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                        {{ $breakdown['sessions_count'] > 0 ? number_format($breakdown['total_hours'] / $breakdown['sessions_count'], 2) : 0 }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-sm font-bold">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <!-- ملخص توزيع الوقت -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">ملخص توزيع الوقت على المشاريع</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">اسم المشروع</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">الساعات</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">النسبة من الإجمالي</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">عدد المهام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalHours = $timeEntries->sum('hours'); @endphp
                        @foreach($projectBreakdown as $breakdown)
                        @php
                            $percentage = $totalHours > 0 ? ($breakdown['total_hours'] / $totalHours) * 100 : 0;
                        @endphp
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ $breakdown['project']->name ?? 'مشروع غير محدد' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-medium">
                                {{ number_format($breakdown['total_hours'], 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ number_format($percentage, 1) }}%
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ $breakdown['tasks']->count() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-200">
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">المجموع</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ number_format($totalHours, 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">100%</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ $projectBreakdown->sum(function($p) { return $p['tasks']->count(); }) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        @if($projectBreakdown->count() == 0)
        <div class="bg-gray-50 p-12 rounded-lg text-center">
            <div class="text-gray-400 text-4xl mb-4">🏗️</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مشاريع</h3>
            <p class="text-gray-600">لم يتم العثور على أي مشاريع للفترة المحددة</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-center text-sm text-gray-600">
            <p>تم إنشاء هذا التقرير بواسطة نظام إدارة الموظفين - شركة طيبة للتسويق الإلكتروني</p>
            <p class="mt-1">تاريخ الطباعة: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="no-print fixed bottom-4 left-4">
        <button onclick="window.print()" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700">
            <i class="fas fa-print ml-2"></i>
            طباعة
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                // Uncomment the next line if you want auto-print
                // window.print();
            }, 1000);
        });
    </script>
</body>
</html>