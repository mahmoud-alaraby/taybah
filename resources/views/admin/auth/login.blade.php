<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل دخول الإدارة - شركة طيبة</title>

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
        .bg-overlay::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
          
            z-index: 0;
        }
    </style>
</head>
<body class="relative min-h-screen bg-cover bg-center bg-no-repeat font-cairo bg-overlay" 
      style="background-image: url('https://images.unsplash.com/photo-1551135049-8a33b5883817?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGNvbXBhbnklMjBvd25lciUyMG1lZXRpbmdzJTIwbWVufGVufDB8fDB8fHww');">

    <div class="relative z-10 flex min-h-screen items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">

            <!-- Card Form -->
            <div class=" max-w-md w-full backdrop-blur-md bg-white/20  rounded-xl shadow-xl p-8 space-y-6">

                <div class="text-center">
                    <img class="mx-auto h-16 w-auto" src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة">
                    <h2 class="mt-6 text-3xl font-bold text-white">تسجيل دخول الإدارة</h2>
                    <p class="mt-2 text-sm text-white/60">مرحباً بك في نظام إدارة شركة طيبة</p>
                </div>

                <!-- Form -->
                <form class="mt-8 space-y-6" action="{{ route('admin.login.post') }}" method="POST">
                    @csrf

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="bg-red-100/80 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-white/80 mb-1">البريد الإلكتروني</label>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   value="{{ old('email') }}"
                                   class="relative w-full px-3 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-taiba-500 focus:border-taiba-500 sm:text-sm"
                                   placeholder="أدخل البريد الإلكتروني">
                        </div>

                  <div>
    <label for="password" class="block text-sm font-medium text-white/80 mb-1">كلمة المرور</label>
    <div class="relative">
        <input
            id="password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            placeholder="أدخل كلمة المرور"
            class="block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 focus:ring-taiba-500 focus:border-taiba-500 sm:text-sm"
        />
        <button
            type="button"
            onclick="togglePassword()"
            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-700 hover:text-gray-600"
            aria-label="Toggle password visibility"
        >
            <svg
                id="eyeOpen"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 hidden"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
            </svg>
            <svg
                id="eyeClosed"
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.39-4.045m3.232-2.313A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.043 5.197M9.88 9.88a3 3 0 104.24 4.24"
                />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </button>
    </div>
</div>

                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-4 w-4 text-taiba-600 focus:ring-taiba-500 border-gray-300 rounded">
                            <label for="remember" class="mr-2 text-sm text-white/80">تذكرني</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-white/60 hover:text-white/80">نسيت كلمة المرور؟</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-taiba-500 hover:bg-taiba-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-taiba-500 transition">
                            تسجيل الدخول
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-xs text-white/60">
                        © {{ date('Y') }} شركة طيبة للتسويق والخدمات. جميع الحقوق محفوظة.
                    </p>
                </div>
            </div>
        </div>
    </div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');
        if (password.type === 'password') {
            password.type = 'text';
            eyeClosed.classList.add('hidden');
            eyeOpen.classList.remove('hidden');
        } else {
            password.type = 'password';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        }
    }
</script>

</body>
</html>
