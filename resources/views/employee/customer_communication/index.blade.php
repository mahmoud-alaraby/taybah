@extends('employee.layouts.app')

@section('title', 'التواصل بخصوص العملاء')
@section('page-title', 'التواصل بخصوص العملاء')
@section('page-subtitle', 'نظام التواصل بين الموظف والإدارة حول عملاء المكالمات والزيارات')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">التواصل بخصوص العملاء</h1>
                <p class="text-gray-600 mt-1">تواصل مع الإدارة بخصوص العملاء الذين يحتاجون مكالمات أو زيارات</p>
            </div>
            
            <!-- Filter Form -->
            <form method="GET" class="flex items-center space-x-3 space-x-reverse">
                <select name="filter" onchange="this.form.submit()" 
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>جميع العملاء</option>
                    <option value="calls_pending" {{ $filter == 'calls_pending' ? 'selected' : '' }}>عملاء المكالمات</option>
                    <option value="visits_pending" {{ $filter == 'visits_pending' ? 'selected' : '' }}>عملاء الزيارات</option>
                    <option value="high_priority" {{ $filter == 'high_priority' ? 'selected' : '' }}>عملاء عالي الأولوية</option>
                    <option value="my_chats" {{ $filter == 'my_chats' ? 'selected' : '' }}>شاتاتي</option>
                </select>
                
                <button type="button" onclick="location.reload()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        
        <!-- إجمالي العملاء -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">إجمالي العملاء</p>
                    <p class="text-2xl font-bold">{{ $stats['total_customers'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
        </div>

        <!-- عملاء المكالمات -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عملاء المكالمات</p>
                    <p class="text-2xl font-bold">{{ $stats['calls_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-phone text-lg"></i>
                </div>
            </div>
        </div>

        <!-- عملاء الزيارات -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عملاء الزيارات</p>
                    <p class="text-2xl font-bold">{{ $stats['visits_requests'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-lg"></i>
                </div>
            </div>
        </div>

        <!-- عالي الأولوية -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عالي الأولوية</p>
                    <p class="text-2xl font-bold">{{ $stats['high_priority'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
            </div>
        </div>

        <!-- شاتاتي -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">شاتاتي</p>
                    <p class="text-2xl font-bold">{{ $stats['my_chats'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-lg"></i>
                </div>
            </div>
        </div>

        <!-- رسائل غير مقروءة -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">رسائل جديدة</p>
                    <p class="text-2xl font-bold">{{ $stats['unread_from_admin'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bell text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العميل</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">رقم الهاتف</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">وصف العمل</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">التصنيفات</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">حالة الشات</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">آخر رسالة</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">رسائل جديدة</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        @php
                            $chat = $customer->customerChat->first();
                            $unreadCount = $customer->unread_count ?? 0;
                            $classifications = $customer->customer_classifications ?? [];
                            if (is_string($classifications)) {
                                $classifications = json_decode($classifications, true) ?? [];
                            }
                            
                            $isCallRequest = in_array('requested_call', $classifications);
                            $isVisitRequest = in_array('requested_visit', $classifications);
                            $isDifficult = in_array('difficult_customer', $classifications);
                        @endphp
                        
                        <tr class="hover:bg-gray-50 transition-colors {{ $isDifficult ? 'bg-red-50' : '' }}">
                            <!-- معلومات العميل -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r {{ $isDifficult ? 'from-red-500 to-red-600' : 'from-blue-500 to-blue-600' }} flex items-center justify-center text-white font-semibold">
                                            {{ substr($customer->customer_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $customer->customer_name }}</div>
                                        <div class="text-sm text-gray-500">تم الإضافة: {{ $customer->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- رقم الهاتف -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $customer->phone }}</div>
                                <div class="text-xs text-gray-500">{{ $customer->phone ? 'مؤكد' : 'غير مؤكد' }}</div>
                            </td>
                            
                            <!-- وصف العمل -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ Str::limit($customer->work_description, 60) }}
                                </div>
                                @if(strlen($customer->work_description) > 60)
                                    <div class="text-xs text-blue-600 cursor-pointer hover:text-blue-800" 
                                         onclick="showFullDescription('{{ addslashes($customer->work_description) }}')">
                                        عرض الكامل...
                                    </div>
                                @endif
                            </td>
                            
                            <!-- التصنيفات -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-wrap justify-center gap-1">
                                    @if($isDifficult)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle ml-1"></i>
                                            عاجل
                                        </span>
                                    @endif
                                    
                                    @if($isCallRequest)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-phone ml-1"></i>
                                            مكالمة
                                        </span>
                                    @endif
                                    
                                    @if($isVisitRequest)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-building ml-1"></i>
                                            زيارة
                                        </span>
                                    @endif
                                    
                                    @if(!$isDifficult && !$isCallRequest && !$isVisitRequest)
                                        <span class="text-sm text-gray-500">لا توجد</span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- حالة الشات -->
                            <td class="px-6 py-4 text-center">
                                @if($chat)
                                    @php
                                        $statusColor = match($chat->status) {
                                            'active' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        
                                        $statusText = match($chat->status) {
                                            'active' => 'نشط',
                                            'pending' => 'في الانتظار',
                                            'completed' => 'تم التواصل',
                                            default => 'غير محدد'
                                        };
                                        
                                        $statusIcon = match($chat->status) {
                                            'active' => 'fa-comments',
                                            'pending' => 'fa-clock',
                                            'completed' => 'fa-check-circle',
                                            default => 'fa-question-circle'
                                        };
                                    @endphp
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        <i class="fas {{ $statusIcon }} ml-1"></i>
                                        {{ $statusText }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-plus ml-1"></i>
                                        لم يبدأ
                                    </span>
                                @endif
                            </td>
                            
                            <!-- آخر رسالة -->
                            <td class="px-6 py-4 text-center">
                                @if($chat && $chat->last_message_at)
                                    <div class="text-sm text-gray-900">
                                        {{ $chat->last_message_at->format('Y/m/d') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $chat->last_message_at->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-blue-600">
                                        {{ $chat->last_message_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">لا توجد رسائل</span>
                                @endif
                            </td>
                            
                            <!-- رسائل جديدة -->
                            <td class="px-6 py-4 text-center">
                                @if($unreadCount > 0)
                                    <div class="inline-flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-red-500 text-white rounded-full text-sm font-bold animate-pulse">
                                            {{ $unreadCount }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-red-600 font-medium mt-1">رسائل جديدة</div>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-400 rounded-full text-sm">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">مقروءة</div>
                                @endif
                            </td>
                            
                            <!-- الإجراءات -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('employee.customer-communication.show', $customer->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                                        @if($unreadCount > 0)
                                            <i class="fas fa-bell ml-2"></i>
                                            دخول ({{ $unreadCount }})
                                        @elseif($chat && $chat->status === 'active')
                                            <i class="fas fa-comments ml-2"></i>
                                            متابعة
                                        @else
                                            <i class="fas fa-plus ml-2"></i>
                                            بدء الشات
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء</h3>
                                    <p class="text-gray-500">
                                        لا يوجد عملاء مطلوب التواصل معهم حالياً بخصوص مكالمات أو زيارات.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="mt-8 flex justify-center">
            <div class="bg-white rounded-lg shadow-sm border p-4">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal for full description -->
<div id="descriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">وصف العمل كاملاً</h3>
            <div id="fullDescription" class="mt-2 px-7 py-3 text-right text-gray-700 leading-relaxed"></div>
            <div class="items-center px-4 py-3">
                <button id="closeModal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// عرض الوصف الكامل
function showFullDescription(description) {
    document.getElementById('fullDescription').innerText = description;
    document.getElementById('descriptionModal').classList.remove('hidden');
}

// إغلاق المودال
document.getElementById('closeModal').onclick = function() {
    document.getElementById('descriptionModal').classList.add('hidden');
}

// إغلاق المودال عند النقر خارجه
document.getElementById('descriptionModal').onclick = function(event) {
    if (event.target === this) {
        this.classList.add('hidden');
    }
}

// Auto refresh every 30 seconds
setInterval(function() {
    if (document.visibilityState === 'visible') {
        // Check for new messages indicator
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.ok) {
                // Update unread counts if needed
                console.log('Checking for updates...');
            }
        }).catch(error => {
            console.log('Update check failed:', error);
        });
    }
}, 30000);

// Notification sound for new messages
function playNotificationSound() {
    // Create a simple notification sound
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    
    oscillator.start();
    oscillator.stop(audioContext.currentTime + 0.1);
}

// Check for new messages every minute
let lastUnreadCount = {{ $stats['unread_from_admin'] }};
setInterval(function() {
    if (document.visibilityState === 'visible') {
        fetch('/employee/customer-communication/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.count > lastUnreadCount) {
                    playNotificationSound();
                    // Update any visible counters
                    document.querySelectorAll('.unread-counter').forEach(counter => {
                        counter.textContent = data.count;
                        counter.classList.add('animate-pulse');
                    });
                }
                lastUnreadCount = data.count;
            })
            .catch(error => console.log('Unread count check failed:', error));
    }
}, 60000);
</script>
@endpush
@endsection