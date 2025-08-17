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
    <link rel="stylesheet" href="{{ asset('css/project-tracking.css') }}">
    <script src="{{ asset('js/project-tracking.js') }}"></script>

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
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
        }
        [x-cloak] { 
            display: none !important; 
        }
        .fade-out {
            transition: opacity 0.5s ease-in-out;
        }
        .fade-out.hidden {
            opacity: 0;
        }
        .prevent-flash {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-cairo">
    <div class="flex h-screen bg-gray-50">
     <!-- Sidebar -->
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

          <!-- الرئيسية -->
          <a href="{{ route('employee.dashboard') }}"
             class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('employee.dashboard') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <i class="fas fa-tachometer-alt ml-3 text-sm"></i>
            الرئيسية
          </a>

          {{-- إدارة العملاء --}}
          @php
            $showCustomerMenu = auth('employee')->user()->hasPermission('customer_communication')
              || auth('employee')->user()->hasPermission('customer_movement')
              || auth('employee')->user()->hasPermission('potential_customers');
          @endphp
          @if($showCustomerMenu)
          <div x-data="{ open: false }" class="relative w-full">
            <button type="button" @click="open = !open"
              class="flex items-center w-full px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700 transition-colors text-gray-300"
              aria-haspopup="true" aria-expanded="open">
              <i class="fas fa-address-book ml-3 text-sm"></i>
              إدارة العملاء
              <svg :class="{'rotate-180': open}" class="w-4 h-4 ml-auto text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                  x-transition:leave="transition ease-in duration-150"
                  class="absolute left-0 top-full mt-1 w-full bg-gray-800 rounded-md shadow-lg z-20 origin-top-left">

                       @if(auth('employee')->user()->hasPermission('customer_response'))
              <a href="{{ route('employee.customer-response.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.customer-response.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-comments ml-3 text-sm"></i>
                قاموس الردود على العملاء
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('customer_communication'))
              <a href="{{ route('employee.customer-communication.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.customer-communication*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-phone-alt ml-3 text-sm"></i>
                التواصل مع العملاء
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('customer_movement'))
              <a href="{{ route('employee.customer-movement') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.customer-movement') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-users ml-3 text-sm"></i>
                متابعة حركة العملاء
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('potential_customers'))
              <a href="{{ route('employee.potential-customers') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.potential-customers') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-user-plus ml-3 text-sm"></i>
                العملاء المحتملين
              </a>
              @endif
            </div>
          </div>
          @endif

          {{-- الأعمال/العمليات --}}
          @php
            $showOpsMenu = auth('employee')->user()->hasPermission('general_operations')
              || auth('employee')->user()->hasPermission('photography_booking')
              || auth('employee')->user()->hasPermission('designers_account')
              || auth('employee')->user()->hasPermission('customer_response')
              || auth('employee')->user()->hasPermission('renewal_dates')
              || auth('employee')->user()->hasPermission('receipts_payments');
          @endphp
          @if($showOpsMenu)
          <div x-data="{ open: false }" class="relative w-full">
            <button type="button" @click="open = !open"
              class="flex items-center w-full px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700 transition-colors text-gray-300">
              <i class="fas fa-cogs ml-3 text-sm"></i>
              الأعمال/العمليات
              <svg :class="{'rotate-180': open}" class="w-4 h-4 ml-auto text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                  x-transition:leave="transition ease-in duration-150"
                  class="absolute left-0 top-full mt-1 w-full bg-gray-800 rounded-md shadow-lg z-20 origin-top-left">
              @if(auth('employee')->user()->hasPermission('general_operations'))
              <a href="{{ route('employee.general-operations') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.general-operations') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-cogs ml-3 text-sm"></i>
                التشغيل العام
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('photography_booking'))
              <a href="{{ route('employee.photography-booking.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.photography-booking.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-camera ml-3 text-sm"></i>
                حجوزات التصوير والمونتاج
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('designers_account'))
              <a href="{{ route('employee.designer-task-accounts.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.designer-task-accounts.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-palette ml-3 text-sm"></i>
                حساب المصممين
              </a>
              @endif
         
              @if(auth('employee')->user()->hasPermission('renewal_dates'))
              <a href="{{ route('employee.renewal-dates.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.renewal-dates*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-calendar-alt ml-3 text-sm"></i>
                مواعيد التجديد
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('receipts_payments'))
              <a href="{{ route('employee.receipts-payments') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.receipts-payments*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-money-bill-wave ml-3 text-sm"></i>
                المقبوضات والمدفوعات
              </a>
              @endif
            </div>
          </div>
          @endif

          {{-- المتابعة: المشاريع-الحضور-التقارير --}}
          @php
            $hasTrackingMenu = auth('employee')->user()->hasPermission('project_tracking')
              || auth('employee')->user()->hasPermission('attendance_tracking')
              || auth('employee')->user()->hasPermission('work_reports');
          @endphp
          @if($hasTrackingMenu)
          <div x-data="{ open: false }" class="relative w-full">
            <button type="button" @click="open = !open"
              class="flex items-center w-full px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700 transition-colors text-gray-300">
              <i class="fas fa-clipboard-list ml-3 text-sm"></i>
              المتابعة والتقارير
              <svg :class="{'rotate-180': open}" class="w-4 h-4 ml-auto text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                  x-transition:leave="transition ease-in duration-150"
                  class="absolute left-0 top-full mt-1 w-full bg-gray-800 rounded-md shadow-lg z-20 origin-top-left">
              @if(auth('employee')->user()->hasPermission('project_tracking'))
              <a href="{{ route('employee.project-tracking.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.project-tracking.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-tasks ml-3 text-sm"></i>
                متابعة المشاريع
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('attendance_tracking'))
              <a href="{{ route('employee.attendance.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.attendance.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-fingerprint ml-3 text-sm"></i>
                الحضور والانصراف
              </a>
              @endif
              @if(auth('employee')->user()->hasPermission('work_reports'))
              <a href="{{ route('employee.work-reports.index') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.work-reports.*') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-chart-line ml-3 text-sm"></i>
                تقارير العمل
              </a>
              @endif
            </div>
          </div>
          @endif

      {{-- للموظف  --}}
          @php
            $hasWorkChatMenu = auth('employee')->user()->hasPermission('design_follow_up')
              || auth('employee')->user()->hasPermission('montage_follow_up');
          @endphp
          @if($hasWorkChatMenu)
          <div x-data="{ open: false }" class="relative w-full">
            <button type="button" @click="open = !open"
              class="flex items-center w-full px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700 transition-colors text-gray-300">
              <i class="fas fa-comments ml-3 text-sm"></i>
              شاتات العمل
              <svg :class="{'rotate-180': open}" class="w-4 h-4 ml-auto text-gray-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                  x-transition:leave="transition ease-in duration-150"
                  class="absolute left-0 top-full mt-1 w-full bg-gray-800 rounded-md shadow-lg z-20 origin-top-left">
              
              @if(auth('employee')->user()->hasPermission('design_follow_up'))
              <a href="{{ route('employee.design-follow-up') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.design-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-pencil-ruler ml-3 text-sm"></i>
                شاتات التصميم
              </a>
              @endif
              
              @if(auth('employee')->user()->hasPermission('montage_follow_up'))
              <a href="{{ route('employee.montage-follow-up') }}"
                 class="flex items-center w-full px-3 py-2 my-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.montage-follow-up') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-video ml-3 text-sm"></i>
                شاتات المونتاج
              </a>
              @endif
            </div>
          </div>
          @endif

       
           <a href="{{ route('employee.tasks.index') }}" 
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors prevent-flash {{ request()->routeIs('admin.tasks.*') ? 'bg-red-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <i class="fas fa-tasks ml-3 text-sm"></i>
                        قائمة المهام
                    </a>
         

          @if(auth('employee')->user()->hasPermission('photography_costs'))
          <a href="{{ route('employee.photography-costs.index') }}"
             class="flex items-center w-full px-3 py-2 rounded-md hover:bg-red-600 hover:text-white transition-colors {{ request()->routeIs('employee.photography-costs') ? 'bg-red-600 text-white' : 'text-gray-300' }}">
            <i class="fas fa-dollar-sign ml-3 text-sm"></i>
            تكاليف التصوير
          </a>
          @endif


          <!-- زر تسجيل الخروج -->
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
        
        // Auto hide flash messages
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
            const elements = document.querySelectorAll('.prevent-flash');
            elements.forEach(element => {
                element.style.opacity = '1';
                element.style.visibility = 'visible';
            });
        });
    </script>
    
    <!-- تحميل Alpine.js في النهاية -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html>