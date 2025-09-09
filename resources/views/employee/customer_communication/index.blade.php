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

    <!-- Customers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
            
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-lg transition-all duration-300 overflow-hidden group">
                
                <!-- Card Header -->
                <div class="p-4 border-b bg-gradient-to-r {{ $isDifficult ? 'from-red-50 to-red-100' : ($isCallRequest ? 'from-orange-50 to-orange-100' : 'from-green-50 to-green-100') }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg mb-1 truncate">{{ $customer->customer_name }}</h3>
                            <p class="text-gray-600 text-sm flex items-center">
                                <i class="fas fa-phone text-gray-400 ml-2"></i>
                                {{ $customer->phone }}
                            </p>
                        </div>
                        
                        <!-- Status Badges -->
                        <div class="flex flex-col space-y-1">
                            @if($isDifficult)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    عالي الأولوية
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
                        </div>
                    </div>
                </div>
                
                <!-- Card Content -->
                <div class="p-4">
                    <div class="space-y-3">
                        <!-- Work Description -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <i class="fas fa-clipboard-list text-gray-400 ml-2"></i>
                                {{ Str::limit($customer->work_description, 80) }}
                            </p>
                        </div>
                        
                        <!-- Chat Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                @if($chat)
                                    @php
                                        $statusColor = match($chat->status) {
                                            'active' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        
                                        $statusText = match($chat->status) {
                                            'active' => 'جاري التواصل',
                                            'pending' => 'في الانتظار',
                                            'completed' => 'تم التواصل',
                                            default => 'غير محدد'
                                        };
                                    @endphp
                                    
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                    
                                    @if($chat->last_message_at)
                                        <span class="text-xs text-gray-500">
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        لم يتم البدء
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Unread Messages Badge -->
                            @if($unreadCount > 0)
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white rounded-full text-xs font-medium animate-pulse">
                                        {{ $unreadCount }}
                                    </span>
                                    <span class="text-xs text-red-600 font-medium mr-1">رسائل جديدة</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Card Actions -->
                <div class="px-4 pb-4">
                    <a href="{{ route('employee.customer-communication.show', $customer->id) }}" 
                       class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center group-hover:shadow-lg">
                        @if($unreadCount > 0)
                            <i class="fas fa-bell ml-2 animate-pulse"></i>
                            دخول للشات ({{ $unreadCount }})
                        @elseif($chat && $chat->status === 'active')
                            <i class="fas fa-comments ml-2"></i>
                            متابعة الشات
                        @else
                            <i class="fas fa-plus ml-2"></i>
                            بدء التواصل مع الإدارة
                        @endif
                    </a>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">
                        لا يوجد عملاء مطلوب التواصل معهم حالياً بخصوص مكالمات أو زيارات.
                    </p>
                </div>
            </div>
        @endforelse
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

@push('scripts')
<script>
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