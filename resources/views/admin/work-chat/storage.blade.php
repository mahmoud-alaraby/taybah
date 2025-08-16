@extends('admin.layouts.app')

@section('title', 'إدارة المساحة')
@section('page-title', 'إدارة المساحة')
@section('page-subtitle', 'إدارة ملفات الشات والنسخ الاحتياطية')

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
    <!-- إحصائيات عامة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-hdd text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">إجمالي المساحة</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ formatBytes($totalSize) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-file text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">عدد الملفات</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($fileCount) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-medium text-gray-500">ملفات قديمة (+3 شهور)</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $oldFiles->count ?? 0 }}</p>
                </div>
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
                
                <!-- نسخة احتياطية -->
                <div class="border rounded-lg p-4">
                    <div class="text-center">
                        <div class="mx-auto w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-download text-white"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">نسخة احتياطية</h4>
                        <p class="text-xs text-gray-500 mb-4">تحميل جميع الملفات كـ ZIP</p>
                        <button onclick="createBackup()" class="w-full bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                            تحميل النسخة الاحتياطية
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
                        <button onclick="clearOldFiles()" class="w-full bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createBackup() {
    Swal.fire({
        title: 'إنشاء نسخة احتياطية',
        text: 'سيتم تحميل جميع ملفات الشات كملف ZIP. قد يستغرق هذا بعض الوقت.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'تحميل',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/admin/work-chat/backup-files';
        }
    });
}

function clearOldFiles() {
    Swal.fire({
        title: 'حذف الملفات القديمة',
        text: 'سيتم حذف جميع الملفات الأقدم من 3 شهور. هذا الإجراء لا يمكن التراجع عنه!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc143c',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/work-chat/clear-old-files', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`, 'success');
                    location.reload();
                }
            });
        }
    });
}

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