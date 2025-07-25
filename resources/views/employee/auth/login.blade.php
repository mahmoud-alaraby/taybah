<!-- resources/views/employee/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل دخول الموظفين - شركة طيبة</title>
    
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
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 font-cairo">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <img class="h-16 w-auto" src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة">
                </div>
                <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                    تسجيل دخول الموظفين
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    مرحباً بك في نظام الموظفين - شركة طيبة
                </p>
            </div>
            
            <form class="mt-8 space-y-6" action="{{ route('employee.login.post') }}" method="POST">
                @csrf
                
                <!-- Errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            البريد الإلكتروني
                        </label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="{{ old('email') }}"
                               class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-taiba-500 focus:border-taiba-500 focus:z-10 sm:text-sm"
                               placeholder="أدخل البريد الإلكتروني">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            كلمة المرور
                        </label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="current-password" 
                               required
                               class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-taiba-500 focus:border-taiba-500 focus:z-10 sm:text-sm"
                               placeholder="أدخل كلمة المرور">
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-taiba-600 focus:ring-taiba-500 border-gray-300 rounded">
                        <label for="remember" class="mr-2 block text-sm text-gray-900">
                            تذكرني
                        </label>
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-taiba-500 hover:bg-taiba-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-taiba-500 transition-colors duration-200">
                        <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-taiba-300 group-hover:text-taiba-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        تسجيل الدخول
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} شركة طيبة للتسويق والخدمات. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
