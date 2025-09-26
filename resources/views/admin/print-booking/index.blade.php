{{-- resources/views/admin/print-booking/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'نظام حجز الطباعة')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-print text-red-600 ml-2"></i>
                    نظام حجز الطباعة
                </h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم جميع حجوزات الطباعة</p>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Add Booking Button -->
                <a href="{{ route('admin.print-booking.create') }}" 
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

            <!-- جاري العمل -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
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

            <!-- مكتملة -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
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

            <!-- ديون معدومة -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">ديون معدومة</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'bad_debt')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <!-- هذا الشهر -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">هذا الشهر</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- اليوم -->
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">اليوم</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
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
            <form method="GET" action="{{ route('admin.print-booking.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
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
                    <a href="{{ route('admin.print-booking.index') }}" 
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
                    قائمة حجوزات الطباعة
                </h3>
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    إجمالي: {{ $bookings->total() }} حجز
                </span>
            </div>
        </div>

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
                                أنشئ بواسطة
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                اسم العميل
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                رقم الجوال
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                أول أوردر
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                آخر أوردر
                            </th>
                            <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                عدد الأوردرات
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
                                    @if($booking->employeeCreator)
                                        <span class="text-xs font-bold text-green-600">{{ $booking->employeeCreator->name }}</span>
                                    @elseif($booking->creator)
                                        {{ $booking->creator->name }}
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">{{ Str::limit($booking->client_name, 12) }}</div>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    @if($booking->client_phone)
                                        <a href="tel:{{ $booking->client_phone }}" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="fas fa-phone text-xs ml-1"></i>{{ $booking->client_phone }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->first_order ? $booking->first_order->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900">
                                    {{ $booking->last_order ? $booking->last_order->format('m-d') : '-' }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $booking->orders_count ?? 1 }}
                                    </span>
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
                                            <i class="fas fa-clock text-xs ml-1"></i>
                                            جاري
                                        </span>
                                    @elseif($booking->status == 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check text-xs ml-1"></i>
                                            مكتمل
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle text-xs ml-1"></i>
                                            ديون
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.print-booking.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded" title="عرض">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.print-booking.edit', $booking) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors p-1 rounded" title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.print-booking.destroy', $booking) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors p-1 rounded" 
                                                    onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
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
                                    <a href="tel:{{ $booking->client_phone }}" class="text-blue-600 hover:text-blue-800">{{ $booking->client_phone }}</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">الأوردرات:</span> 
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">{{ $booking->orders_count ?? 1 }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">أول أوردر:</span> 
                                {{ $booking->first_order ? $booking->first_order->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">آخر أوردر:</span> 
                                {{ $booking->last_order ? $booking->last_order->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">تسليم أولي:</span> 
                                {{ $booking->initial_delivery ? $booking->initial_delivery->format('Y-m-d') : '-' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">تسليم نهائي:</span> 
                                {{ $booking->final_delivery ? $booking->final_delivery->format('Y-m-d') : '-' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.print-booking.show', $booking) }}" 
                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                <i class="fas fa-eye ml-1"></i> عرض
                            </a>
                            <a href="{{ route('admin.print-booking.edit', $booking) }}" 
                               class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                <i class="fas fa-edit ml-1"></i> تعديل
                            </a>
                            <form action="{{ route('admin.print-booking.destroy', $booking) }}" 
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" 
                                        onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                    <i class="fas fa-trash ml-1"></i> حذف
                                </button>
                            </form>
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
                    <i class="fas fa-print text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد حجوزات</h3>
                <p class="text-gray-600 mb-6">لم يتم العثور على أي حجوزات تطابق معايير البحث</p>
                <a href="{{ route('admin.print-booking.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة حجز جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection