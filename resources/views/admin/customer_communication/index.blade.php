@extends('admin.layouts.app')

@section('title', 'التواصل مع الموظفين بخصوص العملاء')
@section('page-title', 'التواصل مع الموظفين بخصوص العملاء')
@section('page-subtitle', 'متابعة التواصل مع الموظفين المسؤولين عن عملاء المكالمات والزيارات')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">التواصل مع الموظفين بخصوص العملاء</h1>
                    <p class="text-gray-600 mt-1">متابعة ومساعدة الموظفين في التواصل مع العملاء الذين يحتاجون مكالمات أو
                        زيارات</p>
                </div>

                <!-- Filter Form -->
                <form method="GET" class="flex items-center space-x-3 space-x-reverse">
                    <select name="filter" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>جميع العملاء</option>
                        <option value="calls_pending" {{ $filter == 'calls_pending' ? 'selected' : '' }}>عملاء المكالمات
                        </option>
                        <option value="visits_pending" {{ $filter == 'visits_pending' ? 'selected' : '' }}>عملاء الزيارات
                        </option>
                        <option value="high_priority" {{ $filter == 'high_priority' ? 'selected' : '' }}>عملاء عالي الأولوية
                        </option>
                        <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>رسائل جديدة</option>
                        <option value="my_chats" {{ $filter == 'my_chats' ? 'selected' : '' }}>شاتاتي</option>
                    </select>

                    <button type="button" onclick="location.reload()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-sync-alt ml-2"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.customer-communication.storage-management') }}"
                        class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-hdd ml-2"></i>
                        إدارة المساحة
                    </a>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-6">

            <!-- الموظفين المؤهلين -->
            <div
                class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">الموظفين المؤهلين</p>
                        <p class="text-2xl font-bold">{{ $stats['qualified_employees'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- إجمالي العملاء -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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
            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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
            <div
                class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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
            <div
                class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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

            <!-- الشاتات النشطة -->
            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">شاتات نشطة</p>
                        <p class="text-2xl font-bold">{{ $stats['active_chats'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- رسائل جديدة -->
            <div
                class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">رسائل جديدة</p>
                        <p class="text-2xl font-bold">{{ $stats['unread_from_employees'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bell text-lg animate-pulse"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">جدول العملاء</h2>
                <p class="text-sm text-gray-600">قائمة بالعملاء الذين يحتاجون تواصل وحالة كل شات</p>
            </div>

            @if ($customers->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">
                        لا يوجد عملاء مطلوب التواصل معهم حالياً بخصوص مكالمات أو زيارات.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    العميل
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    نوع الطلب
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    وصف العمل
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الموظف المسؤول
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    حالة التواصل
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    آخر نشاط
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($customers as $customer)
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

                                <tr
                                    class="hover:bg-gray-50 transition-colors {{ $isDifficult ? 'bg-red-25 border-r-4 border-red-400' : '' }}">
                                    <!-- العميل -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                                                {{ substr($customer->customer_name, 0, 1) }}
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $customer->customer_name }}</div>
                                                <div class="text-sm text-gray-500 flex items-center">
                                                    <i class="fas fa-phone text-gray-400 ml-1"></i>
                                                    {{ $customer->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- نوع الطلب -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @if ($isDifficult)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                                    عالي الأولوية
                                                </span>
                                            @endif

                                            @if ($isCallRequest)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i class="fas fa-phone ml-1"></i>
                                                    مكالمة
                                                </span>
                                            @endif

                                            @if ($isVisitRequest)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-building ml-1"></i>
                                                    زيارة
                                                </span>
                                            @endif

                                            @if (!$isCallRequest && !$isVisitRequest && !$isDifficult)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    عام
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- وصف العمل -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            <div class="line-clamp-2">{{ $customer->work_description }}</div>
                                        </div>
                                    </td>

                                    <!-- الموظف المسؤول -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($chat && $chat->employee)
                                            <div class="flex items-center">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                                    {{ substr($chat->employee->name, 0, 1) }}
                                                </div>
                                                <div class="mr-2">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $chat->employee->name }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">لم يتم التعيين</span>
                                        @endif
                                    </td>

                                    <!-- حالة التواصل -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($chat)
                                            @php
                                                $statusColor = match ($chat->status) {
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'completed' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };

                                                $statusText = match ($chat->status) {
                                                    'active' => 'جاري التواصل',
                                                    'pending' => 'في الانتظار',
                                                    'completed' => 'تم التواصل',
                                                    default => 'غير محدد',
                                                };
                                            @endphp

                                            <div class="flex flex-col space-y-1">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ $statusText }}
                                                </span>

                                                @if ($unreadCount > 0)
                                                    <div class="flex items-center">
                                                        <span
                                                            class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white rounded-full text-xs font-medium animate-pulse">
                                                            {{ $unreadCount }}
                                                        </span>
                                                        <span class="text-xs text-red-600 font-medium mr-1">رسائل
                                                            جديدة</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                لم يتم البدء
                                            </span>
                                        @endif
                                    </td>

                                    <!-- آخر نشاط -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($chat && $chat->last_message_at)
                                            <div class="flex flex-col">
                                                <span>{{ $chat->last_message_at->diffForHumans() }}</span>
                                                <span
                                                    class="text-xs text-gray-400">{{ $chat->last_message_at->format('Y/m/d H:i') }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">لا يوجد</span>
                                        @endif
                                    </td>

                                    <!-- الإجراءات -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if ($chat)
                                            <a href="{{ route('admin.customer-communication.show', $customer->id) }}"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                @if ($unreadCount > 0)
                                                    <i class="fas fa-bell ml-2 animate-pulse"></i>
                                                    دخول للشات ({{ $unreadCount }})
                                                @else
                                                    <i class="fas fa-comments ml-2"></i>
                                                    دخول للشات
                                                @endif
                                            </a>
                                        @else
                                            <a href="{{ route('admin.customer-communication.show', $customer->id) }}"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                <i class="fas fa-plus ml-2"></i>
                                                إنشاء شات
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if ($customers->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="bg-white rounded-lg shadow-sm border p-4">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .line-clamp-2 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .bg-red-25 {
                background-color: rgba(254, 242, 242, 0.5);
            }

            /* تحسين الجدول للشاشات الصغيرة */
            @media (max-width: 768px) {
                .table-responsive {
                    font-size: 0.875rem;
                }

                .table-responsive td,
                .table-responsive th {
                    padding: 0.5rem !important;
                }
            }
        </style>
    @endpush

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
                const audioContext = new(window.AudioContext || window.webkitAudioContext)();
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
            let lastUnreadCount = {{ $stats['unread_from_employees'] }};
            setInterval(function() {
                if (document.visibilityState === 'visible') {
                    fetch('/admin/customer-communication/stats/unread-count')
                        .then(response => response.json())
                        .then(data => {
                            if (data.count > lastUnreadCount) {
                                playNotificationSound();
                                // Update any visible counters
                                document.querySelectorAll('.unread-counter').forEach(counter => {
                                    counter.textContent = data.count;
                                    counter.classList.add('animate-pulse');
                                });

                                // Show browser notification if supported
                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification("رسائل جديدة من الموظفين", {
                                        body: `لديك ${data.count} رسائل جديدة من الموظفين`,
                                        icon: "/favicon.ico"
                                    });
                                }
                            }
                            lastUnreadCount = data.count;
                        })
                        .catch(error => console.log('Unread count check failed:', error));
                }
            }, 60000);

            // Request notification permission on page load
            document.addEventListener('DOMContentLoaded', function() {
                if ("Notification" in window && Notification.permission === "default") {
                    Notification.requestPermission();
                }
            });
        </script>
    @endpush
@endsection
