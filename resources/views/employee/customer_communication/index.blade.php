{{-- resources/views/employee/customer_communication/index.blade.php --}}
@extends('employee.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-bold mb-6">العملاء المطلوب التواصل معهم</h1>

    {{-- Filter dropdown --}}
    <form method="GET" class="mb-6 max-w-xs">
        <select name="filter" onchange="this.form.submit()" class="w-full border rounded p-2">
            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>الكل</option>
            <option value="calls_pending" {{ $filter == 'calls_pending' ? 'selected' : '' }}>عملاء انتظار المكالمات</option>
            <option value="visits_pending" {{ $filter == 'visits_pending' ? 'selected' : '' }}>عملاء انتظار الزيارات</option>
            <option value="contacted" {{ $filter == 'contacted' ? 'selected' : '' }}>تم التواصل</option>
            <option value="not_contacted" {{ $filter == 'not_contacted' ? 'selected' : '' }}>لم يتم التواصل</option>
        </select>
    </form>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- إجمالي العملاء -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">العملاء الكليون</p>
                        <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12z" />
                    </svg>
                </div>
            </div>

            <!-- عملاء طلبوا مكالمات -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">عملاء طلبوا مكالمات</p>
                        <p class="text-2xl font-bold">{{ $stats['calls_pending'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0" />
                    </svg>
                </div>
            </div>

            <!-- عملاء طلبوا زيارات -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">عملاء طلبوا زيارات</p>
                        <p class="text-2xl font-bold">{{ $stats['visits_pending'] }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($paginated as $customer)
            <div class="bg-white shadow rounded p-6 flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-bold mb-2">{{ $customer->customer_name }}</h2>
                    <p class="text-gray-700 mb-2">📞 {{ $customer->phone }}</p>
                    <p class="text-gray-500 mb-4">{{ $customer->work_description }}</p>
                    <p class="font-bold flex items-center space-x-2">
                        الحالة:
                        @if($customer->employee_contacted_status === 'contacted')
                            <span class="text-red-700 bg-red-100 px-2 rounded">تم التواصل</span>
                            @php
                                $hasUnread = DB::table('customer_communications')->where('potential_customer_id', $customer->id)->where('is_read_by_employee', 0)->count();
                            @endphp
                            @if($hasUnread)
                                <span class="inline-flex items-center bg-red-600 text-white rounded-full w-6 h-6 justify-center animate-pulse"
                                      title="{{ $hasUnread }} رسائل جديدة">
                                    {{ $hasUnread }}
                                </span>
                            @endif
                        @elseif($customer->employee_contacted_status === 'pending')
                            <span class="text-yellow-700 bg-yellow-100 px-2 rounded">تواصل جارٍ</span>
                        @else
                            <span class="text-gray-700 bg-gray-100 px-2 rounded">لم يتم التواصل</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('employee.customer-communication.show', $customer->id) }}"
                   class="mt-4 block bg-red-600 hover:bg-red-700 text-white rounded px-5 py-2 text-center font-semibold transition duration-300">
                    أدخل للتواصل
                </a>
            </div>
        @empty
        <p class="col-span-3 text-center text-gray-500 font-semibold">لا يوجد عملاء مطلوب التواصل معهم حالياً</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $paginated->appends(request()->query())->links() }}
    </div>
</div>
@endsection
