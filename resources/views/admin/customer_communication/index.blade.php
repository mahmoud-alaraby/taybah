@extends('admin.layouts.app')

@section('title', 'التواصل مع الموظفين بخصوص العملاء')
@section('page-title', 'التواصل مع الموظفين بخصوص العملاء')
@section('page-subtitle', 'متابعة التواصل مع الموظفين المسؤولين عن عملاء المكالمات والزيارات')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">التواصل مع الموظفين بخصوص العملاء</h1>
                    <p class="text-gray-600 mt-1">متابعة ومساعدة الموظفين في التواصل مع العملاء الذين يحتاجون مكالمات أو زيارات</p>
                </div>

                <!-- إنشاء شات جديد - في الزاوية اليمنى -->
                <div class="flex justify-start lg:justify-end">
                    <button id="createNewChatBtn"
                            class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold text-lg transition-all duration-200 shadow-lg hover:shadow-xl flex items-center transform hover:scale-105">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء شات جديد
                    </button>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="mt-4 pt-4 border-t border-gray-100">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <select name="filter" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>جميع العملاء</option>
                        <option value="calls_pending" {{ $filter == 'calls_pending' ? 'selected' : '' }}>عملاء المكالمات</option>
                        <option value="visits_pending" {{ $filter == 'visits_pending' ? 'selected' : '' }}>عملاء الزيارات</option>
                        <option value="high_priority" {{ $filter == 'high_priority' ? 'selected' : '' }}>عملاء عالي الأولوية</option>
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
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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

            <!-- الشاتات النشطة -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white shadow-lg hover:shadow-xl transition-all">
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

        <!-- الشاتات النشطة - قائمة كاملة -->
        @php
            $activeChats = $customers->filter(function($customer) {
                return $customer->customerChat->isNotEmpty();
            });
        @endphp

        @if ($activeChats->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-green-50 to-green-100">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-comments text-green-500 ml-2"></i>
                    الشاتات النشطة ({{ $activeChats->count() }})
                </h2>
                <p class="text-sm text-gray-600">جميع الشاتات التي تم إنشاؤها ومتاحة للتواصل</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($activeChats as $customer)
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

                        <div class="bg-white border rounded-xl p-6 hover:shadow-lg transition-all duration-300 {{ $isDifficult ? 'ring-2 ring-red-200' : '' }}">
                            <!-- Customer Header -->
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ substr($customer->customer_name, 0, 1) }}
                                </div>
                                <div class="mr-3 flex-1">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $customer->customer_name }}</h3>
                                    <p class="text-sm text-gray-500 flex items-center">
                                        <i class="fas fa-phone text-xs ml-1"></i>
                                        {{ $customer->phone }}
                                    </p>
                                </div>
                                
                                <!-- Chat ID Badge -->
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        #{{ $chat->id }}
                                    </span>
                                </div>
                            </div>

                            <!-- Request Types -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if ($isDifficult)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                        عالي الأولوية
                                    </span>
                                @endif

                                @if ($isCallRequest)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-phone ml-1"></i>
                                        مكالمة
                                    </span>
                                @endif

                                @if ($isVisitRequest)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-building ml-1"></i>
                                        زيارة
                                    </span>
                                @endif
                            </div>

                            <!-- Employee Info -->
                            @if ($chat && $chat->employee)
                                <div class="flex items-center mb-3 p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($chat->employee->name, 0, 1) }}
                                    </div>
                                    <div class="mr-2">
                                        <p class="text-sm font-medium text-purple-900">{{ $chat->employee->name }}</p>
                                        <p class="text-xs text-purple-700">الموظف المسؤول</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Chat Status -->
                            <div class="flex items-center justify-between mb-4">
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

                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>

                                @if ($unreadCount > 0)
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-red-500 text-white rounded-full text-xs font-medium animate-pulse">
                                            {{ $unreadCount }}
                                        </span>
                                        <span class="text-xs text-red-600 font-medium mr-1">جديد</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Work Description -->
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $customer->work_description }}</p>

                            <!-- Last Activity -->
                            @if ($chat->last_message_at)
                                <p class="text-xs text-gray-400 mb-4">
                                    آخر نشاط: {{ $chat->last_message_at->diffForHumans() }}
                                </p>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('admin.customer-communication.show', $customer->id) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r {{ $unreadCount > 0 ? 'from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 animate-pulse' : 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700' }} transition-all duration-200 shadow-sm hover:shadow-md">
                                @if ($unreadCount > 0)
                                    <i class="fas fa-bell ml-2"></i>
                                    دخول للشات ({{ $unreadCount }})
                                @else
                                    <i class="fas fa-comments ml-2"></i>
                                    دخول للشات
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Customer Selection Modal -->
        <div id="customerSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b bg-gradient-to-r from-red-500 to-red-600 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold">اختر العميل لإنشاء شات</h3>
                            <p class="text-red-100 text-sm mt-1">اختر أحد العملاء التاليين لبدء محادثة جديدة مع الموظف المسؤول</p>
                        </div>
                        <button id="closeModalBtn" class="text-red-100 hover:text-white transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                    @php
                        $availableCustomers = $customers->filter(function($customer) {
                            return !$customer->customerChat->first();
                        });
                    @endphp

                    @if ($availableCustomers->isEmpty())
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد عملاء متاحين</h3>
                            <p class="text-gray-500 max-w-sm mx-auto">
                                جميع العملاء لديهم شاتات مُنشأة بالفعل أو لا يوجد عملاء جدد يحتاجون تواصل.
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($availableCustomers as $customer)
                                @php
                                    $classifications = $customer->customer_classifications ?? [];
                                    if (is_string($classifications)) {
                                        $classifications = json_decode($classifications, true) ?? [];
                                    }

                                    $isCallRequest = in_array('requested_call', $classifications);
                                    $isVisitRequest = in_array('requested_visit', $classifications);
                                    $isDifficult = in_array('difficult_customer', $classifications);
                                    
                                    // البحث عن الموظف المسؤول
                                    $responsibleEmployee = null;
                                    foreach($employeesWithPermission as $emp) {
                                        if ($customer->employee_id == $emp->id) {
                                            $responsibleEmployee = $emp;
                                            break;
                                        }
                                    }
                                @endphp

                                <div class="customer-card bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg hover:border-red-300 transition-all duration-300 cursor-pointer {{ $isDifficult ? 'ring-2 ring-red-200' : '' }}"
                                     data-customer-id="{{ $customer->id }}"
                                     data-customer-name="{{ $customer->customer_name }}">
                                    
                                    <!-- Customer Header -->
                                    <div class="flex items-center mb-4">
                                        <div class="h-14 w-14 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl">
                                            {{ substr($customer->customer_name, 0, 1) }}
                                        </div>
                                        <div class="mr-3 flex-1">
                                            <h3 class="font-semibold text-gray-900 text-lg">{{ $customer->customer_name }}</h3>
                                            <p class="text-sm text-gray-500 flex items-center mt-1">
                                                <i class="fas fa-phone text-xs ml-1"></i>
                                                {{ $customer->phone }}
                                            </p>
                                        </div>
                                        
                                        <!-- Selection Indicator -->
                                        <div class="selection-indicator w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                            <div class="selection-dot w-3 h-3 bg-red-500 rounded-full opacity-0"></div>
                                        </div>
                                    </div>

                                    <!-- الموظف المسؤول -->
                                    @if ($responsibleEmployee)
                                        <div class="flex items-center mb-4 p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center text-white text-xs font-bold">
                                                {{ substr($responsibleEmployee->name, 0, 1) }}
                                            </div>
                                            <div class="mr-2">
                                                <p class="text-sm font-medium text-green-900">{{ $responsibleEmployee->name }}</p>
                                                <p class="text-xs text-green-700">الموظف المسؤول الحالي</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Request Types -->
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @if ($isDifficult)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                                عالي الأولوية
                                            </span>
                                        @endif

                                        @if ($isCallRequest)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-phone ml-1"></i>
                                                مكالمة مطلوبة
                                            </span>
                                        @endif

                                        @if ($isVisitRequest)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-building ml-1"></i>
                                                زيارة مطلوبة
                                            </span>
                                        @endif

                                        @if (!$isCallRequest && !$isVisitRequest && !$isDifficult)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                عميل عام
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Work Description -->
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-900 mb-1">وصف العمل:</h4>
                                        <p class="text-sm text-gray-600 line-clamp-3">{{ $customer->work_description }}</p>
                                    </div>

                                    <!-- Customer Date -->
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar ml-1"></i>
                                            {{ $customer->created_at->format('Y/m/d') }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-user-plus ml-1"></i>
                                            عميل محتمل
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Modal Footer -->
                        <div class="mt-8 flex items-center justify-center border-t pt-6">
                            <button id="proceedBtn" disabled
                                    class="bg-gray-400 text-white px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg disabled:cursor-not-allowed">
                                <i class="fas fa-arrow-left ml-2"></i>
                                المتابعة لإنشاء الشات
                            </button>
                        </div>
                    @endif
                </div>
            </div>
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

    <style>
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .line-clamp-3 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .customer-card {
            transition: all 0.3s ease;
        }

        .customer-card:hover {
            transform: translateY(-2px);
        }

        .customer-card.selected {
            border-color: #ef4444 !important;
            background: linear-gradient(135deg, #fef2f2 0%, #fff5f5 100%);
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.1), 0 8px 10px -6px rgba(239, 68, 68, 0.1);
        }

        .customer-card.selected .selection-indicator {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .customer-card.selected .selection-dot {
            opacity: 1;
        }

        .selection-indicator {
            transition: all 0.3s ease;
        }

        .selection-dot {
            transition: opacity 0.3s ease;
        }

        .customer-card.selected {
            animation: cardSelect 0.4s ease-out;
        }

        @keyframes cardSelect {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        #customerSelectionModal.show {
            animation: modalShow 0.3s ease-out;
        }

        @keyframes modalShow {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <script>
        // تعريف المتغيرات العامة
        let selectedCustomerId = null;
        let selectedCustomerName = '';

        // تحميل JavaScript عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // ربط زر إنشاء شات جديد
            document.getElementById('createNewChatBtn').addEventListener('click', openCustomerSelectionModal);
            
           // ربط زر إغلاق النافذة
            document.getElementById('closeModalBtn').addEventListener('click', closeCustomerSelectionModal);
            
            // ربط زر المتابعة
            document.getElementById('proceedBtn').addEventListener('click', proceedToCreateChat);
            
            // ربط النقر على كروت العملاء
            document.querySelectorAll('.customer-card').forEach(card => {
                card.addEventListener('click', function() {
                    const customerId = this.dataset.customerId;
                    const customerName = this.dataset.customerName;
                    selectCustomer(customerId, customerName);
                });
            });

            // إغلاق النافذة عند النقر خارجها
            document.getElementById('customerSelectionModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCustomerSelectionModal();
                }
            });

            // إغلاق النافذة بمفتاح Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCustomerSelectionModal();
                }
            });

            // طلب إذن الإشعارات
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission();
            }
        });

        function openCustomerSelectionModal() {
            const modal = document.getElementById('customerSelectionModal');
            modal.classList.remove('hidden');
            modal.classList.add('show');
            
            // إعادة تعيين الاختيار
            selectedCustomerId = null;
            selectedCustomerName = '';
            resetCustomerSelection();
        }

        function closeCustomerSelectionModal() {
            const modal = document.getElementById('customerSelectionModal');
            modal.classList.add('hidden');
            modal.classList.remove('show');
            // إعادة تعيين الاختيار
            selectedCustomerId = null;
            selectedCustomerName = '';
            resetCustomerSelection();
        }

        function selectCustomer(customerId, customerName) {
            // إزالة الاختيار السابق
            document.querySelectorAll('.customer-card').forEach(card => {
                card.classList.remove('selected');
            });

            // إضافة الاختيار للكارت المنقور عليه
            const selectedCard = document.querySelector(`[data-customer-id="${customerId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
                
                // تحديث المتغيرات العامة
                selectedCustomerId = customerId;
                selectedCustomerName = customerName;
                
                // تفعيل زر المتابعة
                const proceedBtn = document.getElementById('proceedBtn');
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('bg-gray-400');
                proceedBtn.classList.add('bg-gradient-to-r', 'from-red-500', 'to-red-600', 'hover:from-red-600', 'hover:to-red-700');
                proceedBtn.innerHTML = '<i class="fas fa-arrow-left ml-2"></i>المتابعة لإنشاء شات مع ' + customerName;
            }
        }

        function resetCustomerSelection() {
            document.querySelectorAll('.customer-card').forEach(card => {
                card.classList.remove('selected');
            });

            const proceedBtn = document.getElementById('proceedBtn');
            proceedBtn.disabled = true;
            proceedBtn.classList.remove('bg-gradient-to-r', 'from-red-500', 'to-red-600', 'hover:from-red-600', 'hover:to-red-700');
            proceedBtn.classList.add('bg-gray-400');
            proceedBtn.innerHTML = '<i class="fas fa-arrow-left ml-2"></i>المتابعة لإنشاء الشات';
        }

        function proceedToCreateChat() {
            if (!selectedCustomerId) {
                alert('يرجى اختيار عميل أولاً');
                return;
            }

            // إظهار حالة تحميل
            const proceedBtn = document.getElementById('proceedBtn');
            proceedBtn.disabled = true;
            proceedBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري التحويل...';

            // الانتقال لصفحة التواصل مع العميل
            window.location.href = `/admin/customer-communication/${selectedCustomerId}`;
        }

        // تشغيل صوت الإشعار
        function playNotificationSound() {
            try {
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
            } catch (error) {
                console.log('Could not play notification sound:', error);
            }
        }

        // فحص الرسائل الجديدة كل دقيقة
        let lastUnreadCount = {{ $stats['unread_from_employees'] }};
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                fetch('/admin/customer-communication/stats/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > lastUnreadCount) {
                            playNotificationSound();
                            
                            // تحديث العدادات المرئية
                            document.querySelectorAll('.unread-counter').forEach(counter => {
                                counter.textContent = data.count;
                                counter.classList.add('animate-pulse');
                            });

                            // إظهار إشعار المتصفح إذا كان مسموحاً
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

        // تحديث تلقائي كل 30 ثانية
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (response.ok) {
                        console.log('Checking for updates...');
                    }
                }).catch(error => {
                    console.log('Update check failed:', error);
                });
            }
        }, 30000);
    </script>
@endsection