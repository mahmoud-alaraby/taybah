{{-- resources/views/admin/customer_communication/index.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-extrabold mb-6">التواصل مع العملاء</h1>
<!-- resources/views/admin/customer_communication/index.blade.php -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        
        <!-- إجمالي العملاء -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">العملاء الكليون</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12z"/>
                </svg>
            </div>
        </div>

        <!-- عملاء طلبوا مكالمات -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عملاء طلبوا مكالمات</p>
                    <p class="text-2xl font-bold">{{ $stats['calls_pending'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0"/>
                </svg>
            </div>
        </div>

        <!-- عملاء طلبوا زيارات -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">عملاء طلبوا زيارات</p>
                    <p class="text-2xl font-bold">{{ $stats['visits_pending'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
            </div>
        </div>

    </div>
</div>


    <form method="GET" class="mb-6 max-w-xs">
        <select name="filter" onchange="this.form.submit()" class="w-full border rounded p-2">
            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>الكل</option>
            <option value="calls_pending" {{ $filter == 'calls_pending' ? 'selected' : '' }}>عملاء انتظار المكالمات</option>
            <option value="visits_pending" {{ $filter == 'visits_pending' ? 'selected' : '' }}>عملاء انتظار الزيارات</option>
            <option value="read" {{ $filter == 'read' ? 'selected' : '' }}>مقرؤة</option>
            <option value="unread" {{ $filter == 'unread' ? 'selected' : '' }}>غير مقرؤة</option>
        </select>
    </form>

    <div class="overflow-x-auto rounded shadow bg-white">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border p-3 text-right">العميل</th>
                    <th class="border p-3 text-right">الهاتف</th>
                    <th class="border p-3 text-right">الموظف</th>
                    <th class="border p-3 text-right">عدد الرسائل</th>
                    <th class="border p-3 text-right">آخر تواصل</th>
                    <th class="border p-3 text-center">الحالة</th>
                    <th class="border p-3 text-center">الإشعارات</th>
                    <th class="border p-3 text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($communications as $comm)
                <tr class="{{ $comm->unread_messages > 0 ? 'bg-yellow-50 font-semibold' : '' }}">
                    <td class="border p-3 text-right">{{ $comm->customer_name }}</td>
                    <td class="border p-3 text-right">{{ $comm->phone }}</td>
                    <td class="border p-3 text-right">{{ $comm->employee_name ?? '-' }}</td>
                    <td class="border p-3 text-right">{{ $comm->total_messages }}</td>
                    <td class="border p-3 text-right">{{ \Carbon\Carbon::parse($comm->last_message_time)->format('Y-m-d H:i') }}</td>
                    <td class="border p-3 text-center">
                        @if($comm->unread_messages > 0)
                            <span class="text-red-600 font-bold">غير مقروءة</span>
                        @else
                            <span class="text-green-600 font-bold">مقرؤة</span>
                        @endif
                    </td>
                    <td class="border p-3 text-center">
                        @if($comm->unread_messages > 0)
                            <div class="inline-flex items-center bg-red-600 text-white rounded-full w-8 h-8 justify-center animate-pulse cursor-pointer" title="{{ $comm->unread_messages }} رسائل جديدة">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M21 12c0 3.866-3.582 7-8 7-1.52 0-2.937-.438-4.095-1.16l-4.518 1.106 1.106-4.518c-.722-1.158-1.16-2.575-1.16-4.095 0-4.418 3.134-8 7-8 5.418 0 7 3.134 7 7z"/>
                                </svg>
                                <span class="sr-only">رسائل جديدة</span>
                            </div>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td class="border p-3 text-center">
                        <a href="{{ route('admin.customer-communication.show', $comm->potential_customer_id) }}" class="inline-block bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">عرض</a>
                        @if($comm->unread_messages > 0)
                        <form action="{{ route('admin.customer-communication.markRead', $comm->potential_customer_id) }}" method="POST" class="inline-block ml-2">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">تعليم كمقروء</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center p-4 font-semibold text-gray-500">لا توجد اتصالات حتى الآن.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $communications->appends(request()->query())->links() }}
    </div>
</div>
@endsection