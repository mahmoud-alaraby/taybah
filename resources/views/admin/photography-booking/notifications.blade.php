@extends('admin.layouts.app')
@section('title', 'إشعارات الحجوزات')
@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold mb-4">إشعارات الحجوزات</h1>
    <table class="min-w-full bg-white rounded shadow overflow-x-auto">
        <thead>
            <tr>
                <th class="px-4 py-2">البيان</th>
                <th class="px-4 py-2">الحجز</th>
                <th class="px-4 py-2">منشئ الحجز</th>
                <th class="px-4 py-2">تاريخ الإشعار</th>
                <th class="px-4 py-2">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $notification)
                <tr class="@if(!$notification->is_read) bg-blue-50 @endif hover:bg-gray-100">
                    <td class="px-4 py-2">{{ $notification->message }}</td>
                    <td class="px-4 py-2">#{{ $notification->booking_id }}</td>
                    <td class="px-4 py-2">
                        {{ optional($notification->employee)->name ?? 'غير محدد' }}
                    </td>
                    <td class="px-4 py-2">{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-2">
                        @if($notification->is_read)
                            <span class="text-green-600">مقروء</span>
                        @else
                            <span class="text-red-600">غير مقروء</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">لا توجد إشعارات</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
