<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تقرير العملاء المحتملين</title>
    
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
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .stats-table th,
        .stats-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        
        .stats-table th {
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
            vertical-align: top;
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
        
        .classification-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin: 1px;
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .whatsapp-link {
            color: #25d366;
            text-decoration: none;
            font-weight: bold;
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
        <h2>تقرير العملاء المحتملين</h2>
        <p>الموظف: {{ auth('employee')->user()->name }} ({{ auth('employee')->user()->employee_id }})</p>
        @if($selectedClassification)
            <p>التصنيف: {{ $selectedClassification->display_name }}</p>
        @endif
        @if($search)
            <p>البحث: {{ $search }}</p>
        @endif
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <!-- Statistics Section -->
    <div class="info-section">
        <h3 class="section-title">إحصائيات التصنيفات</h3>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>التصنيف</th>
                    <th>عدد العملاء</th>
                    <th>النسبة</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCustomers = array_sum(array_column($classificationStats, 'count'));
                @endphp
                @foreach($classificationStats as $stat)
                    <tr>
                        <td>{{ $stat['display_name'] }}</td>
                        <td>{{ $stat['count'] }}</td>
                        <td>{{ $totalCustomers > 0 ? number_format(($stat['count'] / $totalCustomers) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td>الإجمالي</td>
                    <td>{{ $totalCustomers }}</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Customers Section -->
    <div class="customers-section">
        <h3 class="section-title">تفاصيل العملاء المحتملين</h3>
        @if($customers->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>اسم العميل</th>
                        <th>الجوال</th>
                        <th>وصف العمل</th>
                        <th>التصنيفات</th>
                        <th>رابط الواتساب</th>
                        <th>تاريخ الإضافة</th>
                        <th>الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ Str::limit($customer->work_description, 80) }}</td>
                            <td>
                                @foreach($customer->classifications_badges as $badge)
                                    <span class="classification-badge">{{ $badge['display_name'] }}</span>
                                @endforeach
                                @if(empty($customer->classifications_badges))
                                    <span style="color: #9ca3af;">لا يوجد تصنيف</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->whatsapp_link)
                                    <a href="{{ $customer->whatsapp_link }}" class="whatsapp-link">واتساب</a>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                            <td>{{ Str::limit($customer->notes, 50) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #666;">لا توجد عملاء محتملين وفقاً للمعايير المحددة</p>
        @endif
    </div>

    <!-- Summary Section -->
    @if($customers->count() > 0)
        <div class="summary-section">
            <h3 class="section-title">ملخص التقرير</h3>
            <table class="stats-table">
                <tr>
                    <th>إجمالي العملاء المحتملين</th>
                    <td>{{ $customers->count() }}</td>
                </tr>
                <tr>
                    <th>العملاء بدون تصنيف</th>
                    <td>{{ $customers->filter(function($c) { return empty($c->customer_classifications); })->count() }}</td>
                </tr>
                <tr>
                    <th>العملاء مع تصنيفات متعددة</th>
                    <td>{{ $customers->filter(function($c) { return count($c->customer_classifications ?? []) > 1; })->count() }}</td>
                </tr>
                <tr>
                    <th>معدل التصنيفات للعميل الواحد</th>
                    <td>{{ $customers->count() > 0 ? number_format($customers->sum(function($c) { return count($c->customer_classifications ?? []); }) / $customers->count(), 2) : 0 }}</td>
                </tr>
                <tr>
                    <th>العملاء مع روابط واتساب</th>
                    <td>{{ $customers->filter(function($c) { return !empty($c->whatsapp_link); })->count() }}</td>
                </tr>
                <tr>
                    <th>العملاء مع ملاحظات</th>
                    <td>{{ $customers->filter(function($c) { return !empty($c->notes); })->count() }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Classification Details -->
    @if($customers->count() > 0 && !$selectedClassification)
        <div class="classification-details">
            <h3 class="section-title">تفاصيل التصنيفات</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>التصنيف</th>
                        <th>أسماء العملاء</th>
                        <th>العدد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classificationStats as $stat)
                        @if($stat['count'] > 0)
                            <tr>
                                <td>{{ $stat['display_name'] }}</td>
                                <td>
                                    @php
                                        $customersWithThisClassification = $customers->filter(function($customer) use ($stat) {
                                            return in_array($stat['name'], $customer->customer_classifications ?? []);
                                        });
                                    @endphp
                                    {{ $customersWithThisClassification->pluck('customer_name')->implode('، ') }}
                                </td>
                                <td>{{ $stat['count'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>نظام إدارة شركة طيبة - تقرير العملاء المحتملين</p>
        <p>الموظف: {{ auth('employee')->user()->name }} - القسم: {{ auth('employee')->user()->department_name }}</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>