<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحبا بك في شركة طيبة</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        taiba: {
                            500: '#dc143c',
                            600: '#b91c1c',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        /* لإضافة طبقة تغميق إضافية على الصورة */
        body{
            font-family: 'Cairo', sans-serif;
        }
        .bg-dark-layer {
            position: absolute;
            inset: 0;
            background: rgba(24,28,34,0.6);
            z-index: 0;
        }
    </style>
</head>

<body class="relative min-h-screen flex flex-col items-center justify-center bg-cover bg-center" 
style="  background-image-size:cover ;background-image: url('https://plus.unsplash.com/premium_photo-1670315267653-2adecd823d9e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1yZWxhdGVkfDh8fHxlbnwwfHx8fHw%3D');">


    <!-- طبقة تغميق فوق الصورة -->
    <div class="bg-dark-layer"></div>

    <!-- شعار الشركة -->
    <div class="w-full flex items-center justify-center mt-8 mb-4 z-10" >
        <div class="flex items-center my-4">
            <div class="h-16 flex items-center justify-center ml-2">
                <img src="{{ asset('assets/images/taiba-logo.png') }}" alt="شركة طيبة" class="h-16">    
            </div>
        </div>
    </div>

    <div class="z-10 w-full flex flex-col items-center">
        <h1 class="text-white text-4xl md:text-5xl font-extrabold mt-4 mb-8 drop-shadow-lg text-center">أهلاً بك في نظام شركة طيبة</h1>
        <div class="flex flex-col md:flex-row gap-8 mb-6 justify-center items-center">
            <!-- كارد الموظف -->
            <div class="w-80 bg-white bg-opacity-85 rounded-2xl shadow-xl flex flex-col items-center pb-8 relative overflow-hidden ring-2 ring-blue-400 hover:scale-105 transition-transform duration-300">
                <!-- بوردر علوي جريدينت -->
                <div class="absolute top-0 left-0 right-0 h-2" style="background: linear-gradient(90deg, #2563eb, #38bdf8);"></div>
                <!-- أيقونة الموظف -->
                <div class="mt-8 mb-4 flex items-center justify-center">
                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="12" fill="#38bdf8" opacity="0.18"/>
                        <path d="M14 10a2 2 0 11-4 0 2 2 0 014 0z" fill="#2563eb"/>
                        <path d="M6 18a6 6 0 1112 0H6z" fill="#0ea5e9"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-gray-800 mb-1">دخول الموظف</div>
                <p class="text-gray-500 mb-6 text-center px-6">لو كنت موظف، اضغط الزر التالي لتسجيل الدخول إلى النظام.</p>
                <a href="{{ route('employee.login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-7 rounded-full shadow transition duration-200 mt-2">
                    دخول الموظف
                </a>
            </div>

            <!-- كارد الأدمن -->
            <div class="w-80 bg-white bg-opacity-85 rounded-2xl shadow-xl flex flex-col items-center pb-8 relative overflow-hidden ring-2 ring-red-400 hover:scale-105 transition-transform duration-300">
                <div class="absolute top-0 left-0 right-0 h-2" style="background: linear-gradient(90deg, #ef4444, #f59e42);"></div>
                <!-- أيقونة الأدمن -->
                <div class="mt-8 mb-4 flex items-center justify-center">
                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="12" fill="#f87171" opacity="0.18"/>
                        <path d="M19 13a7 7 0 10-14 0a7 7 0 0014 0zm-8 1v2a1 1 0 102 0v-2a1 1 0 10-2 0zm.41-7.41A1.78 1.78 0 0112 6c.62 0 1.18.28 1.59.59a1.67 1.67 0 01.41.82c0 .83-.55 1.54-1.33 1.54S11 8.24 11 7.41c0-.31.16-.62.41-.82z" fill="#ef4444"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-gray-800 mb-1">دخول الأدمن</div>
                <p class="text-gray-500 mb-6 text-center px-6">لو أنت أدمن اضغط الزر التالي لتسجيل الدخول إلى لوحة الإدارة.</p>
                <a href="{{ route('admin.login') }}" class="bg-red-500 hover:bg-red-700 text-white font-semibold py-2 px-7 rounded-full shadow transition duration-200 mt-2">
                    دخول الأدمن
                </a>
            </div>
        </div>
    </div>
</body>
</html>
