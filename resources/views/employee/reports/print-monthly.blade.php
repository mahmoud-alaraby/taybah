<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير العمل الشهري - {{ $employee->name }} - {{ $months[$month] }} {{ $year }}</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 10px 0 5px 0;
            color: #3b82f6;
            font-size: 20px;
        }
        
        .header .period {
            font-size: 16px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .employee-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 5px solid #0ea5e9;
        }
        
        .employee-info h3 {
            margin: 0 0 15px 0;
            color: #0c4a6e;
            font-size: 18px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #bae6fd;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #0c4a6e;
        }
        
        .info-value {
            color: #1e40af;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .work-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .work-summary-table th,
        .work-summary-table td {
            padding: 12px;
            text-align: right;
            font-size: 13px;
        }
        
        .work-summary-table th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        
        .work-summary-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .work-summary-table tbody tr:hover {
            background-color: #e0f2fe;
        }
        
        .projects-section {
            margin-top: 30px;
        }
        
        .project-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .project-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .project-tasks {
            padding: 15px 20px;
        }
        
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .task-item:last-child {
            border-bottom: none;
        }
        
        .task-name {
            font-weight: 500;
            color: #374151;
        }
        
        .task-hours {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .summary-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 30px;
            border: 1px solid #f59e0b;
        }
        
        .summary-section h3 {
            color: #92400e;
            margin: 0 0 20px 0;
            font-size: 20px;
        }
        
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .performance-item h4 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .performance-bar {
            background: #fbbf24;
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .performance-fill {
            background: linear-gradient(90deg, #059669, #10b981);
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .performance-value {
            font-size: 28px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }
        
        .evaluation-box {
            margin-top: 25px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        
        .evaluation-excellent {
            background: #dcfce7;
            color: #166534;
            border: 2px solid #22c55e;
        }
        
        .evaluation-good {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }
        
        .evaluation-needs-improvement {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 2px solid #e5e7eb;
            padding-top: 25px;
            color: #6b7280;
            font-size: 13px;
        }
        
        .footer .date {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
            .stat-card { break-inside: avoid; }
            .project-card { break-inside: avoid; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>شركة طيبة</h1>
        <h2>تقرير العمل الشهري</h2>
        <p class="period">{{ $months[$month] }} {{ $year }}</p>
    </div>

    <div class="employee-info">
        <h3>بيانات الموظف</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value">{{ $employee->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الموظف:</span>
                    <span class="info-value">{{ $employee->employee_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">المنصب:</span>
                    <span class="info-value">{{ $employee->position }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">القسم:</span>
                    <span class="info-value">{{ $employee->department_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">تاريخ التقرير:</span>
                    <span class="info-value">{{ now()->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">الفترة:</span>
                    <span class="info-value">{{ $months[$month] }} {{ $year }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الشهر -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #3b82f6;">{{ $monthlyStats['days_worked'] }}</div>
            <div class="stat-label">أيام العمل</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #10b981;">{{ number_format($monthlyStats['total_hours'], 1) }}</div>
            <div class="stat-label">إجمالي الساعات</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #f59e0b;">{{ number_format($monthlyStats['average_daily_hours'], 1) }}</div>
            <div class="stat-label">متوسط يومي</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #8b5cf6;">{{ number_format($monthlyStats['overtime_hours'], 1) }}</div>
            <div class="stat-label">ساعات إضافية</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #ef4444;">{{ number_format($monthlyStats['target_achievement'], 1) }}%</div>
            <div class="stat-label">نسبة الإنجاز</div>
        </div>
    </div>

    @if($summaries->count() > 0)
        <div class="page-break"></div>
        <h3 style="color: #1e40af; margin-bottom: 20px; font-size: 22px;">تفاصيل العمل اليومي</h3>
        <table class="work-summary-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>اليوم</th>
                    <th>ساعات العمل</th>
                    <th>ساعات إضافية</th>
                    <th>نسبة الإنجاز</th>
                    <th>عدد المشاريع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaries as $summary)
                    <tr>
                        <td style="font-weight: 600;">{{ $summary->date->format('Y-m-d') }}</td>
                        <td>{{ $summary->date->translatedFormat('l') }}</td>
                        <td style="color: #10b981; font-weight: 600;">{{ number_format($summary->total_work_hours, 1) }}</td>
                        <td style="color: #8b5cf6; font-weight: 600;">
                            {{ $summary->overtime_hours > 0 ? number_format($summary->overtime_hours, 1) : '-' }}
                        </td>
                        <td>
                            <span style="background: {{ $summary->daily_target_percentage >= 100 ? '#dcfce7' : ($summary->daily_target_percentage >= 80 ? '#fef3c7' : '#fee2e2') }}; 
                                         color: {{ $summary->daily_target_percentage >= 100 ? '#166534' : ($summary->daily_target_percentage >= 80 ? '#92400e' : '#991b1b') }}; 
                                         padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                {{ number_format($summary->daily_target_percentage, 1) }}%
                            </span>
                        </td>
                        <td>{{ is_array($summary->projects_worked) ? count($summary->projects_worked) : 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- تفاصيل المشاريع -->
        @php
            $allProjects = [];
            foreach($summaries as $summary) {
                if(is_array($summary->projects_worked)) {
                    foreach($summary->projects_worked as $project) {
                        $projectName = $project['project_name'] ?? 'مشروع غير محدد';
                        if(isset($allProjects[$projectName])) {
                            $allProjects[$projectName]['total_hours'] += $project['hours'] ?? 0;
                            $allProjects[$projectName]['days']++;
                            if(isset($project['tasks'])) {
                                foreach($project['tasks'] as $task) {
                                    $taskName = $task['task_name'] ?? 'مهمة غير محددة';
                                    if(isset($allProjects[$projectName]['tasks'][$taskName])) {
                                        $allProjects[$projectName]['tasks'][$taskName] += $task['hours'] ?? 0;
                                    } else {
                                        $allProjects[$projectName]['tasks'][$taskName] = $task['hours'] ?? 0;
                                    }
                                }
                            }
                        } else {
                            $allProjects[$projectName] = [
                                'total_hours' => $project['hours'] ?? 0,
                                'days' => 1,
                                'tasks' => []
                            ];
                            if(isset($project['tasks'])) {
                                foreach($project['tasks'] as $task) {
                                    $taskName = $task['task_name'] ?? 'مهمة غير محددة';
                                    $allProjects[$projectName]['tasks'][$taskName] = $task['hours'] ?? 0;
                                }
                            }
                        }
                    }
                }
            }
        @endphp

        @if(count($allProjects) > 0)
            <div class="page-break"></div>
            <h3 style="color: #1e40af; margin-bottom: 20px; font-size: 22px;">تفاصيل المشاريع</h3>
            <div class="projects-section">
                @foreach($allProjects as $projectName => $projectData)
                    <div class="project-card">
                        <div class="project-header">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span>{{ $projectName }}</span>
                                <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 15px; font-size: 14px;">
                                    {{ number_format($projectData['total_hours'], 1) }} ساعة
                                </span>
                            </div>
                        </div>
                        @if(count($projectData['tasks']) > 0)
                            <div class="project-tasks">
                                @foreach($projectData['tasks'] as $taskName => $taskHours)
                                    <div class="task-item">
                                        <span class="task-name">{{ $taskName }}</span>
                                        <span class="task-hours">{{ number_format($taskHours, 1) }} ساعة</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <!-- ملخص الأداء -->
    <div class="summary-section">
        <h3>تقييم الأداء الشهري</h3>
        <div class="performance-grid">
            <div class="performance-item">
                <h4>معدل ساعات العمل اليومية</h4>
                <div class="performance-value">{{ number_format($monthlyStats['average_daily_hours'], 1) }}</div>
                <div class="performance-bar">
                    <div class="performance-fill" style="width: {{ min(100, ($monthlyStats['average_daily_hours'] / 8) * 100) }}%;"></div>
                </div>
            </div>
            <div class="performance-item">
                <h4>نسبة تحقيق الأهداف</h4>
                <div class="performance-value">{{ number_format($monthlyStats['target_achievement'], 1) }}%</div>
                <div class="performance-bar">
                    <div class="performance-fill" style="width: {{ min(100, $monthlyStats['target_achievement']) }}%;"></div>
                </div>
            </div>
            <div class="performance-item">
                <h4>إنتاجية العمل</h4>
                @php
                    $productivity = ($monthlyStats['days_worked'] > 0) ? ($monthlyStats['total_hours'] / $monthlyStats['days_worked']) / 7 * 100 : 0;
                @endphp
                <div class="performance-value">{{ number_format($productivity, 1) }}%</div>
                <div class="performance-bar">
                    <div class="performance-fill" style="width: {{ min(100, $productivity) }}%;"></div>
                </div>
            </div>
        </div>

        <div class="evaluation-box {{ $monthlyStats['target_achievement'] >= 90 ? 'evaluation-excellent' : ($monthlyStats['target_achievement'] >= 70 ? 'evaluation-good' : 'evaluation-needs-improvement') }}">
            <strong>التقييم العام:</strong>
            @if($monthlyStats['target_achievement'] >= 90)
                أداء ممتاز - الموظف يحقق أهدافه بانتظام ويتفوق في الأداء
            @elseif($monthlyStats['target_achievement'] >= 70)
                أداء جيد - الموظف ملتزم ولكن يحتاج لتحسين بسيط لتحقيق الأهداف المطلوبة
            @else
                يحتاج متابعة - الأداء دون المستوى المطلوب ويحتاج لخطة تحسين
            @endif
        </div>
    </div>

    <div class="footer">
        <div class="date">تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</div>
        <div>نظام إدارة شركة طيبة - تقرير العمل الشهري</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>