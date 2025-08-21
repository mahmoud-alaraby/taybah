{{-- resources/views/employee/photography-booking/index.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'نظام حجز التصوير والمونتاج')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-camera text-red-600 ml-2"></i>
                    نظام حجز التصوير والمونتاج
                </h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم جميع حجوزاتي</p>
            </div>
            <!-- Add Booking Button -->
            <div class="flex items-center gap-3">
                <a href="{{ route('employee.photography-booking.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة حجز جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <!-- إجمالي الحجوزات -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي الحجوزات</p>
                        <p class="text-2xl font-bold">{{ $bookings->total() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <!-- مُخصصة من المدير -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مُخصصة من المدير</p>
                        <p class="text-2xl font-bold">{{ $stats['assigned_by_admin'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <!-- أضفتها بنفسي -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">أضفتها بنفسي</p>
                        <p class="text-2xl font-bold">{{ $stats['created_by_me'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
            <!-- غير مُخصصة لي -->
            <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">غير مُخصصة لي</p>
                        <p class="text-2xl font-bold">{{ $stats['not_assigned_to_me'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- مكتملة -->
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مكتملة</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'completed')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- جاري العمل -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">جاري العمل</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'in_progress')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search text-red-600 ml-2"></i>
                البحث والتصفية
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('employee.photography-booking.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <!-- فلتر نوع الحجز -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الحجز</label>
                        <select name="booking_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">جميع الحجوزات</option>
                            <option value="assigned_by_admin" {{ request('booking_filter') == 'assigned_by_admin' ? 'selected' : '' }}>
                                حجوزات أضافني المسئول فيها
                            </option>
                            <option value="created_by_me" {{ request('booking_filter') == 'created_by_me' ? 'selected' : '' }}>
                                حجوزات أضفتها بنفسي
                            </option>
                            <option value="not_assigned_to_me" {{ request('booking_filter') == 'not_assigned_to_me' ? 'selected' : '' }}>
                                حجوزات لم أضفها أو لم تُسند إلي
                            </option>
                        </select>
                    </div>
                    <!-- الحالة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">جميع الحالات</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>جاري العمل</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تم الانتهاء</option>
                            <option value="bad_debt" {{ request('status') == 'bad_debt' ? 'selected' : '' }}>ديون معدومة</option>
                        </select>
                    </div>
                    <!-- اسم العميل -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
                        <input type="text" name="client_name" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" 
                            value="{{ request('client_name') }}" placeholder="ابحث بالاسم">
                    </div>
                    <!-- من تاريخ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input type="date" name="date_from" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" 
                            value="{{ request('date_from') }}">
                    </div>
                    <!-- إلى تاريخ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input type="date" name="date_to" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" 
                            value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                    <a href="{{ route('employee.photography-booking.index') }}" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors text-center inline-flex items-center justify-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-table text-red-600 ml-2"></i>
                    قائمة حجوزاتي
                </h3>
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    إجمالي: {{ $bookings->total() }} حجز
                </span>
            </div>
        </div>
        @php
            $employeeId = auth('employee')->id();
        @endphp
        @if($bookings->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                رقم الحجز
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                نوع الحجز
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                اسم العميل
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                وصف العمل
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                رقم الجوال
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                أول سيشن
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                آخر سيشن
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                عدد السيشنات
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                بداية المونتاج
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تسليم أولي
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                تسليم نهائي
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="text-xs font-bold text-red-600">#{{ $booking->id }}</span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($booking->created_by_employee == $employeeId)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-user-plus ml-1"></i>
                                            أضفته بنفسي
                                        </span>
                                    @elseif($booking->assigned_person_id == $employeeId && $booking->created_by && !$booking->created_by_employee)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-user-tie ml-1"></i>
                                            مُخصص من المدير
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question-circle ml-1"></i>
                                            غير مُخصص
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">{{ Str::limit($booking->client_name, 12) }}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-xs">
                                        {{ Str::limit($booking->work_description, 20) }}
                                        @if($booking->work_notes)
                                            <br><small class="text-gray-500">{{ Str::limit($booking->work_notes, 15) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($booking->client_phone)
                                        <a href="tel:{{ $booking->client_phone }}" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="fas fa-phone text-xs"></i>{{ $booking->client_phone }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->first_session ? $booking->first_session->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->last_session ? $booking->last_session->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $booking->sessions_count ?? 1 }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->montage_start ? $booking->montage_start->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->initial_delivery ? $booking->initial_delivery->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->final_delivery ? $booking->final_delivery->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($booking->status == 'in_progress')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock text-xs"></i>
                                            جاري
                                        </span>
                                    @elseif($booking->status == 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check text-xs"></i>
                                            مكتمل
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle text-xs"></i>
                                            ديون
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('employee.photography-booking.show', $booking) }}" 
                                            class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded" title="عرض">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if($booking->created_by_employee == $employeeId || $booking->assigned_person_id == $employeeId)
                                            <a href="{{ route('employee.photography-booking.edit', $booking) }}" 
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors p-1 rounded" title="تعديل">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        @endif
                                        @if($booking->created_by_employee == $employeeId)
                                            <form action="{{ route('employee.photography-booking.destroy', $booking) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors p-1 rounded" 
                                                        onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach($bookings as $booking)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="text-sm font-bold text-red-600">#{{ $booking->id }}</h4>
                                <p class="text-base font-medium text-gray-900 mt-1">{{ $booking->client_name }}</p>
                                @if($booking->work_description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($booking->work_description, 30) }}</p>
                                @endif
                                <!-- نوع الحجز -->
                                <div class="mt-2">
                                    @if($booking->created_by_employee == $employeeId)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-user-plus ml-1"></i>
                                            أضفته بنفسي
                                        </span>
                                    @elseif($booking->assigned_person_id == $employeeId && $booking->created_by && !$booking->created_by_employee)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-user-tie ml-1"></i>
                                            مُخصص من المدير
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-question-circle ml-1"></i>
                                            غير مُخصص
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($booking->status == 'in_progress')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock ml-1"></i>
                                        جاري العمل
                                    </span>
                                @elseif($booking->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check ml-1"></i>
                                        مكتمل
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                        ديون معدومة
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                            <div>
                                <span class="font-medium text-gray-900">الجوال:</span> 
                                @if($booking->client_phone)
                                    <a href="tel:{{ $booking->client_phone }}" class="text-blue-600 hover:text-blue-800 mr-1">{{ $booking->client_phone }}</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">السيشنات:</span> 
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">{{ $booking->sessions_count ?? 1 }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">أول سيشن:</span> 
                                {{ $booking->first_session ? $booking->first_session->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">آخر سيشن:</span> 
                                {{ $booking->last_session ? $booking->last_session->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">بداية المونتاج:</span> 
                                {{ $booking->montage_start ? $booking->montage_start->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">تسليم نهائي:</span> 
                                {{ $booking->final_delivery ? $booking->final_delivery->format('Y-m-d') : '-' }}
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('employee.photography-booking.show', $booking) }}" 
                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                <i class="fas fa-eye ml-1"></i> عرض
                            </a>
                            @if($booking->created_by_employee == $employeeId || $booking->assigned_person_id == $employeeId)
                                <a href="{{ route('employee.photography-booking.edit', $booking) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                    <i class="fas fa-edit ml-1"></i> تعديل
                                </a>
                            @endif
                            @if($booking->created_by_employee == $employeeId)
                                <form action="{{ route('employee.photography-booking.destroy', $booking) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" 
                                            onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fas fa-trash ml-1"></i> حذف
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $bookings->withQueryString()->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-camera text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد حجوزات</h3>
                <p class="text-gray-600 mb-6">لم يتم العثور على أي حجوزات مخصصة لك</p>
                <a href="{{ route('employee.photography-booking.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة حجز جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
