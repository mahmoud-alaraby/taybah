{{-- resources/views/admin/photography-booking/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تفاصيل الحجز')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 flex items-center">
                <svg class="w-10 h-10 mr-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                تفاصيل الحجز #{{ $photographyBooking->id }}
            </h1>
            <p class="text-slate-600 mt-2 text-lg">عرض جميع تفاصيل الحجز</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('admin.photography-booking.edit', $photographyBooking) }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
            <a href="{{ route('admin.photography-booking.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-8">
        @if($photographyBooking->status == 'in_progress')
            <span class="inline-flex items-center px-6 py-3 rounded-full text-base font-semibold bg-amber-100 text-amber-800 border-2 border-amber-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                جاري العمل
            </span>
        @elseif($photographyBooking->status == 'completed')
            <span class="inline-flex items-center px-6 py-3 rounded-full text-base font-semibold bg-emerald-100 text-emerald-800 border-2 border-emerald-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                مكتمل
            </span>
        @else
            <span class="inline-flex items-center px-6 py-3 rounded-full text-base font-semibold bg-rose-100 text-rose-800 border-2 border-rose-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                ديون معدومة
            </span>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- معلومات العميل -->
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h3 class="text-xl font-bold">معلومات العميل</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-sm opacity-80 block mb-1">اسم العميل</label>
                    <p class="text-2xl font-bold">{{ $photographyBooking->client_name }}</p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">رقم الجوال</label>
                    @if($photographyBooking->client_phone)
                        <p class="text-lg">
                            <a href="tel:{{ $photographyBooking->client_phone }}" class="hover:underline inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $photographyBooking->client_phone }}
                            </a>
                        </p>
                    @else
                        <p class="text-lg opacity-70">غير محدد</p>
                    @endif
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">المسؤول عن التنفيذ</label>
                    @if($photographyBooking->assignedPerson)
                        <p class="text-lg font-semibold">{{ $photographyBooking->assignedPerson->name }}</p>
                        <p class="text-sm opacity-80">{{ $photographyBooking->assignedPerson->employee_id }}</p>
                    @else
                        <p class="text-lg opacity-70">غير محدد</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- وصف العمل -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-bold">وصف العمل</h3>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-6 mb-4">
                <p class="text-white leading-relaxed">{{ $photographyBooking->work_description ?: 'لا يوجد وصف' }}</p>
            </div>
            @if($photographyBooking->work_notes)
                <div>
                    <label class="text-sm opacity-80 block mb-2">ملاحظات العمل</label>
                    <div class="bg-white bg-opacity-20 rounded-xl p-4">
                        <p class="text-white leading-relaxed">{{ $photographyBooking->work_notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- الاتفاق -->
        @if($photographyBooking->agreement)
        <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold">الاتفاق</h3>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-6">
                <p class="text-white leading-relaxed">{{ $photographyBooking->agreement }}</p>
            </div>
        </div>
        @endif

        <!-- جدولة السيشنات -->
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-bold">جدولة السيشنات</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">أول سيشن</label>
                    <p class="text-lg font-semibold bg-white bg-opacity-20 rounded-lg py-2 px-3">
                        {{ $photographyBooking->first_session ? $photographyBooking->first_session->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">آخر سيشن</label>
                    <p class="text-lg font-semibold bg-white bg-opacity-20 rounded-lg py-2 px-3">
                        {{ $photographyBooking->last_session ? $photographyBooking->last_session->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">عدد السيشنات</label>
                    <p class="text-3xl font-bold bg-white bg-opacity-20 rounded-lg py-2 px-3">{{ $photographyBooking->sessions_count ?? 1 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- جدولة المونتاج والتسليم -->
        <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-bold">المونتاج والتسليم</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm opacity-80 block mb-2">بداية المونتاج</label>
                    <p class="text-sm font-semibold bg-white bg-opacity-20 rounded-lg py-2 px-3">
                        {{ $photographyBooking->montage_start ? $photographyBooking->montage_start->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-2">تسليم أولي</label>
                    <p class="text-sm font-semibold bg-white bg-opacity-20 rounded-lg py-2 px-3">
                        {{ $photographyBooking->initial_delivery ? $photographyBooking->initial_delivery->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-2">تسليم نهائي</label>
                    <p class="text-sm font-semibold bg-white bg-opacity-20 rounded-lg py-2 px-3">
                        {{ $photographyBooking->final_delivery ? $photographyBooking->final_delivery->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold">معلومات إضافية</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm opacity-80 block mb-1">تاريخ الحجز</label>
                    <p class="text-sm font-semibold">
                        {{ $photographyBooking->booking_date ? $photographyBooking->booking_date->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">وقت الحجز</label>
                    <p class="text-sm font-semibold">
                        {{ $photographyBooking->booking_time ? $photographyBooking->booking_time->format('H:i') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">مدة الحجز</label>
                    <p class="text-sm font-semibold">
                        {{ $photographyBooking->duration_hours ?? 1 }} ساعة
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">الموقع</label>
                    <p class="text-sm font-semibold">
                        {{ $photographyBooking->location ?: 'غير محدد' }}
                    </p>
                </div>
            </div>
            @if($photographyBooking->notes)
                <div class="mt-6">
                    <label class="text-sm opacity-80 block mb-2">ملاحظات إضافية</label>
                    <div class="bg-white bg-opacity-20 rounded-xl p-4">
                        <p class="text-white leading-relaxed">{{ $photographyBooking->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- معلومات النظام -->
    <div class="bg-gradient-to-br from-slate-600 to-slate-700 rounded-2xl p-8 text-white shadow-xl mb-8">
        <div class="flex items-center mb-6">
            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="text-xl font-bold">معلومات النظام</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white bg-opacity-10 rounded-xl p-4 text-center">
                <label class="text-sm opacity-80 block mb-2">معرف الحجز</label>
                <p class="text-3xl font-bold">#{{ $photographyBooking->id }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">تاريخ الإنشاء</label>
                <p class="text-lg font-semibold">{{ $photographyBooking->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">آخر تحديث</label>
                <p class="text-lg font-semibold">{{ $photographyBooking->updated_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-xl p-4">
                <label class="text-sm opacity-80 block mb-1">تم الإنشاء بواسطة</label>
                <p class="text-lg font-semibold">
                    @if($photographyBooking->creator)
                        {{ $photographyBooking->creator->name }}
                    @else
                        غير محدد
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <h3 class="text-xl font-semibold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
            </svg>
            إجراءات سريعة
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.photography-booking.edit', $photographyBooking) }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل الحجز
            </a>

            @if($photographyBooking->client_phone)
                <a href="tel:{{ $photographyBooking->client_phone }}" 
                   class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    اتصال بالعميل
                </a>
            @endif

            <a href="{{ route('admin.photography-booking.index') }}" 
               class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                العودة للقائمة
            </a>

            <form action="{{ route('admin.photography-booking.destroy', $photographyBooking) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105"
                        onclick="return confirm('هل أنت متأكد من حذف هذا الحجز؟')">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف الحجز
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
