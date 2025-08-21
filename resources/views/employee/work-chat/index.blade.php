@extends('employee.layouts.app')

@section('title', 'شاتات العمل')
@section('page-title', 'شاتات العمل')
@section('page-subtitle', 'التواصل مع الإدارة حول مهام العمل')

@section('content')
<div class="space-y-6">
    <!-- Header with Filter Tabs -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-comments ml-3"></i>
                شاتات العمل
            </h2>
            <p class="text-blue-100 text-sm mt-1">تواصل مع الإدارة حول مشاريع العمل</p>
        </div>
        
        <div class="p-4 border-b bg-gray-50">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('employee.design-follow-up') }}" 
                   class="px-4 py-2 rounded-lg transition-colors {{ !$type ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border' }}">
                    <i class="fas fa-comments ml-2"></i>
                    جميع الشاتات
                </a>
                
                @if(auth('employee')->user()->hasPermission('design_follow_up'))
                    <a href="{{ route('employee.design-follow-up', ['type' => 'design']) }}" 
                       class="px-4 py-2 rounded-lg transition-colors {{ $type === 'design' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border' }}">
                        <i class="fas fa-pencil-ruler ml-2"></i>
                        شاتات التصميم
                        @php
                            $designCount = $chats->where('type', 'design')->count();
                        @endphp
                        @if($designCount > 0)
                            <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs ml-2">{{ $designCount }}</span>
                        @endif
                    </a>
                @endif
                
                @if(auth('employee')->user()->hasPermission('montage_follow_up'))
                    <a href="{{ route('employee.montage-follow-up', ['type' => 'montage']) }}" 
                       class="px-4 py-2 rounded-lg transition-colors {{ $type === 'montage' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border' }}">
                        <i class="fas fa-video ml-2"></i>
                        شاتات المونتاج
                        @php
                            $montageCount = $chats->where('type', 'montage')->count();
                        @endphp
                        @if($montageCount > 0)
                            <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs ml-2">{{ $montageCount }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    @if($chats->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Chats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-comment text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">إجمالي الشاتات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $chats->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">رسائل غير مقروءة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $chats->sum('unread_count') }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Chats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">شاتات نشطة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $chats->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Chats List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($chats->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($chats as $chat)
                    <div class="p-6 hover:bg-gray-50 transition-colors chat-item {{ $chat->unread_count > 0 ? 'bg-blue-50 border-r-4 border-blue-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <!-- Chat Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-{{ $chat->type === 'design' ? 'purple' : 'indigo' }}-500 to-{{ $chat->type === 'design' ? 'purple' : 'indigo' }}-600 flex items-center justify-center shadow-lg">
                                            <i class="fas {{ $chat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} text-white text-lg"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Chat Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 space-x-reverse mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $chat->title }}</h3>
                                            @if($chat->unread_count > 0)
                                                <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium animate-pulse">
                                                    {{ $chat->unread_count }} جديدة
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center space-x-3 space-x-reverse text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <i class="fas fa-user ml-1"></i>
                                                مع: {{ $chat->admin->name ?? 'مدير' }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-clock ml-1"></i>
                                                {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : 'لا توجد رسائل' }}
                                            </span>
                                        </div>
                                        
                                        @if($chat->messages->first())
                                            <p class="text-sm text-gray-600 mt-2 flex items-center">
                                                <i class="fas fa-comment-dots ml-2 text-gray-400"></i>
                                                آخر رسالة: 
                                                @if($chat->messages->first()->message_type === 'text')
                                                    {{ Str::limit($chat->messages->first()->content, 50) }}
                                                @elseif($chat->messages->first()->message_type === 'file')
                                                    <span class="flex items-center text-blue-600">
                                                        <i class="fas fa-file ml-1"></i>
                                                        ملف مرفق
                                                    </span>
                                                @elseif($chat->messages->first()->message_type === 'voice')
                                                    <span class="flex items-center text-purple-600">
                                                        <i class="fas fa-microphone ml-1"></i>
                                                        رسالة صوتية
                                                    </span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chat Actions -->
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $chat->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($chat->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    @if($chat->status === 'active')
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                        نشط
                                    @elseif($chat->status === 'completed')
                                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                        مكتمل
                                    @else
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        مؤرشف
                                    @endif
                                </span>
                                
                                <!-- Open Chat Button -->
                                <a href="{{ route('employee.work-chat.show', $chat) }}" 
                                   class="inline-flex items-center px-4 py-2 btn-gradient text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                                    <i class="fas fa-comments ml-2"></i>
                                    فتح الشات
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="max-w-sm mx-auto">
                    <div class="mb-6">
                        <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-comments text-white text-3xl"></i>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">لا توجد شاتات</h3>
                    
                    <p class="text-gray-600 mb-6">
                        @if($type)
                            لا توجد شاتات {{ $type === 'design' ? 'تصميم' : 'مونتاج' }} حتى الآن
                        @else
                            لا توجد شاتات عمل حتى الآن
                        @endif
                    </p>
                    
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center text-blue-800 text-sm">
                            <i class="fas fa-info-circle ml-2"></i>
                            سيتم إنشاء الشاتات من قبل الإدارة حسب المهام المطلوبة
                        </div>
                    </div>
                    
                    <!-- Available Permissions Info -->
                    <div class="text-sm text-gray-500 space-y-2">
                        <p class="font-medium">الصلاحيات المتاحة لك:</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            @if(auth('employee')->user()->hasPermission('design_follow_up'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs">
                                    <i class="fas fa-pencil-ruler ml-1"></i>
                                    متابعة التصميم
                                </span>
                            @endif
                            @if(auth('employee')->user()->hasPermission('montage_follow_up'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs">
                                    <i class="fas fa-video ml-1"></i>
                                    متابعة المونتاج
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Info & Guidelines -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 ml-2"></i>
            دليل استخدام شاتات العمل
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-comment text-white text-xs"></i>
                    </div>
                    <h5 class="font-medium text-gray-900">الرسائل</h5>
                </div>
                <p class="text-gray-600">يمكنك إرسال رسائل نصية وصوتية ومرفقات للتواصل مع الإدارة حول المشاريع</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-file text-white text-xs"></i>
                    </div>
                    <h5 class="font-medium text-gray-900">المرفقات</h5>
                </div>
                <p class="text-gray-600">الحد الأقصى للملف 10 ميجابايت. يمكن رفع الصور والمستندات والملفات المختلفة</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-bell text-white text-xs"></i>
                    </div>
                    <h5 class="font-medium text-gray-900">الإشعارات</h5>
                </div>
                <p class="text-gray-600">ستظهر لك الرسائل الجديدة فوراً مع إشعارات صوتية عند وصول رسائل من الإدارة</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-link text-white text-xs"></i>
                    </div>
                    <h5 class="font-medium text-gray-900">المشاركة</h5>
                </div>
                <p class="text-gray-600">جميع الملفات تحصل على رابط مباشر يمكن مشاركته ونسخه بسهولة</p>
            </div>
        </div>
        
        <!-- Tips Section -->
        <div class="mt-6 p-4 bg-white rounded-lg border border-blue-200">
            <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                <i class="fas fa-star text-yellow-500 ml-2"></i>
                نصائح مهمة
            </h5>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 ml-2 mt-0.5 flex-shrink-0"></i>
                    تأكد من قراءة جميع الرسائل والرد عليها في الوقت المناسب
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 ml-2 mt-0.5 flex-shrink-0"></i>
                    استخدم أسماء واضحة ووصفية عند رفع الملفات
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 ml-2 mt-0.5 flex-shrink-0"></i>
                    يتم تحديث الرسائل تلقائياً كل 5 ثواني
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 ml-2 mt-0.5 flex-shrink-0"></i>
                    يمكنك معاينة الملفات قبل تحميلها أو مشاركتها
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
/* تحسينات CSS */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

.chat-item:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease-in-out;
}

.btn-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: all 0.3s ease;
}

.btn-gradient:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    transform: translateY(-1px);
}

/* تأثيرات إضافية */
.transition-colors {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
}

.shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث الصفحة كل 30 ثانية للحصول على آخر الشاتات
    setInterval(function() {
        // تحقق من وجود رسائل جديدة عبر AJAX
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // استخراج عدد الرسائل غير المقروءة من الاستجابة
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newUnreadElements = doc.querySelectorAll('.bg-red-500');
            const currentUnreadElements = document.querySelectorAll('.bg-red-500');
            
            // إذا كان هناك رسائل جديدة، قم بإعادة تحميل الصفحة
            if (newUnreadElements.length !== currentUnreadElements.length) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.log('تعذر تحديث الشاتات:', error);
        });
    }, 30000); // كل 30 ثانية
});

// طلب إذن الإشعارات
if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
}
</script>
@endpush
@endsection