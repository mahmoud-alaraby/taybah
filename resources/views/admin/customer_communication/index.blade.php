@extends('admin.layouts.app')

@section('title', 'متابعة العملاء المحتملين')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('admin.customer-communication.index') }}" 
               class="px-4 py-2 rounded-md bg-blue-600 text-white">
                <i class="fas fa-user ml-2"></i>
                كل العملاء
            </a>
            <!-- أي فلترة إضافية -->
        </div>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('admin.customer-communication.storageManagement') }}"
               class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                <i class="fas fa-hdd ml-1"></i>
                إدارة المساحة
            </a>
        </div>
    </div>

    <!-- إحصائيات أعلى الصفحة -->
    <div class="flex space-x-4 mb-4 space-x-reverse">
        <div class="px-3 py-2 bg-teal-100 text-teal-700 rounded">
            <i class="fas fa-phone mr-1"></i>
            عملاء ينتظرون مكالمة: <strong>{{ $callPendingCount }}</strong>
        </div>
        <div class="px-3 py-2 bg-pink-100 text-pink-700 rounded">
            <i class="fas fa-building mr-1"></i>
            عملاء ينتظرون زيارة: <strong>{{ $visitPendingCount }}</strong>
        </div>
    </div>

    <!-- Chats List -->
    <div class="bg-white shadow rounded-lg">
        @if($chats->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($chats as $chat)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $chat->potentialCustomer->customer_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            الموظف المسؤول: {{ $chat->employee->name ?? 'غير محدد' }}
                                        </p>
                                        @if($chat->messages->first())
                                            <p class="text-sm text-gray-600 mt-1">
                                                آخر رسالة: {{ $chat->messages->first()->content ?? 'مرفق' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 space-x-reverse">
                                @if($chat->unread_count > 0)
                                    <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                        {{ $chat->unread_count }}
                                    </span>
                                @endif
                                <div class="text-sm text-gray-500">
                                    {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : 'لا توجد رسائل' }}
                                </div>
                                <div class="flex space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.customer-communication.show', $chat->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2">
                                        <i class="fas fa-comment"></i>
                                    </a>
                                    <button onclick="deleteChat({{ $chat->id }})"
                                            class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-user text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد شاتات</h3>
                <p class="text-gray-600 mb-4">ابدأ بإنشاء شات جديد للمراسلة مع الموظف حول العميل</p>
                <form action="{{ route('admin.customer-communication.createChat') }}" method="POST" class="inline-block">
                    @csrf
                    <input type="hidden" name="customer_id" value="">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء شات جديد
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteChat(chatId) {
    Swal.fire({
        title: 'حذف الشات',
        text: 'هل أنت متأكد من حذف هذا الشات؟ سيتم حذف جميع الرسائل والملفات!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc143c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/customer-communication/${chatId}`;
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfField);

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
