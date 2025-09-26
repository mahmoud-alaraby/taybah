<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التقرير الأسبوعي - {{ $employee->name }} - {{ $startOfWeek->format('Y-m-d') }} إلى {{ $endOfWeek->format('Y-m-d') }}</title>
    
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
                    <p class="text-sm opacity-90">التقرير الأسبوعي لساعات العمل</p>
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
                    <p><span class="font-medium">الفترة:</span> {{ $startOfWeek->format('Y-m-d') }} إلى {{ $endOfWeek->format('Y-m-d') }}</p>
                    <p><span class="font-medium">تاريخ الإنشاء:</span> {{ now()->format('Y-m-d H:i') }}</p>
                    <p><span class="font-medium">نوع التقرير:</span> تقرير أسبوعي</p>
                    <p><span class="font-medium">عدد الأيام:</span> {{ $summaries->count() }} أيام</p>
                </div>
            </div>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">الإحصائيات الرئيسية</h3>
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($weeklyStats['total_hours'], 2) }}</div>
                    <div class="text-sm text-blue-700">إجمالي الساعات</div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($weeklyStats['overtime_hours'], 2) }}</div>
                    <div class="text-sm text-purple-700">ساعات إضافية</div>
                </div>
                
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($weeklyStats['average_daily_hours'], 2) }}</div>
                    <div class="text-sm text-green-700">متوسط يومي</div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $weeklyStats['days_worked'] }}</div>
                    <div class="text-sm text-orange-700">أيام العمل</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600">{{ number_format($weeklyStats['target_achievement'], 1) }}%</div>
                    <div class="text-sm text-red-700">نسبة الهدف</div>
                </div>
            </div>
        </div>

        <!-- تفاصيل الأيام -->
        @if($summaries->count() > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">تفاصيل أيام العمل</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">التاريخ</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">اليوم</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">ساعات العمل</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">ساعات إضافية</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">نسبة الهدف</th>
                            <th class="border border-gray-300 px-4 py-2 text-right text-sm font-medium">التقييم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaries as $summary)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ \Carbon\Carbon::parse($summary->date)->format('Y-m-d') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ \Carbon\Carbon::parse($summary->date)->locale('ar')->dayName }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-medium">
                                {{ number_format($summary->total_work_hours, 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ number_format($summary->overtime_hours, 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                {{ number_format($summary->daily_target_percentage, 1) }}%
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm">
                                @if($summary->daily_target_percentage >= 100)
                                    <span class="text-green-600 font-medium">ممتاز</span>
                                @elseif($summary->daily_target_percentage >= 80)
                                    <span class="text-yellow-600 font-medium">جيد</span>
                                @else
                                    <span class="text-red-600 font-medium">يحتاج تحسين</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-200">
                        <tr>
                            <td colspan="2" class="border border-gray-300 px-4 py-2 text-sm font-bold text-center">الإجمالي</td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ number_format($summaries->sum('total_work_hours'), 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ number_format($summaries->sum('overtime_hours'), 2) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ number_format($summaries->avg('daily_target_percentage'), 1) }}%
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-sm font-bold">
                                {{ $summaries->count() }} أيام
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- الرسم البياني الأسبوعي -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">الرسم البياني للأداء الأسبوعي</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-7 gap-2">
                    @php
                        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'السبت', 'الجمعة'];
                        $maxHours = $summaries->max('total_work_hours') ?: 8;
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $currentDate = $startOfWeek->copy()->addDays($i);
                            $daySummary = $summaries->firstWhere('date', $currentDate->format('Y-m-d'));
                            $hours = $daySummary ? $daySummary->total_work_hours : 0;
                            $height = $maxHours > 0 ? ($hours / $maxHours) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <div class="bg-white border border-gray-300 h-32 flex flex-col justify-end mb-2 relative">
                                @if($hours > 0)
                                    <div class="bg-blue-500 rounded-t" style="height: {{ $height }}%"></div>
                                @endif
                                <div class="absolute top-1 left-1 right-1 text-xs">
                                    {{ number_format($hours, 1) }}
                                </div>
                            </div>
                            <div class="text-xs font-medium">{{ $days[$i] }}</div>
                            <div class="text-xs text-gray-500">{{ $currentDate->format('m/d') }}</div>
                        </div>
                    @endfor
                </div>
                <div class="mt-4 text-center text-sm text-gray-600">
                    <p>ساعات العمل اليومية خلال الأسبوع</p>
                </div>
            </div>
        </div>
        @endif

        @if($summaries->count() == 0)
        <div class="bg-gray-50 p-12 rounded-lg text-center">
            <div class="text-gray-400 text-4xl mb-4">📅</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد بيانات</h3>
            <p class="text-gray-600">لم يتم العثور على بيانات عمل لهذا الأسبوع</p>
        </div>
        @endif

        <!-- ملاحظات وتوصيات -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">ملاحظات وتوصيات</h3>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                @php
                    $avgTarget = $summaries->avg('daily_target_percentage');
                    $totalDays = $summaries->count();
                    $workingDays = 6; // عدد أيام العمل المتوقعة في الأسبوع
                @endphp
                
                <div class="space-y-2 text-sm">
                    @if($avgTarget >= 100)
                        <p class="text-green-700">✅ <strong>أداء ممتاز:</strong> تم تحقيق الأهداف المطلوبة بنجاح</p>
                    @elseif($avgTarget >= 80)
                        <p class="text-yellow-700">⚠️ <strong>أداء جيد:</strong> يمكن تحسين الأداء لتحقيق المزيد</p>
                    @else
                        <p class="text-red-700">❌ <strong>يحتاج تحسين:</strong> الأداء أقل من المطلوب</p>
                    @endif
                    
                    @if($totalDays < $workingDays)
                        <p class="text-orange-700">📅 <strong>الحضور:</strong> تم العمل {{ $totalDays }} أيام من أصل {{ $workingDays }} أيام متوقعة</p>
                    @endif
                    
                    @if($weeklyStats['overtime_hours'] > 0)
                        <p class="text-blue-700">⏰ <strong>ساعات إضافية:</strong> تم العمل {{ number_format($weeklyStats['overtime_hours'], 2) }} ساعة إضافية</p>
                    @endif
                </div>
            </div>
        </div>

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