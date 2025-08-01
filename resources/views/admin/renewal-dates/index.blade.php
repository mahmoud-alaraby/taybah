{{-- resources/views/admin/renewal-dates/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'نظام مواعيد التجديد')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-calendar-alt text-red-600 mr-2"></i>
                    نظام مواعيد التجديد
                </h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم جميع مواعيد التجديد</p>
            </div>
            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Add Event Button -->
                <a href="{{ route('admin.renewal-dates.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة حدث جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold">نجح!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            <strong class="font-bold">خطأ!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
            <!-- إجمالي الأحداث -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي الأحداث</p>
                        <p class="text-2xl font-bold">{{ $statistics['total'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- الأحداث النشطة -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">الأحداث النشطة</p>
                        <p class="text-2xl font-bold">{{ $statistics['active'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- قادمة (3 أيام) -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">قادمة (3 أيام)</p>
                        <p class="text-2xl font-bold">{{ $statistics['upcoming'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- اليوم -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">اليوم</p>
                        <p class="text-2xl font-bold">{{ $statistics['today'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a9 9 0 100 18 9 9 0 000-18z"></path>
                    </svg>
                </div>
            </div>

            <!-- متأخرة -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">متأخرة</p>
                        <p class="text-2xl font-bold">{{ $statistics['overdue'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Notifications -->
    @if($todayRenewals->count() > 0 || $upcomingRenewals->count() > 0 || $overdueRenewals->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- أحداث اليوم -->
            @if($todayRenewals->count() > 0)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-day text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="text-blue-800 font-semibold">أحداث اليوم</h4>
                            <p class="text-blue-600 text-sm">{{ $todayRenewals->count() }} حدث يحتاج إجراء اليوم</p>
                        </div>
                    </div>
                </div>
                <div class="max-h-48 overflow-y-auto space-y-2">
                    @foreach($todayRenewals as $renewal)
                    <div class="bg-white rounded-lg p-3 border border-blue-100">
                        <h6 class="font-medium text-gray-900 text-sm">{{ $renewal->title }}</h6>
                        @if($renewal->amount)
                            <p class="text-xs text-gray-600">المبلغ: {{ number_format($renewal->amount, 2) }} ريال</p>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('admin.renewal-dates.show', $renewal) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- الأحداث القادمة -->
            @if($upcomingRenewals->count() > 0)
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div>
                            <h4 class="text-yellow-800 font-semibold">قادمة خلال 3 أيام</h4>
                            <p class="text-yellow-600 text-sm">{{ $upcomingRenewals->count() }} حدث للتحضير له</p>
                        </div>
                    </div>
                </div>
                <div class="max-h-48 overflow-y-auto space-y-2">
                    @foreach($upcomingRenewals as $renewal)
                    <div class="bg-white rounded-lg p-3 border border-yellow-100">
                        <h6 class="font-medium text-gray-900 text-sm">{{ $renewal->title }}</h6>
                        <p class="text-xs text-gray-600">{{ $renewal->renewal_date->format('Y-m-d') }}</p>
                        @if($renewal->amount)
                            <p class="text-xs text-gray-600">المبلغ: {{ number_format($renewal->amount, 2) }} ريال</p>
                        @endif
                        <p class="text-xs text-yellow-600 mt-1">باقي {{ abs($renewal->getDaysUntilRenewal()) }} يوم</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.renewal-dates.show', $renewal) }}" 
                               class="text-xs text-yellow-600 hover:text-yellow-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- الأحداث المتأخرة -->
            @if($overdueRenewals->count() > 0)
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-semibold">أحداث متأخرة</h4>
                            <p class="text-red-600 text-sm">{{ $overdueRenewals->count() }} حدث يحتاج معالجة عاجلة</p>
                        </div>
                    </div>
                </div>
                <div class="max-h-48 overflow-y-auto space-y-2">
                    @foreach($overdueRenewals as $renewal)
                    <div class="bg-white rounded-lg p-3 border border-red-100">
                        <h6 class="font-medium text-gray-900 text-sm">{{ $renewal->title }}</h6>
                        <p class="text-xs text-gray-600">{{ $renewal->renewal_date->format('Y-m-d') }}</p>
                        @if($renewal->amount)
                            <p class="text-xs text-gray-600">المبلغ: {{ number_format($renewal->amount, 2) }} ريال</p>
                        @endif
                        <p class="text-xs text-red-600 mt-1">متأخر {{ abs($renewal->getDaysUntilRenewal()) }} يوم</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.renewal-dates.show', $renewal) }}" 
                               class="text-xs text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search text-red-600 mr-2"></i>
                البحث والتصفية
            </h3>
        </div>
        <div class="p-6">
        <form method="GET" action="{{ route('admin.renewal-dates.index') }}">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- البحث -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
            <input type="text" name="search"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                   value="{{ request('search') }}" placeholder="البحث في العنوان أو الوصف">
        </div>

        <!-- الحالة -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                <option value="" {{ request('status') == '' ? 'selected' : '' }}>جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>

        <!-- التكرار -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">التكرار</label>
            <select name="frequency"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                <option value="" {{ request('frequency') == '' ? 'selected' : '' }}>جميع الأنواع</option>
                <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>سنوي</option>
                <option value="quarterly" {{ request('frequency') == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>شهري</option>
            </select>
        </div>

        <!-- التاريخ -->
        <!-- <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ</label>
            <input type="date" name="date"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                   value="{{ request('date') }}">
        </div> -->
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
            <i class="fas fa-search mr-2"></i>
            بحث
        </button>
        <a href="{{ route('admin.renewal-dates.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors text-center inline-flex items-center justify-center">
            <i class="fas fa-times mr-2"></i>
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
                    <i class="fas fa-table text-red-600 mr-2"></i>
                    قائمة مواعيد التجديد
                </h3>
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    إجمالي: {{ $renewalDates->total() }} حدث
                </span>
            </div>
        </div>

        @if($renewalDates->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التجديد</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التكرار</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنشئ</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المتبقي</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($renewalDates as $renewal)
                        <tr class="hover:bg-gray-50 transition-colors 
                        @if($renewal->isOverdue()) bg-red-50 
                        @elseif($renewal->isToday()) bg-blue-50
                        @elseif($renewal->isUpcoming(3)) bg-yellow-50 
                        @endif">
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($renewal->title, 30) }}</div>
                                @if($renewal->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($renewal->description, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $renewal->renewal_date->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $renewal->renewal_date->format('l') }}</div>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $renewal->getFrequencyDisplayName() }}
                                </span>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                @if($renewal->amount)
                                    <span class="font-bold text-green-600">{{ number_format($renewal->amount, 2) }} ريال</span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">
                                @if($renewal->status == 'active')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>نشط
                                    </span>
                                @elseif($renewal->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check-circle mr-1"></i>مكتمل
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>ملغي
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $renewal->getCreatorName() }}
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                @if($renewal->status === 'active')
                                    @php $days = $renewal->getDaysUntilRenewal(); @endphp
                                    @if($days < 0)
                                        <span class="text-red-600 font-bold">متأخر {{ abs($days) }} يوم</span>
                                    @elseif($days == 0)
                                        <span class="text-blue-600 font-bold">اليوم</span>
                                    @else
                                        <span class="text-green-600">{{ $days }} يوم</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.renewal-dates.show', $renewal) }}" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.renewal-dates.edit', $renewal) }}" 
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors p-1 rounded" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($renewal->status === 'active')
                                        <button onclick="markCompleted({{ $renewal->id }})" 
                                                class="text-green-600 hover:text-green-900 transition-colors p-1 rounded" title="مكتمل">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="renewEvent({{ $renewal->id }})" 
                                                class="text-purple-600 hover:text-purple-900 transition-colors p-1 rounded" title="تجديد">
                                            <i class="fas fa-refresh"></i>
                                        </button>
                                    @endif
                                    <!-- زر الحذف المُحدث - بدون onclick -->
                                    <button type="button" 
                                            class="delete-btn text-red-600 hover:text-red-900 transition-colors p-1 rounded" 
                                            data-title="{{ addslashes($renewal->title) }}"
                                            data-form-id="delete-form-{{ $renewal->id }}"
                                            title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <!-- نموذج الحذف المخفي -->
                                    <form id="delete-form-{{ $renewal->id }}" 
                                          action="{{ route('admin.renewal-dates.destroy', $renewal) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
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
                @foreach($renewalDates as $renewal)
                    <div class="p-6 hover:bg-gray-50 transition-colors 
                    @if($renewal->isOverdue()) bg-red-50 
                    @elseif($renewal->isToday()) bg-blue-50
                    @elseif($renewal->isUpcoming(3)) bg-yellow-50 
                    @endif">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ $renewal->title }}</h4>
                                @if($renewal->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($renewal->description, 60) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($renewal->status == 'active')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>نشط
                                    </span>
                                @elseif($renewal->status == 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check-circle mr-1"></i>مكتمل
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>ملغي
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                            <div>
                                <span class="font-medium text-gray-900">تاريخ التجديد:</span>
                                <div class="text-gray-800 font-bold">{{ $renewal->renewal_date->format('Y-m-d') }}</div>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">التكرار:</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">{{ $renewal->getFrequencyDisplayName() }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">المبلغ:</span>
                                @if($renewal->amount)
                                    <span class="text-green-600 font-bold">{{ number_format($renewal->amount, 2) }} ريال</span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">المتبقي:</span>
                                @if($renewal->status === 'active')
                                    @php $days = $renewal->getDaysUntilRenewal(); @endphp
                                    @if($days < 0)
                                        <span class="text-red-600 font-bold">متأخر {{ abs($days) }} يوم</span>
                                    @elseif($days == 0)
                                        <span class="text-blue-600 font-bold">اليوم</span>
                                    @else
                                        <span class="text-green-600">{{ $days }} يوم</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div class="col-span-2">
                                <span class="font-medium text-gray-900">المنشئ:</span>
                                {{ $renewal->getCreatorName() }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.renewal-dates.show', $renewal) }}" 
                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                <i class="fas fa-eye mr-1"></i> عرض
                            </a>
                            <a href="{{ route('admin.renewal-dates.edit', $renewal) }}" 
                               class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                <i class="fas fa-edit mr-1"></i> تعديل
                            </a>
                            @if($renewal->status === 'active')
                                <button onclick="markCompleted({{ $renewal->id }})" 
                                        class="text-green-600 hover:text-green-900 transition-colors">
                                    <i class="fas fa-check mr-1"></i> مكتمل
                                </button>
                                <button onclick="renewEvent({{ $renewal->id }})" 
                                        class="text-purple-600 hover:text-purple-900 transition-colors">
                                    <i class="fas fa-refresh mr-1"></i> تجديد
                                </button>
                            @endif
                            <!-- زر الحذف المُحدث للموبايل - بدون onclick -->
                            <button type="button" 
                                    class="delete-btn text-red-600 hover:text-red-900 transition-colors"
                                    data-title="{{ addslashes($renewal->title) }}"
                                    data-form-id="delete-form-mobile-{{ $renewal->id }}">
                                <i class="fas fa-trash mr-1"></i> حذف
                            </button>
                            
                            <!-- نموذج الحذف المخفي للموبايل -->
                            <form id="delete-form-mobile-{{ $renewal->id }}" 
                                  action="{{ route('admin.renewal-dates.destroy', $renewal) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($renewalDates->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $renewalDates->withQueryString()->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مواعيد تجديد</h3>
                <p class="text-gray-600 mb-6">لم يتم العثور على أي مواعيد تطابق معايير البحث</p>
                <a href="{{ route('admin.renewal-dates.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة حدث جديد
                </a>
            </div>
        @endif
    </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">تأكيد الحذف</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteMessage">
                    هل أنت متأكد من حذف هذا الحدث؟ لا يمكن التراجع عن هذا الإجراء.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmDeleteBtn" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 ml-3">
                    نعم، احذف
                </button>
                <button id="cancelDeleteBtn" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript مباشرة في الملف -->
<script>
// تحديد جميع المتغيرات في أول الكود
let formToSubmit = null;

// تعريف جميع الدوال قبل استخدامها
function showDeleteConfirm(title, formId) {
    formToSubmit = formId;
    document.getElementById('deleteMessage').innerHTML = 
        `هل أنت متأكد من حذف الحدث:<br><strong>"${title}"</strong>؟<br><small class="text-red-600">لا يمكن التراجع عن هذا الإجراء</small>`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function hideDeleteConfirm() {
    document.getElementById('deleteModal').classList.add('hidden');
    formToSubmit = null;
}

function executeDelete() {
    if (formToSubmit) {
        document.getElementById(formToSubmit).submit();
    }
    hideDeleteConfirm();
}

function markCompleted(id) {
    if (confirm('هل تريد تحديد هذا الحدث كمكتمل؟')) {
        const url = `{{ route('admin.renewal-dates.complete', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('تم تحديد الحدث كمكتمل بنجاح');
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorMessage('حدث خطأ أثناء التحديث');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('حدث خطأ أثناء التحديث');
        });
    }
}

function renewEvent(id) {
    if (confirm('هل تريد إنشاء حدث تجديد جديد لهذا الحدث؟')) {
        const url = `{{ route('admin.renewal-dates.renew', ':id') }}`.replace(':id', id);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message);
                setTimeout(() => location.reload(), 2000);
            } else {
                showErrorMessage('حدث خطأ أثناء التجديد');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage('حدث خطأ أثناء التجديد');
        });
    }
}

function showSuccessMessage(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'نجح!',
            text: message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}

function showErrorMessage(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'خطأ!',
            text: message,
            icon: 'error'
        });
    } else {
        alert(message);
    }
}

// إعداد مستمعي الأحداث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // أزرار التأكيد والإلغاء
    document.getElementById('confirmDeleteBtn').addEventListener('click', executeDelete);
    document.getElementById('cancelDeleteBtn').addEventListener('click', hideDeleteConfirm);
    
    // إغلاق النافذة عند النقر خارجها
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideDeleteConfirm();
        }
    });
    
    // إغلاق النافذة بمفتاح ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideDeleteConfirm();
        }
    });
    
    // إضافة مستمعي الأحداث لأزرار الحذف
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const formId = this.getAttribute('data-form-id');
            showDeleteConfirm(title, formId);
        });
    });
    
    // إخفاء رسائل النجاح/الخطأ تلقائياً بعد 5 ثواني
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
@endsection
