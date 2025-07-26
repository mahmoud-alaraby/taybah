<!-- resources/views/employee/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - نظام الموظفين</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'cairo': ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        'taiba': {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#dc143c',
                            600: '#b91c1c',
                            700: '#991b1b',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <!-- نأخر تحميل Alpine.js عشان نتجنب مشاكل الانميشن -->
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
        }
        [x-cloak] { 
            display: none !important; 
        }
        /* إصلاح مشاكل الانيميشن */
        .fade-out {
            transition: opacity 0.5s ease-in-out;
        }
        .fade-out.hidden {
            opacity: 0;
        }
        /* منع اختفاء العناصر بطريقة مفاجئة */
        .prevent-flash {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-cairo">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%)">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4" style="background-color: #dddddd1a;">
                    <div class="flex items-center">
                        <div class="h-14 flex items-center justify-center ml-2">
                            <img src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة" class="h-14">    
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col flex-1">
                    <!-- Employee Info -->
                    <div class="px-3 py-4">
                        <div class="mb-6 p-3 rounded-lg" style="background-color: #374151;">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                        <span class="text-white font-medium">
                                            {{ mb_substr(auth('employee')->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mr-3">
                                    <p class="text-sm font-medium text-white">{{ auth('employee')->user()->name }}</p>
                                    <p class="text-xs text-gray-300">{{ auth('employee')->user()->position }}</p>
                                    <p class="text-xs text-gray-300">{{ auth('employee')->user()->employee_id }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation -->
                        <nav class="space-y-1">
                            <a href="{{ route('employee.dashboard') }}" 
                               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.dashboard') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-tachometer-alt ml-3 text-sm"></i>
                                الرئيسية
                            </a>
                            
                            @if(auth('employee')->user()->hasPermission('receipts_payments'))
                                <a href="{{ route('employee.receipts-payments') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.receipts-payments*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-money-bill-wave ml-3 text-sm"></i>
                                    المقبوضات والمدفوعات
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('customer_movement'))
                                <a href="{{ route('employee.customer-movement') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-movement') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-users ml-3 text-sm"></i>
                                    حركة العملاء
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('potential_customers'))
                                <a href="{{ route('employee.potential-customers') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.potential-customers') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-user-plus ml-3 text-sm"></i>
                                    العملاء المحتملين
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('stopwatch_system'))
                                <a href="{{ route('employee.stopwatch') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.stopwatch') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-stopwatch ml-3 text-sm"></i>
                                    ستوب وتش
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('general_operations'))
                                <a href="{{ route('employee.general-operations') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.general-operations') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-cogs ml-3 text-sm"></i>
                                    التشغيل العام
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('photography_booking'))
                                <a href="{{ route('employee.photography-booking') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.photography-booking') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-camera ml-3 text-sm"></i>
                                    حجز التصوير والمونتاج
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('designers_account'))
                                <a href="{{ route('employee.designers-account') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.designers-account') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-palette ml-3 text-sm"></i>
                                    حساب المصممين
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('customer_response'))
                                <a href="{{ route('employee.customer-response') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-response') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-comments ml-3 text-sm"></i>
                                    قاموس الرد على العملاء
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('task_list'))
                                <a href="{{ route('employee.task-list') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.task-list') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-tasks ml-3 text-sm"></i>
                                    قائمة المهام
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('renewal_dates'))
                                <a href="{{ route('employee.renewal-dates') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.renewal-dates') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-calendar-alt ml-3 text-sm"></i>
                                    مواعيد التجديد
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('photography_costs'))
                                <a href="{{ route('employee.photography-costs') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.photography-costs') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-dollar-sign ml-3 text-sm"></i>
                                    تكاليف التصوير
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('customer_communication'))
                                <a href="{{ route('employee.customer-communication') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.customer-communication') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-phone ml-3 text-sm"></i>
                                    التواصل مع العملاء
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('design_follow_up'))
                                <a href="{{ route('employee.design-follow-up') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.design-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-pencil-ruler ml-3 text-sm"></i>
                                    متابعة التصميم
                                </a>
                            @endif
                            
                            @if(auth('employee')->user()->hasPermission('montage_follow_up'))
                                <a href="{{ route('employee.montage-follow-up') }}" 
                                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.montage-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-video ml-3 text-sm"></i>
                                    متابعة المونتاج
                                </a>
                            @endif
                            
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <form method="POST" action="{{ route('employee.logout') }}">
                                    @csrf
                                    <button type="submit" class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-red-600 hover:text-white transition-colors prevent-flash">
                                        <i class="fas fa-sign-out-alt ml-3 text-sm"></i>
                                        تسجيل خروج
                                    </button>
                                </form>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-sm text-gray-600">@yield('page-subtitle', 'مرحباً بك في نظام الموظفين')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- Profile dropdown -->
                        <div class="dropdown-container relative">
                            <button onclick="toggleDropdown()" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-1 prevent-flash">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ mb_substr(auth('employee')->user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down mr-2 text-sm text-gray-400"></i>
                            </button>
                            
                            <div id="profile-dropdown"
                                 class="hidden origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50 prevent-flash">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ auth('employee')->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth('employee')->user()->email }}</p>
                                    <p class="text-xs text-gray-500">{{ auth('employee')->user()->employee_id }}</p>
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الملف الشخصي</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الإعدادات</a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('employee.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        تسجيل خروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div id="success-alert" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md prevent-flash">
                                <div class="flex items-center justify-between">
                                    <div class="flex">
                                        <i class="fas fa-check-circle text-green-400 ml-2 mt-0.5"></i>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                    <button onclick="closeAlert('success-alert')" class="text-green-700 hover:text-green-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div id="error-alert" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md prevent-flash">
                                <div class="flex items-center justify-between">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-circle text-red-400 ml-2 mt-0.5"></i>
                                        <span>{{ session('error') }}</span>
                                    </div>
                                    <button onclick="closeAlert('error-alert')" class="text-red-700 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Global Scripts -->
    <script>
        // CSRF Token
        window.Laravel = { csrfToken: '{{ csrf_token() }}' };
        
        // SweetAlert Delete Confirmation
        function confirmDelete(title = 'هل أنت متأكد؟', text = 'لن تتمكن من التراجع عن هذا الإجراء!') {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc143c',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            });
        }
        
        // Success Alert
        function showSuccess(message) {
            Swal.fire({
                title: 'تم بنجاح!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#dc143c',
                confirmButtonText: 'موافق'
            });
        }
        
        // Dropdown toggle function
        function toggleDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = document.querySelector('.dropdown-container button');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Close alert function
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }
        }
        
        // Auto hide flash messages بعد 8 ثواني بدلاً من 5
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');
            
            if (successAlert) {
                closeAlert('success-alert');
            }
            if (errorAlert) {
                closeAlert('error-alert');
            }
        }, 8000);
        
        // منع أي انيميشن غير مرغوب فيه عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // إزالة أي كلاسات قد تسبب اختفاء العناصر
            const elements = document.querySelectorAll('.prevent-flash');
            elements.forEach(element => {
                element.style.opacity = '1';
                element.style.visibility = 'visible';
            });
        });
    </script>
    
    <!-- تحميل Alpine.js في النهاية عشان نتجنب مشاكل الانيميشن -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>