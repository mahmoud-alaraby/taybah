<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير الحضور - {{ $employee->name }} - {{ $months[$month] }} {{ $year }}</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
        }
        
        .employee-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
            font-size: 12px;
        }
        
        .attendance-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        
        .attendance-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-ontime {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .status-late {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .status-temp-out {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        
        .overtime {
            color: #6f42c1;
            font-weight: bold;
        }
        
        .summary-section {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            color: #666;
            font-size: 12px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>شركة طيبة</h1>
        <h2>تقرير الحضور والانصراف</h2>
        <p>{{ $months[$month] }} {{ $year }}</p>
    </div>

    <div class="employee-info">
        <h3>بيانات الموظف</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
            <div><strong>الاسم:</strong> {{ $employee->name }}</div>
            <div><strong>رقم الموظف:</strong> {{ $employee->employee_id }}</div>
            <div><strong>المنصب:</strong> {{ $employee->position }}</div>
            <div><strong>القسم:</strong> {{ $employee->department_name }}</div>
            <div><strong>تاريخ التقرير:</strong> {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <!-- إحصائيات الشهر -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #007bff;">{{ $monthlyStats['working_days'] }}</div>
            <div class="stat-label">أيام العمل المطلوبة</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #28a745;">{{ $monthlyStats['attended_days'] }}</div>
            <div class="stat-label">أيام الحضور</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #ffc107;">{{ $monthlyStats['on_time_days'] }}</div>
            <div class="stat-label">أيام في الموعد</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #dc3545;">{{ $monthlyStats['late_days'] }}</div>
            <div class="stat-label">أيام التأخير</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #fd7e14;">{{ $monthlyStats['temp_checkout_days'] ?? 0 }}</div>
            <div class="stat-label">أيام انصراف مؤقت</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6f42c1;">{{ number_format($monthlyStats['total_work_hours'], 1) }}</div>
            <div class="stat-label">إجمالي ساعات العمل</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #fd7e14;">{{ number_format($monthlyStats['total_overtime_hours'], 1) }}</div>
            <div class="stat-label">الساعات الإضافية</div>
        </div>
    </div>

    <!-- جدول تفصيلي -->
    @if($attendances->count() > 0)
        <h3>التفاصيل اليومية</h3>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>اليوم</th>
                    <th>وقت الحضور</th>
                    <th>وقت الانصراف</th>
                    <th>انصراف مؤقت</th>
                    <th>إجمالي الساعات</th>
                    <th>ساعات إضافية</th>
                    <th>الحالة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('Y-m-d') }}</td>
                        <td>{{ $attendance->date->translatedFormat('l') }}</td>
                        <td>{{ $attendance->check_in_time->format('H:i') }}</td>
                        <td>
                            @if($attendance->check_out_time)
                                {{ $attendance->check_out_time->format('H:i') }}
                                @if($attendance->checkout_type === 'final')
                                    <small style="color: #dc3545;">(نهائي)</small>
                                @endif
                            @elseif($attendance->is_temp_out)
                                <span style="color: #fd7e14;">انصراف مؤقت</span>
                            @else
                                لم ينصرف
                            @endif
                        </td>
                        <td>
                            @if($attendance->temp_checkout_time)
                                @if($attendance->temp_checkin_time)
                                    {{ $attendance->temp_checkout_time->format('H:i') }} - {{ $attendance->temp_checkin_time->format('H:i') }}
                                    <br><small>{{ $attendance->temp_out_duration }}</small>
                                    @if($attendance->temp_checkout_count > 1)
                                        <br><small>({{ $attendance->temp_checkout_count }} مرات)</small>
                                    @endif
                                @elseif($attendance->is_temp_out)
                                    منذ {{ $attendance->temp_checkout_time->format('H:i') }}
                                    <br><small>{{ $attendance->temp_out_duration }}</small>
                                @else
                                    {{ $attendance->temp_out_duration ?? '-' }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($attendance->total_hours ?? 0, 1) }}</td>
                        <td class="{{ $attendance->overtime_hours > 0 ? 'overtime' : '' }}">
                            {{ $attendance->overtime_hours > 0 ? number_format($attendance->overtime_hours, 1) : '-' }}
                        </td>
                        <td>
                            @if($attendance->is_temp_out)
                                <span class="status-temp-out">انصراف مؤقت</span>
                            @elseif($attendance->is_late)
                                <span class="status-late">متأخر {{ $attendance->late_minutes }}د</span>
                            @else
                                <span class="status-ontime">في الموعد</span>
                            @endif
                            
                            @if($attendance->check_out_time && $attendance->checkout_type === 'final')
                                <br><span style="background-color: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 4px; font-size: 10px;">انصراف نهائي</span>
                            @endif
                        </td>
                        <td>{{ $attendance->notes ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- ملخص الأداء -->
    <div class="summary-section">
        <h3>تقييم الأداء الشهري</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <h4>نسبة الحضور</h4>
                <div style="font-size: 24px; font-weight: bold; color: #007bff;">
                    {{ $monthlyStats['attendance_percentage'] }}%
                </div>
                <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
                    <div style="background: #007bff; height: 100%; width: {{ $monthlyStats['attendance_percentage'] }}%;"></div>
                </div>
            </div>
            <div>
                <h4>الالتزام بالمواعيد</h4>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">
                    {{ $monthlyStats['punctuality_percentage'] }}%
                </div>
                <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
                    <div style="background: #28a745; height: 100%; width: {{ $monthlyStats['punctuality_percentage'] }}%;"></div>
                </div>
            </div>
            <div>
                <h4>متوسط ساعات العمل اليومية</h4>
                <div style="font-size: 24px; font-weight: bold; color: #6f42c1;">
                    {{ number_format($monthlyStats['average_daily_hours'] ?? 0, 1) }} ساعة
                </div>
                <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
                    <div style="background: #6f42c1; height: 100%; width: {{ min(100, ($monthlyStats['average_daily_hours'] ?? 0 / 8) * 100) }}%;"></div>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; border-radius: 5px; 
            {{ $monthlyStats['attendance_percentage'] >= 95 ? 'background: #d4edda; color: #155724;' : 
               ($monthlyStats['attendance_percentage'] >= 85 ? 'background: #fff3cd; color: #856404;' : 'background: #f8d7da; color: #721c24;') }}">
            <strong>التقييم العام:</strong>
            @if($monthlyStats['attendance_percentage'] >= 95)
                أداء ممتاز - الموظف ملتزم جداً بالحضور والمواعيد
            @elseif($monthlyStats['attendance_percentage'] >= 85)
                أداء جيد - يحتاج لتحسين بسيط في الالتزام
            @else
                يحتاج متابعة - الأداء دون المستوى المطلوب
            @endif
        </div>
    </div>

    <div class="footer">
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>نظام إدارة شركة طيبة - تقرير الحضور والانصراف</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>