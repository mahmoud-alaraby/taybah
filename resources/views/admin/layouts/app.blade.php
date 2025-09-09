<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - شركة طيبة</title>
    
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
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


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
       <aside class="hidden md:flex md:flex-shrink-0 h-screen">
    <div class="flex flex-col w-64 overflow-y-auto" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%)">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4" style="background-color: #dddddd1a;">
            <div class="flex items-center">
                <div class="h-14 flex items-center justify-center ml-2">
                    <img src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة" class="h-14">    
                </div>
            </div>
        </div>
        
        <div class="flex flex-col flex-1">
            <!-- Admin Info -->
            <div class="px-3 py-4">
                <div class="mb-6 p-3 rounded-lg" style="background-color: #374151;">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                                <span class="text-white font-medium">
                                    {{ mb_substr(auth('admin')->user()->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-white">{{ auth('admin')->user()->name }}</p>
                            <p class="text-xs text-gray-300 flex items-center">
                                <i class="fas fa-user-shield ml-1"></i>
                                {{ auth('admin')->user()->role == 'super_admin' ? 'مدير النظام' : 'مدير عام' }}
                            </p>
                        </div>
                    </div>
                </div>
                
            <nav class="space-y-1  py-4">

    <!-- الرئيسية -->
    <a href="{{ route('admin.dashboard') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-tachometer-alt ml-2"></i>
        الرئيسية
    </a>

    <!-- إدارة المستخدمين -->
    <div x-data="{ open: false }" class="space-y-1">
        <button @click="open = !open"
                class="flex w-full items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
            <i class="fas fa-users-cog ml-2"></i>
            إدارة المستخدمين 
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-1 mt-1 text-gray-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="pl-6 space-y-1">
            <a href="{{ route('admin.admins.index') }}"
               class="flex text-gray-400 items-center text-sm px-3 py-2  font-medium rounded-md {{ request()->routeIs('admin.admins.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-users-cog ml-2"></i>
                إدارة المديرين
            </a>
            <a href="{{ route('admin.employees.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.employees.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-users ml-2"></i>
                إدارة الموظفين
            </a>

                <!-- إدارة الأدوار -->
    <a href="{{ route('admin.roles.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.roles.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-user-tag ml-2"></i>
        إدارة الأدوار
    </a>
        </div>
    </div>



    <!-- قائمة المهام -->
    <a href="{{ route('admin.tasks.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.tasks.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-tasks ml-2"></i>
        قائمة المهام
    </a>

    <!-- إدارة العمل -->
    <div x-data="{ open: false }" class="space-y-1">
        <button @click="open = !open"
                class="flex w-full items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
            <i class="fas fa-tasks ml-2"></i>
            إدارة العمل
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-1 mt-1 text-gray-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="pl-6 space-y-1">
            

    <a href="{{ route('admin.project-tracking.index') }}" 
       class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.project-tracking.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="nav-icon fas fa-project-diagram"></i>
        <p>  نظام الحضور والستوب ووتش</p>
    </a>


            <a href="{{ route('admin.projects.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.projects.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-project-diagram ml-2"></i>
                إدارة المشاريع
            </a>
            <a href="{{ route('admin.attendance.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.attendance.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-clock ml-2"></i>
                متابعة الحضور
            </a>
            <a href="{{ route('admin.work-reports.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.work-reports.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-chart-line ml-2"></i>
                تقارير العمل
            </a>
            <a href="{{ route('admin.time-tracking.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.time-tracking.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-stopwatch ml-2"></i>
                متابعة أوقات العمل
            </a>
        </div>
    </div>

    <!-- التواصل مع العملاء -->
    <div x-data="{ open: false }" class="space-y-1">
        <button @click="open = !open"
                class="flex w-full items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
            <i class="fas fa-phone-alt ml-2"></i>
          إدارة العملاء
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-1 mt-1 text-gray-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="pl-6 space-y-1">
            <a href="{{ route('admin.customer-response.index') }}"
               class="group flex items-center px-3 text-gray-400 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customer-response.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-comments ml-2"></i>
                قاموس الردود على العملاء
            </a>
            <a href="{{ route('admin.customer-communication.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customer-communication.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-phone-alt ml-2"></i>
                التواصل مع العملاء
            </a>
            <a href="{{ route('admin.customer-movement.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customer-movement.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-users ml-2"></i>
                متابعة حركة العملاء
            </a>
            <a href="{{ route('admin.potential-customers.index') }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.potential-customers.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-user-plus ml-2"></i>
                العملاء المحتملين
            </a>
        </div>
    </div>

    
    <!-- متابعة العمل -->
    <div x-data="{ open: false }" class="space-y-1">
        <button @click="open = !open"
                class="flex w-full items-center px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
            <i class="fas fa-comments ml-2"></i>
            متابعة العمل
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 ml-1 mt-1 text-gray-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-transition class="pl-6 space-y-1 ">
            <a href="{{ route('admin.work-chat.index', ['type' => 'design']) }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.work-chat.*') && request('type') === 'design' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-pencil-ruler ml-2"></i>
                متابعة التصميم
            </a>
            <a href="{{ route('admin.work-chat.index', ['type' => 'montage']) }}"
               class="flex text-gray-400 items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.work-chat.*') && request('type') === 'montage' ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <i class="fas fa-video ml-2"></i>
                متابعة المونتاج
            </a>
        </div>
    </div>

        <!-- المقبوضات والمدفوعات -->
    <a href="{{ route('admin.receipts-payments.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.receipts-payments.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-money-bill-wave ml-2"></i>
        المقبوضات والمدفوعات
    </a>

    
    <!-- نظام التشغيل العام -->
    <a href="{{ route('admin.operation-system.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.operation-system.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-calendar-check ml-2"></i>
        نظام التشغيل العام
    </a>

        <!-- حجوزات التصوير والمونتاج -->
    <a href="{{ route('admin.photography-booking.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.photography-booking.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-camera ml-2"></i>
        حجوزات التصوير والمونتاج
    </a>

        <!-- تكاليف التصوير -->
    <a href="{{ route('admin.photography-costs.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.photography-costs.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-camera-retro ml-2"></i>
        تكاليف التصوير
    </a>

    <!-- حساب المصممين بالتاسك -->
    <a href="{{ route('admin.designer-task-accounts.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
       {{ request()->routeIs('admin.designer-task-accounts.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-pencil-ruler ml-2"></i>
        حساب المصممين بالتاسك
    </a>




    <!-- مواعيد التجديد -->
    <a href="{{ route('admin.renewal-dates.index') }}"
       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.renewal-dates.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
        <i class="fas fa-calendar-alt ml-2"></i>
        مواعيد التجديد
        @if(isset($upcomingRenewalsCount) && $upcomingRenewalsCount > 0)
            <span class="bg-yellow-500 text-white text-xs rounded-full px-2 py-1 mr-2">
                {{ $upcomingRenewalsCount }}
            </span>
        @endif
    </a>




    <!-- تسجيل خروج -->
    <div class="mt-2 pt-6 border-t border-gray-700">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-red-600 hover:text-white transition-colors prevent-flash">
                <i class="fas fa-sign-out-alt ml-2"></i>
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
                        <p class="text-sm text-gray-600">@yield('page-subtitle', 'مرحباً بك في نظام إدارة شركة طيبة')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- Profile dropdown -->
                        <div class="dropdown-container relative">
                            <button onclick="toggleDropdown()" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-1 prevent-flash">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium text-sm">
                                        {{ mb_substr(auth('admin')->user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down mr-2 text-sm text-gray-400"></i>
                            </button>
                            
                            <div id="profile-dropdown"
                                 class="hidden origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50 prevent-flash">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ auth('admin')->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth('admin')->user()->email }}</p>
                                </div>
                                <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الملف الشخصي</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">الإعدادات</a> -->
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('admin.logout') }}">
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