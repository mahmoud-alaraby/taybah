<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير حركة العملاء - {{ $months[$month] }} {{ $year }}</title>
    
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
        
        .info-section {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: right;
        }
        
        .summary-table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: right;
        }
        
        .data-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .section-title {
            background-color: #333;
            color: white;
            padding: 10px;
            margin: 0 0 15px 0;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        
        .positive {
            color: #28a745;
        }
        
        .negative {
            color: #dc3545;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
        <h2>تقرير حركة العملاء</h2>
        <p>{{ $months[$month] }} {{ $year }}</p>
        @if($selectedEmployee)
            <p>الموظف: {{ $selectedEmployee->name }} ({{ $selectedEmployee->employee_id }})</p>
        @else
            <p>تقرير شامل لجميع الموظفين</p>
        @endif
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="info-section">
        <table class="summary-table">
            <tr>
                <th>البيان</th>
                <th>المبلغ (ريال)</th>
                <th>النسبة</th>
            </tr>
            <tr>
                <td>إجمالي المبالغ المتفق عليها</td>
                <td class="positive">{{ number_format($totalAgreed, 2) }}</td>
                <td>{{ $targetAmount > 0 ? number_format(($totalAgreed / $targetAmount) * 100, 2) . '%' : '-' }}</td>
            </tr>
            <tr>
                <td>إجمالي المبالغ المدفوعة</td>
                <td class="positive">{{ number_format($totalPaid, 2) }}</td>
                <td>{{ $totalAgreed > 0 ? number_format(($totalPaid / $totalAgreed) * 100, 2) . '%' : '-' }}</td>
            </tr>
            <tr>
                <td>إجمالي الديون</td>
                <td class="negative">{{ number_format($totalDebts, 2) }}</td>
                <td>{{ $totalAgreed > 0 ? number_format(($totalDebts / $totalAgreed) * 100, 2) . '%' : '-' }}</td>
            </tr>
            <tr>
                <td>{{ $selectedEmployee ? 'التارجت المحدد' : 'إجمالي التارجتات' }}</td>
                <td>{{ number_format($targetAmount, 2) }}</td>
                <td>100%</td>
            </tr>
            <tr class="total-row">
                <td>نسبة تحقق تارجت الاتفاق</td>
                <td colspan="2"><strong>{{ $achievementPercentage }}%</strong></td>
            </tr>
        </table>
    </div>

    <!-- Movements Section -->
    <div class="movements-section">
        <h3 class="section-title">تفاصيل حركة العملاء</h3>
        @if($movements->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>اسم العميل</th>
                        <th>الجوال</th>
                        @if(!$selectedEmployee)
                            <th>الموظف</th>
                        @endif
                        <th>وصف العمل</th>
                        <th>بداية الاتفاق</th>
                        <th>التسليم الأولي</th>
                        <th>التسليم النهائي</th>
                        <th>المبلغ المتفق</th>
                        <th>دفعة 1</th>
                        <th>دفعة 2</th>
                        <th>دفعة 3</th>
                        <th>دفعة 4</th>
                        <th>المتبقي</th>
                        <th>نوع العميل</th>
                        <th>حالة العمل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                        <tr>
                            <td>{{ $movement->customer_name }}</td>
                            <td>{{ $movement->customer_phone }}</td>
                            @if(!$selectedEmployee)
                                <td>{{ $movement->employee->name }}</td>
                            @endif
                            <td>{{ Str::limit($movement->work_description, 50) }}</td>
                            <td>{{ $movement->agreement_start_date->format('Y-m-d') }}</td>
                            <td>{{ $movement->initial_delivery_date->format('Y-m-d') }}</td>
                            <<td>
    {{ $movement->final_delivery_date?->format('Y-m-d') ?? '---' }}
</td>

                            <td class="positive">{{ number_format($movement->agreed_amount, 2) }}</td>
                            <td>{{ number_format($movement->first_payment, 2) }}</td>
                            <td>{{ number_format($movement->second_payment, 2) }}</td>
                            <td>{{ number_format($movement->third_payment, 2) }}</td>
                            <td>{{ number_format($movement->fourth_payment, 2) }}</td>
                            <td class="{{ $movement->remaining_amount > 0 ? 'negative' : 'positive' }}">
                                {{ number_format($movement->remaining_amount, 2) }}
                            </td>
                            <td>{{ $movement->customer_type }}</td>
                            <td>
                                <span class="status-badge {{ 
                                    $movement->work_status == 'جاري العمل' ? 'status-active' : 
                                    ($movement->work_status == 'تم الانتهاء' ? 'status-completed' : 'status-cancelled') 
                                }}">
                                    {{ $movement->work_status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="{{ $selectedEmployee ? '7' : '8' }}"><strong>الإجماليات</strong></td>
                        <td class="positive"><strong>{{ number_format($totalAgreed, 2) }}</strong></td>
                        <td colspan="3"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                        <td class="negative"><strong>{{ number_format($totalDebts, 2) }}</strong></td>
                        <td colspan="2">-</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #666;">لا توجد حركة عملاء في هذه الفترة</p>
        @endif
    </div>

    <div class="footer">
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>نظام إدارة شركة طيبة - تقرير حركة العملاء</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>