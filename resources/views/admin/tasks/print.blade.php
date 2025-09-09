{{-- resources/views/admin/tasks/print.blade.php --}}

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة المهام اليومية - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap');
        
        body { 
            font-family: 'Tajawal', Arial, sans-serif; 
            font-size: 14px;
            line-height: 1.4;
            direction:rtl;
              padding: 30px 60px;
        }
        
        @media print {
            .noprint { 
                display: none !important; 
            }
            
            html, body { 
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-break {
                page-break-after: always;
            }
            
            .print-avoid-break {
                page-break-inside: avoid;
            }
            
            /* تحسين الألوان للطباعة */
            .bg-gray-50 {
                background-color: #f8f9fa !important;
            }
            
            .border {
                border: 1px solid #000 !important;
            }
            
            /* تحسين النصوص للطباعة */
            .text-xs {
                font-size: 11px !important;
            }
            
            .text-sm {
                font-size: 12px !important;
            }
            
            /* تقليل الهوامش للطباعة */
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        /* تحسين الجداول */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
            vertical-align: top;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* تحسين الألوان للحالات */
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }

        .print-button-large {
  background-color: #16a34a; /* bg-green-600 */
  color: white;
  padding: 0.5rem 1.5rem; /* px-6 py-2 */
  border-radius: 0.5rem; /* rounded-lg */
  font-weight: 700; /* font-bold */
  box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.7), 0 4px 6px -2px rgba(22, 163, 74, 0.5); /* shadow-lg */
  display: inline-flex;
  align-items: center;
  gap: 0.5rem; /* gap-2 */
  cursor: pointer;
  transition: background-color 0.2s ease; /* transition-colors duration-200 */
  border: none;
  font-size: 1rem; /* تقريبا أكبر من الزر السابق */
  text-align:center;
  margin:auto;
}

.print-button-large:hover {
  background-color: #15803d; /* bg-green-700 */
}

.print-button-large svg {
  width: 1rem; /* w-4 */
  height: 1rem; /* h-4 */
  stroke: currentColor;
  fill: none;
}

    </style>
</head>

<body class="bg-white text-gray-900 p-6">
    
    <!-- Print and Back Buttons (Screen Only) -->
    <div class="noprint mb-6 flex justify-center gap-4">
   <button onclick="window.print()" class="print-button-large">
  <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
  </svg>
  طباعة التقرير
</button>

        
    
    </div>

    <!-- Header Section -->
    <div class="print-avoid-break mb-8">
        <!-- Logo and Title -->
        <div class="flex items-center justify-between mb-6 border-b-2 border-gray-300 pb-4">
            <div class="flex-1 text-center">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">قائمة المهام اليومية</h1>
                <p class="text-lg text-gray-600">{{ config('app.name', 'نظام إدارة المهام') }}</p>
            </div>
            
            <!-- Date and Time -->
            <div class="text-sm text-gray-500">
                <div>تاريخ الطباعة: {{ now()->format('Y-m-d') }}</div>
                <div>وقت الطباعة: {{ now()->format('H:i') }}</div>
            </div>
        </div>
        
        <!-- Filter Information -->
        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-lg mb-3 text-gray-700">معلومات الفلاتر المطبقة:</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-700">الحالة:</span>
                    <span class="mr-2">
                        @if(request('status') === 'completed')
                            <span class="status-completed px-2 py-1 rounded text-xs">مهام مكتملة</span>
                        @elseif(request('status') === 'pending')
                            <span class="status-pending px-2 py-1 rounded text-xs">مهام غير مكتملة</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">جميع المهام</span>
                        @endif
                    </span>
                </div>
                
                <div>
                    <span class="font-semibold text-gray-700">التاريخ المحدد:</span>
                    <span class="mr-2">{{ request('date') ?: '---' }}</span>
                </div>
                
                <div class="col-span-2">
                    <span class="font-semibold text-gray-700">البحث النصي:</span>
                    <span class="mr-2">{{ request('search') ?: '---' }}</span>
                </div>
                
                <div class="col-span-2">
                    <span class="font-semibold text-gray-700">إجمالي النتائج:</span>
                    <span class="mr-2 font-bold text-blue-600">{{ $tasks->count() }} مهمة</span>
                </div>
            </div>
        </div>

        <!-- Statistics Summary -->
        @php
            $completedCount = $tasks->where('status', 'completed')->count();
            $pendingCount = $tasks->where('status', 'pending')->count();
            $completionRate = $tasks->count() > 0 ? round(($completedCount / $tasks->count()) * 100, 1) : 0;
        @endphp
        
        <div class="grid grid-cols-3 gap-4 mb-6 text-center">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="text-2xl font-bold text-green-600">{{ $completedCount }}</div>
                <div class="text-sm text-green-700">مهام مكتملة</div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                <div class="text-sm text-yellow-700">مهام معلقة</div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="text-2xl font-bold text-blue-600">{{ $completionRate }}%</div>
                <div class="text-sm text-blue-700">معدل الإنجاز</div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="print-avoid-break">
        <table class="w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="w-12 text-center border border-gray-300 py-3 px-2 font-bold">#</th>
                    <th class="w-1/4 border border-gray-300 py-3 px-3 font-bold">عنوان المهمة</th>
                    <th class="w-2/5 border border-gray-300 py-3 px-3 font-bold">التفاصيل</th>
                    <th class="w-24 text-center border border-gray-300 py-3 px-2 font-bold">تاريخ المهمة</th>
                    <th class="w-20 text-center border border-gray-300 py-3 px-2 font-bold">الحالة</th>
                    <th class="w-32 text-center border border-gray-300 py-3 px-2 font-bold">تاريخ الإنجاز</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="text-center border border-gray-300 py-2 px-2 font-medium">
                            {{ $loop->iteration }}
                        </td>
                        
                        <td class="border border-gray-300 py-2 px-3">
                            <div class="font-bold text-gray-800">{{ $task->title }}</div>
                            @if($task->created_by_admin)
                                <div class="text-xs text-gray-500 mt-1">منشئ: إدارة</div>
                            @elseif($task->created_by_employee)
                                <div class="text-xs text-gray-500 mt-1">منشئ: موظف</div>
                            @endif
                        </td>
                        
                        <td class="border border-gray-300 py-2 px-3">
                            <div class="text-sm text-gray-700 leading-relaxed">
                                {{ $task->details ?: 'لا توجد تفاصيل إضافية' }}
                            </div>
                        </td>
                        
                        <td class="text-center border border-gray-300 py-2 px-2">
                            <div class="font-medium">{{ Carbon\Carbon::parse($task->task_date)->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500">{{ Carbon\Carbon::parse($task->task_date)->translatedFormat('l') }}</div>
                        </td>
                        
                        <td class="text-center border border-gray-300 py-2 px-2">
                            @if($task->status == 'completed')
                                <div class="status-completed rounded-md px-2 py-1 text-xs font-medium">
                                    ✓ مكتملة
                                </div>
                            @else
                                <div class="status-pending rounded-md px-2 py-1 text-xs font-medium">
                                    ⏳ معلقة
                                </div>
                            @endif
                        </td>
                        
                        <td class="text-center border border-gray-300 py-2 px-2">
                            @if($task->completed_at)
                                <div class="font-medium text-xs">{{ $task->completed_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $task->completed_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400 text-xs">---</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 border border-gray-300">
                            <div class="text-gray-500">
                                <div class="text-lg font-medium mb-2">📋 لا توجد مهام</div>
                                <div class="text-sm">لا توجد مهام مطابقة للفلاتر المحددة</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer Information -->
    <div class="mt-8 pt-4 border-t border-gray-300 print-avoid-break">
        <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
            <div>
                <div><strong>تاريخ الطباعة:</strong> {{ now()->translatedFormat('l، j F Y') }}</div>
                <div><strong>وقت الطباعة:</strong> {{ now()->format('h:i A') }}</div>
            </div>
            
            <div class="text-left">
                <div><strong>المستخدم:</strong> {{ auth('admin')->user()->name ?? 'غير محدد' }}</div>
                <div><strong>النظام:</strong> {{ config('app.name') }}</div>
            </div>
        </div>
        
        @if($tasks->count() > 0)
            <div class="mt-4 text-center text-xs text-gray-500 border-t pt-2">
                تم إنشاء هذا التقرير بواسطة نظام إدارة المهام - جميع الحقوق محفوظة
            </div>
        @endif
    </div>

</body>
</html>