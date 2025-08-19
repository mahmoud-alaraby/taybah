{{-- resources/views/employee/photography-booking/show.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'تفاصيل الحجز')

@section('content')
<div class="bg-gray-50 min-h-screen py-6" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="bg-white px-6 py-4 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="w-16 h-16 sm:w-16 sm:h-16 rounded-full bg-gradient-to-r ml-2 from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                      <svg class="w-8 h-8 text-white 3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-600">تفاصيل الحجز</h1>
                            <p class="text-gray-500 text-sm">{{ $photographyBooking->client_name }} - {{ $photographyBooking->booking_date ? $photographyBooking->booking_date->format('d F Y') : '' }}</p>
                        </div>
                    </div>

                 
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <a href="{{ route('employee.photography-booking.edit', $photographyBooking) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل الحجز
                        </a>
                        <a href="{{ route('employee.photography-booking.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Client Info Card --}}
        <!-- <div class="bg-gradient-to-r from-red-50 to-red-50 rounded-xl p-6 border border-red-200 mb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-r from-red-400 to-red-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    {{ substr($photographyBooking->client_name, 0, 1) }}
                </div>
                <div class="sm:mr-6 flex-1 text-center sm:text-right">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ $photographyBooking->client_name }}</h2>
                    <p class="text-red-600 font-semibold text-lg mb-1">{{ $photographyBooking->client_phone ?? 'غير محدد' }}</p>
                    <p class="text-gray-600 text-sm">رقم الحجز: #{{ $photographyBooking->id }}</p>
                </div>
            </div>
        </div> -->

        {{-- Booking Details --}}
        <div class="mb-6">
            {{-- Work Description --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    وصف العمل
                </h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $photographyBooking->work_description ?: 'لا يوجد وصف' }}</p>
                </div>
                @if($photographyBooking->work_notes)
                    <div>
                        <label class="text-sm opacity-80 block mb-2">ملاحظات العمل</label>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $photographyBooking->work_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Agreement --}}
            @if($photographyBooking->agreement)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        الاتفاق
                    </h3>
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $photographyBooking->agreement }}</p>
                    </div>
                </div>
            @endif

            {{-- Sessions Scheduling --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    جدولة السيشنات
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="text-center">
                        <label class="text-sm opacity-80 block mb-2">أول سيشن</label>
                        <p class="text-lg font-semibold bg-gray-50 rounded-lg py-2 px-3">
                            {{ $photographyBooking->first_session ? $photographyBooking->first_session->format('Y-m-d') : 'غير محدد' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <label class="text-sm opacity-80 block mb-2">آخر سيشن</label>
                        <p class="text-lg font-semibold bg-gray-50 rounded-lg py-2 px-3">
                            {{ $photographyBooking->last_session ? $photographyBooking->last_session->format('Y-m-d') : 'غير محدد' }}
                        </p>
                    </div>
                    <div class="text-center">
                        <label class="text-sm opacity-80 block mb-2">عدد السيشنات</label>
                        <p class="text-3xl font-bold bg-gray-50 rounded-lg py-2 px-3">{{ $photographyBooking->sessions_count ?? 1 }}</p>
                    </div>
                </div>
            </div>

            {{-- Montage & Delivery --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    المونتاج والتسليم
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm opacity-80 block mb-2">بداية المونتاج</label>
                        <p class="text-sm font-semibold bg-gray-50 rounded-lg py-2 px-3">
                            {{ $photographyBooking->montage_start ? $photographyBooking->montage_start->format('Y-m-d') : 'غير محدد' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm opacity-80 block mb-2">تسليم أولي</label>
                        <p class="text-sm font-semibold bg-gray-50 rounded-lg py-2 px-3">
                            {{ $photographyBooking->initial_delivery ? $photographyBooking->initial_delivery->format('Y-m-d') : 'غير محدد' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm opacity-80 block mb-2">تسليم نهائي</label>
                        <p class="text-sm font-semibold bg-gray-50 rounded-lg py-2 px-3">
                            {{ $photographyBooking->final_delivery ? $photographyBooking->final_delivery->format('Y-m-d') : 'غير محدد' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    معلومات إضافية
                </h3>
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
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $photographyBooking->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- System Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    معلومات النظام
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <label class="text-sm opacity-80 block mb-2">معرف الحجز</label>
                        <p class="text-3xl font-bold">#{{ $photographyBooking->id }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="text-sm opacity-80 block mb-1">تاريخ الإنشاء</label>
                        <p class="text-lg font-semibold">{{ $photographyBooking->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="text-sm opacity-80 block mb-1">آخر تحديث</label>
                        <p class="text-lg font-semibold">{{ $photographyBooking->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <label class="text-sm opacity-80 block mb-1">تم الإنشاء بواسطة</label>
                        <p class="text-lg font-semibold">
                            @if($photographyBooking->employeeCreator)
                                {{ $photographyBooking->employeeCreator->name }}
                            @elseif($photographyBooking->creator)
                                {{ $photographyBooking->creator->name }}
                            @else
                                غير محدد
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    إجراءات سريعة
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.photography-booking.edit', $photographyBooking) }}" 
                       class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل الحجز
                    </a>

                    @if($photographyBooking->client_phone)
                        <a href="tel:{{ $photographyBooking->client_phone }}" 
                           class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            اتصال بالعميل
                        </a>
                    @endif

                    <a href="{{ route('admin.photography-booking.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
	                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
	                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
	                        </svg>
	                        حذف الحجز
	                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
