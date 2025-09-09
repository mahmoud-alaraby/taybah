<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير تكاليف التصوير الشخصي - {{ $months[$month] }} {{ $year }}</title>
    
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
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
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
        
        .receipts-section {
            margin-bottom: 30px;
        }
        
        .payments-section {
            margin-bottom: 30px;
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
        <h2>تقرير تكاليف التصوير الشخصي</h2>
        <p>{{ $months[$month] }} {{ $year }}</p>
        <p>الموظف: {{ $employee->name }} ({{ $employee->employee_id }})</p>
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
                <td>إجمالي المقبوضات</td>
                <td class="positive">{{ number_format($totalReceipts, 2) }}</td>
                <td>{{ $targetAmount > 0 ? number_format(($totalReceipts / $targetAmount) * 100, 2) . '%' : '-' }}</td>
            </tr>
            <tr>
                <td>إجمالي المدفوعات</td>
                <td class="negative">{{ number_format($totalPayments, 2) }}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>الفرق (السيولة المتاحة)</td>
                <td class="{{ $netAmount >= 0 ? 'positive' : 'negative' }}">{{ number_format($netAmount, 2) }}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>التارجت الشخصي</td>
                <td>{{ number_format($targetAmount, 2) }}</td>
                <td>100%</td>
            </tr>
            <tr class="total-row">
                <td>نسبة تحقق التارجت</td>
                <td colspan="2"><strong>{{ $achievementPercentage }}%</strong></td>
            </tr>
        </table>
    </div>

    <!-- Receipts Section -->
    <div class="receipts-section">
        <h3 class="section-title">تفاصيل المقبوضات</h3>
        @if($receipts->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المنشئ</th>
                        <th>البيان</th>
                        <th>المبلغ (ريال)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $receipt)
                        <tr>
                            <td>{{ $receipt->date->format('Y-m-d') }}</td>
                            <td>{{ $receipt->creator_name }} ({{ $receipt->creator_id }})</td>
                            <td>{{ $receipt->note }}</td>
                            <td class="positive">{{ number_format($receipt->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3"><strong>الإجمالي</strong></td>
                        <td class="positive"><strong>{{ number_format($totalReceipts, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #666;">لا توجد مقبوضات في هذه الفترة</p>
        @endif
    </div>

    <!-- Payments Section -->
    <div class="payments-section">
        <h3 class="section-title">تفاصيل المدفوعات</h3>
        @if($payments->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المنشئ</th>
                        <th>البيان</th>
                        <th>المبلغ (ريال)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->date->format('Y-m-d') }}</td>
                            <td>{{ $payment->creator_name }} ({{ $payment->creator_id }})</td>
                            <td>{{ $payment->note }}</td>
                            <td class="negative">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3"><strong>الإجمالي</strong></td>
                        <td class="negative"><strong>{{ number_format($totalPayments, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #666;">لا توجد مدفوعات في هذه الفترة</p>
        @endif
    </div>

    <div class="footer">
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>نظام إدارة شركة طيبة - تقرير تكاليف التصوير الشخصي</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>