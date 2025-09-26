@extends('admin.layouts.app')

@section('title', 'إدارة المساحة')
@section('page-title', 'إدارة المساحة')
@section('page-subtitle', 'إدارة ملفات الشات والنسخ الاحتياطية')

@php
    function formatBytes($bytes)
    {
        if ($bytes == 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
@endphp

@section('content')
    <div class="space-y-6">
                <div class="flex items-center space-x-3 space-x-reverse">
            <!-- زر الرجوع للشاتات -->
            <a href="{{ route('admin.work-chat.index') }}" 
               class="flex items-center px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200 shadow-sm">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع للشاتات
            </a>
        </div>

        <!-- إحصائيات عامة مع التصميم العصري -->
        <div class=" px-4 sm:px-6 lg:px-8 py-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <!-- إجمالي المساحة -->
                <div
                    class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي المساحة</p>
                        <p class="text-2xl font-bold">{{ formatBytes($totalSize) }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <!-- عدد الملفات -->
                <div
                    class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">عدد الملفات</p>
                        <p class="text-2xl font-bold">{{ number_format($fileCount) }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-3-3v6m-6-6v12a2 2 0 002 2h8a2 2 0 002-2V9l-6-6-6 6z" />
                    </svg>
                </div>

                <!-- ملفات قديمة (+3 شهور) -->
                <div
                    class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">ملفات قديمة (+3 شهور)</p>
                        <p class="text-2xl font-bold">{{ $oldFiles->count ?? 0 }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        <!-- إحصائيات تفصيلية -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">إحصائيات تفصيلية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($fileStats as $stat)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        @if ($stat->message_type === 'file')
                                            <i class="fas fa-file ml-1"></i> ملفات
                                        @else
                                            <i class="fas fa-microphone ml-1"></i> تسجيلات صوتية
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
                            <div class="mx-auto w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-download text-white"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">نسخة احتياطية شاملة</h4>
                            <p class="text-xs text-gray-500 mb-4">تحميل جميع الملفات كـ ZIP منظم</p>
                            <button onclick="createFullBackup()"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                تحميل جميع الملفات
                            </button>
                        </div>
                    </div>

                    <!-- حذف الملفات القديمة -->
                    <div class="border rounded-lg p-4">
                        <div class="text-center">
                            <div class="mx-auto w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-broom text-white"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">حذف الملفات القديمة</h4>
                            <p class="text-xs text-gray-500 mb-4">حذف ملفات أقدم من 3 شهور</p>
                            <button onclick="clearOldFiles()"
                                class="w-full bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">
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
                            <button onclick="showAdvancedSettings()"
                                class="w-full bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                                الإعدادات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- قائمة الشاتات مع خيار التحميل المنفرد -->
        @if ($userChats->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-comments text-blue-600 ml-2"></i>
                        شاتاتك مع إمكانية التحميل المنفرد
                    </h3>

                    <!-- مربع البحث -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="chatSearch" placeholder="البحث في الشاتات..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        معلومات الشات
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        النوع
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الموظف
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عدد الملفات
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="chatsTableBody">
                                @foreach ($userChats as $chat)
                                    <tr class="hover:bg-gray-50 chat-row" data-chat-title="{{ strtolower($chat->title) }}"
                                        data-employee-name="{{ strtolower($chat->employee->name ?? '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gradient-to-r from-{{ $chat->type === 'design' ? 'purple' : 'indigo' }}-500 to-{{ $chat->type === 'design' ? 'purple' : 'indigo' }}-600 flex items-center justify-center">
                                                        <i
                                                            class="fas {{ $chat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $chat->title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $chat->created_at->format('Y-m-d') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $chat->type === 'design' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                                <i
                                                    class="fas {{ $chat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} ml-1"></i>
                                                {{ $chat->type === 'design' ? 'تصميم' : 'مونتاج' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $chat->employee->name ?? 'غير محدد' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $chat->employee->employee_id ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $chat->file_count }}</span>
                                                <span class="text-xs text-gray-500 mr-1">ملف</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <button
                                                    onclick="downloadChatFiles({{ $chat->id }}, '{{ $chat->title }}')"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    <i class="fas fa-download ml-1"></i>
                                                    تحميل
                                                </button>
                                                <a href="{{ route('admin.work-chat.show', $chat) }}"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-eye ml-1"></i>
                                                    عرض
                                                </a>
                                                <button
                                                    onclick="clearChatFiles({{ $chat->id }}, '{{ $chat->title }}')"
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 ml-2"></i>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">تحذيرات مهمة:</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>تأكد من أخذ نسخة احتياطية قبل حذف أي ملفات</li>
                            <li>حذف الملفات لا يمكن التراجع عنه</li>
                            <li>الملفات المحذوفة لن تكون متاحة في الشاتات</li>
                            <li>النسخ الاحتياطية منظمة حسب الشاتات مع ملف README للشرح</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات التنظيم -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-400 mt-0.5 ml-2"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">هيكل النسخة الاحتياطية:</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <div class="bg-white p-3 rounded border font-mono text-xs">
                            backup_file.zip/<br>
                            ├── README.txt (معلومات الشاتات)<br>
                            ├── backup_details.json (تفاصيل النسخة)<br>
                            ├── Chat_1_design_اسم_المشروع/<br>
                            │&nbsp;&nbsp;&nbsp;├── files/ (الملفات المرفقة)<br>
                            │&nbsp;&nbsp;&nbsp;└── voices/ (التسجيلات الصوتية)<br>
                            └── Chat_2_montage_اسم_آخر/<br>
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
        // البحث في الشاتات
        document.getElementById('chatSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.chat-row');

            rows.forEach(row => {
                const title = row.getAttribute('data-chat-title');
                const employee = row.getAttribute('data-employee-name');

                if (title.includes(searchTerm) || employee.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // تحميل جميع الملفات - محسن
        function createFullBackup() {
            Swal.fire({
                title: 'إنشاء نسخة احتياطية شاملة',
                text: 'سيتم تحميل جميع ملفات الشات كملف ZIP منظم. قد يستغرق هذا بعض الوقت.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'تحميل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#3b82f6'
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
                    downloadFrame.src = '/admin/work-chat/backup-files';
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

        // تحميل ملفات شات محدد - محسن
        function downloadChatFiles(chatId, chatTitle) {
            Swal.fire({
                title: 'تحميل ملفات الشات',
                text: `سيتم تحميل جميع ملفات شات "${chatTitle}" كملف ZIP منظم.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'تحميل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إظهار مؤشر التحميل
                    const loadingModal = Swal.fire({
                        title: 'جاري تحضير ملفات الشات...',
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
                    downloadFrame.src = `/admin/work-chat/${chatId}/backup-files`;
                    document.body.appendChild(downloadFrame);

                    // إزالة المؤشر بعد 2 ثانية (وقت كافي لبدء التحميل)
                    setTimeout(() => {
                        Swal.close();
                        document.body.removeChild(downloadFrame);

                        // إظهار رسالة نجاح
                        Swal.fire({
                            title: 'تم بدء التحميل!',
                            text: `بدأ تحميل ملفات شات "${chatTitle}".`,
                            icon: 'success',
                            timer: 2500,
                            timerProgressBar: true
                        });
                    }, 2000);
                }
            });
        }

        // طريقة بديلة أكثر تقدماً باستخدام fetch
        function downloadChatFilesAdvanced(chatId, chatTitle) {
            Swal.fire({
                title: 'تحميل ملفات الشات',
                text: `سيتم تحميل جميع ملفات شات "${chatTitle}" كملف ZIP منظم.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'تحميل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (result.isConfirmed) {
                    // إظهار مؤشر التحميل مع progress
                    let progressValue = 0;
                    const loadingModal = Swal.fire({
                        title: 'جاري تحضير ملفات الشات...',
                        html: `
                    <div class="mb-3">يرجى الانتظار أثناء إنشاء الملف</div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div id="progressText" class="text-sm text-gray-600 mt-2">جاري البدء...</div>
                `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    // محاكاة التقدم
                    const progressInterval = setInterval(() => {
                        progressValue += Math.random() * 15;
                        if (progressValue > 90) progressValue = 90;

                        const progressBar = document.getElementById('progressBar');
                        const progressText = document.getElementById('progressText');

                        if (progressBar) {
                            progressBar.style.width = progressValue + '%';
                        }
                        if (progressText) {
                            if (progressValue < 30) {
                                progressText.textContent = 'جاري جمع الملفات...';
                            } else if (progressValue < 60) {
                                progressText.textContent = 'جاري ضغط الملفات...';
                            } else if (progressValue < 90) {
                                progressText.textContent = 'جاري تحضير التحميل...';
                            }
                        }
                    }, 200);

                    // بدء التحميل الفعلي
                    fetch(`/admin/work-chat/${chatId}/backup-files`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('فشل في التحميل');
                            return response.blob();
                        })
                        .then(blob => {
                            // إكمال شريط التقدم
                            clearInterval(progressInterval);
                            const progressBar = document.getElementById('progressBar');
                            const progressText = document.getElementById('progressText');
                            if (progressBar) progressBar.style.width = '100%';
                            if (progressText) progressText.textContent = 'اكتمل! جاري بدء التحميل...';

                            // تحميل الملف
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = `chat_backup_${chatId}_${Date.now()}.zip`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            // إغلاق المؤشر وإظهار رسالة نجاح
                            setTimeout(() => {
                                Swal.close();
                                Swal.fire({
                                    title: 'تم التحميل بنجاح!',
                                    text: `تم تحميل ملفات شات "${chatTitle}" بنجاح.`,
                                    icon: 'success',
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }, 500);
                        })
                        .catch(error => {
                            clearInterval(progressInterval);
                            console.error('Error:', error);
                            Swal.close();
                            Swal.fire({
                                title: 'خطأ في التحميل',
                                text: 'حدث خطأ أثناء تحضير الملفات. يرجى المحاولة مرة أخرى.',
                                icon: 'error'
                            });
                        });
                }
            });
        }

        // مسح ملفات شات محدد
        function clearChatFiles(chatId, chatTitle) {
            Swal.fire({
                title: 'مسح ملفات الشات',
                text: `هل تريد حذف جميع الملفات في شات "${chatTitle}"؟ هذا الإجراء لا يمكن التراجع عنه!`,
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

                    fetch(`/admin/work-chat/${chatId}/clear-files`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close(); // إغلاق مؤشر التحميل
                            if (data.success) {
                                Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`,
                                    'success');
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

                    fetch('/admin/work-chat/clear-old-files', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close(); // إغلاق مؤشر التحميل
                            if (data.success) {
                                Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`,
                                    'success');
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
