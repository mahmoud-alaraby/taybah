{{-- resources/views/admin/print-booking/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'تفاصيل حجز الطباعة')

@section('content')
<div class="bg-gray-50 min-h-screen py-6" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="bg-white px-6 py-4 rounded-t-xl">
                 <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center">
                          <div class="w-16 h-16 ml-3 rounded-full bg-gradient-to-r from-red-500 to-red-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                </div>
                     
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-700">تفاصيل حجز الطباعة</h1>
                            <p class="text-gray-500 text-sm">{{ $printBooking->client_name }} - {{ $printBooking->booking_date ? $printBooking->booking_date->format('d F Y') : '' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                        <a href="{{ route('admin.print-booking.edit', $printBooking) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل الحجز
                        </a>
                        <a href="{{ route('admin.print-booking.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booking Details Grid --}}
        <div class="g mb-6">
            {{-- Work Description --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    وصف العمل
                </h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $printBooking->work_description ?: 'لا يوجد وصف' }}</p>
                </div>
                @if($printBooking->work_notes)
                    <div>
                        <label class="text-sm opacity-80 block mb-2">ملاحظات العمل</label>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $printBooking->work_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Agreement --}}
        @if($printBooking->agreement)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 my-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                الاتفاق
            </h3>
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $printBooking->agreement }}</p>
            </div>
        </div>
        @endif

        {{-- Orders Scheduling --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 ml-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                جدولة الأوردرات
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">أول أوردر</label>
                    <p class="text-lg font-semibold bg-gray-50 rounded-lg py-2 px-3">
                        {{ $printBooking->first_order ? $printBooking->first_order->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">آخر أوردر</label>
                    <p class="text-lg font-semibold bg-gray-50 rounded-lg py-2 px-3">
                        {{ $printBooking->last_order ? $printBooking->last_order->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div class="text-center">
                    <label class="text-sm opacity-80 block mb-2">عدد الأوردرات</label>
                    <p class="text-3xl font-bold bg-gray-50 rounded-lg py-2 px-3">{{ $printBooking->orders_count ?? 1 }}</p>
                </div>
            </div>
        </div>

        {{-- Delivery --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                التسليم
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm opacity-80 block mb-2">تسليم أولي</label>
                    <p class="text-sm font-semibold bg-gray-50 rounded-lg py-2 px-3">
                        {{ $printBooking->initial_delivery ? $printBooking->initial_delivery->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-2">تسليم نهائي</label>
                    <p class="text-sm font-semibold bg-gray-50 rounded-lg py-2 px-3">
                        {{ $printBooking->final_delivery ? $printBooking->final_delivery->format('Y-m-d') : 'غير محدد' }}
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
                        {{ $printBooking->booking_date ? $printBooking->booking_date->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">وقت الحجز</label>
                    <p class="text-sm font-semibold">
                        {{ $printBooking->booking_time ? $printBooking->booking_time->format('H:i') : 'غير محدد' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">مدة الحجز</label>
                    <p class="text-sm font-semibold">
                        {{ $printBooking->duration_hours ?? 1 }} ساعة
                    </p>
                </div>
                <div>
                    <label class="text-sm opacity-80 block mb-1">الموقع</label>
                    <p class="text-sm font-semibold">
                        {{ $printBooking->location ?: 'غير محدد' }}
                    </p>
                </div>
            </div>
            @if($printBooking->notes)
                <div class="mt-6">
                    <label class="text-sm opacity-80 block mb-2">ملاحظات إضافية</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $printBooking->notes }}</p>
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
                    <p class="text-3xl font-bold">#{{ $printBooking->id }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <label class="text-sm opacity-80 block mb-1">تاريخ الإنشاء</label>
                    <p class="text-lg font-semibold">{{ $printBooking->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <label class="text-sm opacity-80 block mb-1">آخر تحديث</label>
                    <p class="text-lg font-semibold">{{ $printBooking->updated_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <label class="text-sm opacity-80 block mb-1">تم الإنشاء بواسطة</label>
                    <p class="text-lg font-semibold">
                        @if($printBooking->employeeCreator)
                            {{ $printBooking->employeeCreator->name }}
                        @elseif($printBooking->creator)
                            {{ $printBooking->creator->name }}
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
                <a href="{{ route('admin.print-booking.edit', $printBooking) }}" 
                   class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل الحجز
                </a>

                @if($printBooking->client_phone)
                    <a href="tel:{{ $printBooking->client_phone }}" 
                       class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        اتصال بالعميل
                    </a>
                @endif

                <a href="{{ route('admin.print-booking.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    العودة للقائمة
                </a>

                <form action="{{ route('admin.print-booking.destroy', $printBooking) }}" method="POST" class="inline">
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
@endsection