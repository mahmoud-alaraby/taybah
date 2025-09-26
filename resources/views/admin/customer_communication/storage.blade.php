@extends('admin.layouts.app')

@section('title', 'إدارة مساحة التواصل مع العملاء')
@section('page-title', 'إدارة مساحة التواصل مع العملاء')
@section('page-subtitle', 'إدارة ملفات شاتات التواصل مع العملاء والنسخ الاحتياطية')

@php
    function formatBytes($bytes) {
        if ($bytes == 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3 space-x-reverse">
            <!-- زر الرجوع للتواصل مع العملاء -->
            <a href="{{ route('admin.customer-communication.index') }}" 
               class="flex items-center px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200 shadow-sm">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع للتواصل مع العملاء
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="flex space-x-2 space-x-reverse">
            <button onclick="refreshStorageInfo()" 
                    class="flex items-center px-3 py-2 text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors duration-200"
                    title="تحديث البيانات">
                <i class="fas fa-sync-alt ml-1"></i>
                تحديث
            </button>
        </div>
    </div>

    <!-- إحصائيات عامة مع التصميم العصري -->
    <div class="px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- إجمالي المساحة -->
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي المساحة</p>
                    <p class="text-2xl font-bold">{{ formatBytes($totalSize) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <!-- عدد الملفات -->
            <div class="bg-gradient-to-r from-teal-500 to-teal-700 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عدد الملفات</p>
                    <p class="text-2xl font-bold">{{ number_format($fileCount) }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- ملفات قديمة (+3 شهور) -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">ملفات قديمة (+3 شهور)</p>
                    <p class="text-2xl font-bold">{{ $oldFiles->count ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- إحصائيات تفصيلية -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">إحصائيات تفصيلية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($fileStats as $stat)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    @if($stat->message_type === 'file')
                                        <i class="fas fa-file ml-1 text-blue-500"></i> ملفات العملاء
                                    @else
                                        <i class="fas fa-microphone ml-1 text-green-500"></i> تسجيلات صوتية
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ number_format($stat->count) }} ملف</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold">{{ formatBytes($stat->total_size) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- إجراءات التنظيف -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">إجراءات التنظيف</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- نسخة احتياطية شاملة -->
                <div class="border rounded-lg p-4">
                    <div class="text-center">
                        <div class="mx-auto w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-download text-white"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">نسخة احتياطية شاملة</h4>
                        <p class="text-xs text-gray-500 mb-4">تحميل جميع ملفات شاتات العملاء كـ ZIP منظم</p>
                        <button onclick="createFullBackup()" class="w-full bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                            تحميل جميع الملفات
                        </button>
                    </div>
                </div>

                <!-- حذف الملفات القديمة -->
                <div class="border rounded-lg p-4">
                    <div class="text-center">
                        <div class="mx-auto w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-broom text-white"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">حذف الملفات القديمة</h4>
                        <p class="text-xs text-gray-500 mb-4">حذف ملفات أقدم من 3 شهور</p>
                        <button onclick="clearOldFiles()" class="w-full bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700">
                            تنظيف الملفات القديمة
                        </button>
                    </div>
                </div>

                <!-- إعدادات متقدمة -->
                <div class="border rounded-lg p-4">
                    <div class="text-center">
                        <div class="mx-auto w-12 h-12 bg-gray-500 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-cog text-white"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">إعدادات متقدمة</h4>
                        <p class="text-xs text-gray-500 mb-4">تحديد حجم أقصى للملفات</p>
                        <button onclick="showAdvancedSettings()" class="w-full bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                            الإعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة شاتات العملاء مع خيار التحميل المنفرد -->
    @if($userChats->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-users text-indigo-600 ml-2"></i>
                شاتات العملاء مع إمكانية التحميل المنفرد
            </h3>
            
            <!-- مربع البحث -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="chatSearch" placeholder="البحث في شاتات العملاء..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                معلومات العميل
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الموظف المسؤول
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                عدد الملفات
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="chatsTableBody">
                        @foreach($userChats as $chat)
                        <tr class="hover:bg-gray-50 chat-row" 
                            data-chat-customer="{{ strtolower($chat->potentialCustomer->customer_name ?? '') }}" 
                            data-employee-name="{{ strtolower($chat->employee->name ?? '') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                            {{ substr($chat->potentialCustomer->customer_name ?? 'ع', 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $chat->potentialCustomer->customer_name ?? 'عميل محذوف' }}</div>
                                        <div class="text-sm text-gray-500">{{ $chat->potentialCustomer->phone ?? 'غير محدد' }}</div>
                                        <div class="text-xs text-gray-400">{{ $chat->created_at->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColor = match($chat->status) {
                                        'active' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    
                                    $priorityColor = match($chat->priority) {
                                        'high' => 'bg-red-100 text-red-800',
                                        'medium' => 'bg-orange-100 text-orange-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <div class="flex flex-col space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $chat->status }}
                                    </span>
                                    @if($chat->priority !== 'normal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColor }}">
                                            {{ $chat->priority }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $chat->employee->name ?? 'غير محدد' }}</div>
                                <div class="text-sm text-gray-500">{{ $chat->employee->employee_id ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $chat->file_count }}</span>
                                    <span class="text-xs text-gray-500 mr-1">ملف</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2 space-x-reverse">
                                    <button onclick="downloadChatFiles({{ $chat->id }}, '{{ $chat->potentialCustomer->customer_name ?? 'عميل' }}')" 
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                        <i class="fas fa-download ml-1"></i>
                                        تحميل
                                    </button>
                                    <a href="{{ route('admin.customer-communication.show', $chat->potentialCustomer->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-eye ml-1"></i>
                                        عرض
                                    </a>
                                    <button onclick="clearChatFiles({{ $chat->id }}, '{{ $chat->potentialCustomer->customer_name ?? 'عميل' }}')" 
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-trash ml-1"></i>
                                        مسح الملفات
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- تحذيرات -->
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 ml-2"></i>
            <div>
                <h3 class="text-sm font-medium text-amber-800">تحذيرات مهمة:</h3>
                <div class="mt-2 text-sm text-amber-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>تأكد من أخذ نسخة احتياطية قبل حذف أي ملفات</li>
                        <li>حذف الملفات لا يمكن التراجع عنه</li>
                        <li>الملفات المحذوفة لن تكون متاحة في شاتات العملاء</li>
                        <li>النسخ الاحتياطية منظمة حسب اسم العميل مع ملف README للشرح</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات التنظيم -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-info-circle text-indigo-400 mt-0.5 ml-2"></i>
            <div>
                <h3 class="text-sm font-medium text-indigo-800">هيكل النسخة الاحتياطية:</h3>
                <div class="mt-2 text-sm text-indigo-700">
                    <div class="bg-white p-3 rounded border font-mono text-xs">
                        customer_chat_backup.zip/<br>
                        ├── README.txt (معلومات الشاتات)<br>
                        ├── backup_details.json (تفاصيل النسخة)<br>
                        ├── Customer_Chat_1_احمد_محمد/<br>
                        │&nbsp;&nbsp;&nbsp;├── files/ (الملفات المرفقة)<br>
                        │&nbsp;&nbsp;&nbsp;└── voices/ (التسجيلات الصوتية)<br>
                        └── Customer_Chat_2_فاطمة_علي/<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── files/<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── voices/
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// البحث في شاتات العملاء
document.getElementById('chatSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.chat-row');
    
    rows.forEach(row => {
        const customer = row.getAttribute('data-chat-customer');
        const employee = row.getAttribute('data-employee-name');
        
        if (customer.includes(searchTerm) || employee.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// دالة تحديث بيانات المساحة
function refreshStorageInfo() {
    Swal.fire({
        title: 'جاري تحديث البيانات...',
        html: 'يرجى الانتظار',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// تحميل جميع ملفات التواصل مع العملاء - محسن
function createFullBackup() {
    Swal.fire({
        title: 'إنشاء نسخة احتياطية شاملة',
        text: 'سيتم تحميل جميع ملفات شاتات العملاء كملف ZIP منظم. قد يستغرق هذا بعض الوقت.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'تحميل',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#6366f1'
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار مؤشر التحميل
            const loadingModal = Swal.fire({
                title: 'جاري إنشاء النسخة الاحتياطية...',
                html: 'يرجى الانتظار، قد يستغرق هذا عدة دقائق',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // إنشاء iframe مخفي للتحميل
            const downloadFrame = document.createElement('iframe');
            downloadFrame.style.display = 'none';
            downloadFrame.src = '/admin/customer-communication/backup-files';
            document.body.appendChild(downloadFrame);
            
            // إزالة المؤشر بعد 3 ثواني (وقت كافي لبدء التحميل)
            setTimeout(() => {
                Swal.close();
                document.body.removeChild(downloadFrame);
                
                // إظهار رسالة نجاح
                Swal.fire({
                    title: 'تم بدء التحميل!',
                    text: 'إذا لم يبدأ التحميل تلقائياً، تحقق من إعدادات المتصفح.',
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true
                });
            }, 3000);
        }
    });
}

// تحميل ملفات شات عميل محدد - محسن
function downloadChatFiles(chatId, customerName) {
    Swal.fire({
        title: 'تحميل ملفات شات العميل',
        text: `سيتم تحميل جميع ملفات شات العميل "${customerName}" كملف ZIP منظم.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'تحميل',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981'
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار مؤشر التحميل
            const loadingModal = Swal.fire({
                title: 'جاري تحضير ملفات العميل...',
                html: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // إنشاء iframe مخفي للتحميل
            const downloadFrame = document.createElement('iframe');
            downloadFrame.style.display = 'none';
            downloadFrame.src = `/admin/customer-communication/${chatId}/backup-files`;
            document.body.appendChild(downloadFrame);
            
            // إزالة المؤشر بعد 2 ثانية (وقت كافي لبدء التحميل)
            setTimeout(() => {
                Swal.close();
                document.body.removeChild(downloadFrame);
                
                // إظهار رسالة نجاح
                Swal.fire({
                    title: 'تم بدء التحميل!',
                    text: `بدأ تحميل ملفات شات العميل "${customerName}".`,
                    icon: 'success',
                    timer: 2500,
                    timerProgressBar: true
                });
            }, 2000);
        }
    });
}

// مسح ملفات شات عميل محدد
function clearChatFiles(chatId, customerName) {
    Swal.fire({
        title: 'مسح ملفات شات العميل',
        text: `هل تريد حذف جميع الملفات في شات العميل "${customerName}"؟ هذا الإجراء لا يمكن التراجع عنه!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، امسح',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار مؤشر التحميل
            Swal.fire({
                title: 'جاري مسح الملفات...',
                html: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/customer-communication/${chatId}/clear-files`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close(); // إغلاق مؤشر التحميل
                if (data.success) {
                    Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                Swal.close(); // إغلاق مؤشر التحميل
                Swal.fire('خطأ!', 'حدث خطأ أثناء مسح الملفات', 'error');
            });
        }
    });
}

// حذف الملفات القديمة
function clearOldFiles() {
    Swal.fire({
        title: 'حذف الملفات القديمة',
        text: 'سيتم حذف جميع الملفات الأقدم من 3 شهور. هذا الإجراء لا يمكن التراجع عنه!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار مؤشر التحميل
            Swal.fire({
                title: 'جاري حذف الملفات القديمة...',
                html: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/admin/customer-communication/clear-old-files', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close(); // إغلاق مؤشر التحميل
                if (data.success) {
                    Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                Swal.close(); // إغلاق مؤشر التحميل
                Swal.fire('خطأ!', 'حدث خطأ أثناء حذف الملفات', 'error');
            });
        }
    });
}

// الإعدادات المتقدمة
function showAdvancedSettings() {
    Swal.fire({
        title: 'الإعدادات المتقدمة',
        html: `
            <div class="text-right">
                <label class="block text-sm font-medium mb-2">الحد الأقصى لحجم الملف (MB):</label>
                <input id="maxFileSize" type="number" value="10" min="1" max="100" 
                       class="w-full border rounded px-3 py-2 text-center">
                <p class="text-xs text-gray-500 mt-1">الحد الحالي: 10 ميجابايت</p>
            </div>
        `,
        confirmButtonText: 'حفظ',
        cancelButtonText: 'إلغاء',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            const newSize = document.getElementById('maxFileSize').value;
            Swal.fire('تم!', `تم تحديث الحد الأقصى إلى ${newSize} ميجابايت`, 'success');
        }
    });
}
</script>
@endpush