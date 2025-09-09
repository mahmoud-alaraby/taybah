<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير نظام التشغيل العام - {{ $startDate->translatedFormat('F Y') }}</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { print-color-adjust: exact; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            color: #333;
            background-color: #fff;
            font-size: 12px;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header .period {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .header .print-date {
            color: #9ca3af;
            font-size: 12px;
        }
        
        .filters-section {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .filters-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .filter-item {
            display: flex;
            align-items: center;
        }
        
        .filter-label {
            font-weight: 600;
            color: #4b5563;
            margin-left: 5px;
        }
        
        .filter-value {
            color: #1f2937;
            background-color: #e5e7eb;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-card.design {
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
        }
        
        .stat-card.marketing {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }
        
        .stat-card.reserved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .stat-card.completed {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }
        
        .stat-card.pending {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        
        .stat-title {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-weight: 600;
            padding: 12px 8px;
            text-align: center;
            font-size: 11px;
            border-bottom: 2px solid #1e40af;
        }
        
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .data-table tr:hover {
            background-color: #f3f4f6;
        }
        
        .task-type {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
        }
        
        .task-type.design {
            background-color: #ede9fe;
            color: #7c3aed;
        }
        
        .task-type.marketing {
            background-color: #fed7aa;
            color: #ea580c;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-align: center;
        }
        
        .status.pending {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .status.in_progress {
            background-color: #dbeafe;
            color: #2563eb;
        }
        
        .status.completed {
            background-color: #d1fae5;
            color: #059669;
        }
        
        .status.cancelled {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .reserved-badge {
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .reserved-badge.yes {
            background-color: #10b981;
            color: white;
        }
        
        .reserved-badge.no {
            background-color: #6b7280;
            color: white;
        }
        
        .date-header {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e40af;
            text-align: center;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-btn:hover {
            background: #1d4ed8;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .data-table {
                font-size: 10px;
            }
            
            .data-table th, .data-table td {
                padding: 6px 4px;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ طباعة التقرير</button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تقرير نظام التشغيل العام</h1>
            <div class="period">{{ $startDate->translatedFormat('F Y') }}</div>
            <div class="print-date">تاريخ الطباعة: {{ now()->translatedFormat('d F Y - H:i') }}</div>
        </div>

        <!-- Applied Filters -->
        @if(!empty($filters))
        <div class="filters-section">
            <div class="filters-title">الفلاتر المطبقة:</div>
            <div class="filters-grid">
                @foreach($filters as $filterName => $filterValue)
                <div class="filter-item">
                    <span class="filter-label">{{ $filterName }}:</span>
                    <span class="filter-value">{{ $filterValue }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">إجمالي المهام</div>
                <div class="stat-value">{{ $stats['total_tasks'] }}</div>
            </div>
            <div class="stat-card design">
                <div class="stat-title">مهام التصميم</div>
                <div class="stat-value">{{ $stats['design_tasks'] }}</div>
            </div>
            <div class="stat-card marketing">
                <div class="stat-title">مهام التسويق</div>
                <div class="stat-value">{{ $stats['marketing_tasks'] }}</div>
            </div>
            <div class="stat-card reserved">
                <div class="stat-title">المهام المحجوزة</div>
                <div class="stat-value">{{ $stats['reserved_tasks'] }}</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-title">مهام مكتملة</div>
                <div class="stat-value">{{ $stats['completed_tasks'] }}</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-title">مهام معلقة</div>
                <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            @if($tasks->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th style="width: 8%">التاريخ</th>
                            <th style="width: 8%">اليوم</th>
                            <th style="width: 15%">الموظف</th>
                            <th style="width: 10%">القسم</th>
                            <th style="width: 8%">المنصب</th>
                            <th style="width: 8%">نوع المهمة</th>
                            <th style="width: 20%">وصف المهمة</th>
                            <th style="width: 6%">الحجز</th>
                            <th style="width: 8%">الحالة</th>
                            <th style="width: 6%">الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp
                        @foreach($groupedTasks as $date => $dayTasks)
                            @php
                                $dateObj = \Carbon\Carbon::parse($date);
                                $isFirstInDate = true;
                            @endphp
                            @foreach($dayTasks as $task)
                                @if($isFirstInDate)
                                    <tr>
                                        <td colspan="11" class="date-header">
                                            {{ $dateObj->translatedFormat('l، d F Y') }}
                                            <span style="font-size: 10px; font-weight: normal; margin-right: 10px;">
                                                ({{ $dayTasks->count() }} مهمة)
                                            </span>
                                        </td>
                                    </tr>
                                    @php $isFirstInDate = false; @endphp
                                @endif
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td>{{ $dateObj->format('Y-m-d') }}</td>
                                    <td>{{ $dateObj->translatedFormat('l') }}</td>
                                    <td style="text-align: right; font-weight: 600;">{{ $task->employee_name }}</td>
                                    <td>{{ $task->department ?? '-' }}</td>
                                    <td>{{ $task->position ?? '-' }}</td>
                                    <td>
                                        <span class="task-type {{ $task->task_type }}">
                                            {{ $task->task_type == 'design' ? 'تصميم' : 'تسويق' }}
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-size: 10px;">
                                        {{ Str::limit($task->task_description, 50) ?: '-' }}
                                    </td>
                                    <td>
                                        <span class="reserved-badge {{ $task->is_reserved ? 'yes' : 'no' }}">
                                            {{ $task->is_reserved ? 'محجوز' : 'عادي' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status {{ $task->status }}">
                                            @switch($task->status)
                                                @case('pending')
                                                    انتظار
                                                    @break
                                                @case('in_progress')
                                                    تنفيذ
                                                    @break
                                                @case('completed')
                                                    مكتمل
                                                    @break
                                                @case('cancelled')
                                                    ملغي
                                                    @break
                                                @default
                                                    {{ $task->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td style="text-align: right; font-size: 9px;">
                                        {{ Str::limit($task->notes, 30) ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <h3>لا توجد مهام متاحة</h3>
                    <p>لا توجد مهام مطابقة للفلاتر المحددة في الفترة المطلوبة</p>
                </div>
            @endif
        </div>

        <!-- Summary by Employee -->
        @if($tasks->count() > 0)
        <div class="page-break"></div>
        <h2 style="color: #1e40af; margin-bottom: 20px; text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;">
            ملخص المهام حسب الموظف
        </h2>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>القسم</th>
                        <th>إجمالي المهام</th>
                        <th>مهام التصميم</th>
                        <th>مهام التسويق</th>
                        <th>مهام محجوزة</th>
                        <th>مهام مكتملة</th>
                        <th>مهام معلقة</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $employeeSummary = $tasks->groupBy('employee_name')->map(function($employeeTasks) {
                            return [
                                'name' => $employeeTasks->first()->employee_name,
                                'department' => $employeeTasks->first()->department,
                                'total' => $employeeTasks->count(),
                                'design' => $employeeTasks->where('task_type', 'design')->count(),
                                'marketing' => $employeeTasks->where('task_type', 'marketing')->count(),
                                'reserved' => $employeeTasks->where('is_reserved', 1)->count(),
                                'completed' => $employeeTasks->where('status', 'completed')->count(),
                                'pending' => $employeeTasks->whereIn('status', ['pending', 'in_progress'])->count(),
                            ];
                        })->sortByDesc('total');
                    @endphp
                    
                    @foreach($employeeSummary as $summary)
                    <tr>
                        <td style="text-align: right; font-weight: 600;">{{ $summary['name'] }}</td>
                        <td>{{ $summary['department'] ?? '-' }}</td>
                        <td><strong>{{ $summary['total'] }}</strong></td>
                        <td>{{ $summary['design'] }}</td>
                        <td>{{ $summary['marketing'] }}</td>
                        <td>{{ $summary['reserved'] }}</td>
                        <td style="color: #059669;">{{ $summary['completed'] }}</td>
                        <td style="color: #d97706;">{{ $summary['pending'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذا التقرير تلقائياً من نظام إدارة العمليات</p>
            <p>{{ now()->translatedFormat('l، d F Y الساعة H:i') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); };
        
        // Print function
        function printReport() {
            window.print();
        }
        
        // Add keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>