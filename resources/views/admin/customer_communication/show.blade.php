@extends('admin.layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto h-[600px] flex flex-col">

    <h1 class="text-2xl font-bold mb-6">التواصل بخصوص العميل: {{ $customer->customer_name }}</h1>

    {{-- زر حذف كل الملاحظات (يظهر فقط للأدمن) --}}
    <form method="POST" action="{{ route('admin.customer-communication.all-notes.destroy', $customer->id) }}" 
          onsubmit="return confirm('هل أنت متأكد من حذف جميع الملاحظات لهذا العميل؟');" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" 
            class="bg-red-600 text-white rounded px-4 py-2 hover:bg-red-700 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
            </svg>
            حذف جميع الملاحظات
        </button>
    </form>

    <div id="chat-container" class="flex-grow overflow-y-auto bg-gray-50 rounded p-5 border flex flex-col space-y-3 text-sm font-sans">

        @foreach($communications as $msg)
            @php 
                $isAdminReply = !empty($msg->admin_reply);
                $isOdd = $loop->index % 2 === 1;
            @endphp

            <div class="max-w-[80%] {{ $isOdd ? 'self-end text-right' : 'self-start text-right' }}">
                <div class="block p-3 rounded-lg shadow-md min-w-[80%] break-words
                    {{ $isOdd ? 'bg-gray-300 text-gray-900' : 'bg-red-600 text-white' }}">
                    {!! nl2br(e($isAdminReply ? $msg->admin_reply : $msg->notes)) !!}
                </div>

                <div class="flex justify-between items-center mt-1 text-xs text-gray-500 {{ $isOdd ? 'text-left' : 'text-right' }}">
                    <div>
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('Y-m-d H:i') }}

                        @if(!$isAdminReply && !$msg->is_read_by_admin)
                            <span class="ml-2 text-red-600 font-semibold">رسالة جديدة</span>
                        @endif
                    </div>

                    {{-- أيقونة حذف ملاحظة واحدة (بدون تقييد لأنه أدمن) --}}
                    <form method="POST" action="{{ route('admin.customer-communication.note.destroy', $msg->id) }}" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded" 
                            title="حذف الملاحظة">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

    </div>

    {{-- Form to add admin reply --}}
    <form action="{{ route('admin.customer-communication.reply', $customer->id) }}" method="POST" class="mt-4 flex space-x-3 max-w-4xl">
        @csrf
        <input name="admin_reply" required placeholder="اكتب ردك هنا..." 
            class="flex-grow border rounded p-3 resize-none text-right" />
        <button type="submit" 
            class="bg-red-700 hover:bg-red-800 text-white rounded px-4 py-3 font-semibold transition duration-300 shadow-lg shadow-red-500/50 flex items-center justify-center">
            إرسال
        </button>
    </form>

</div>
@endsection
