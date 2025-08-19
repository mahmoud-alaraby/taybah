@extends('employee.layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto h-[600px] flex flex-col">

    <h1 class="text-2xl font-bold mb-6">التواصل بخصوص العميل: {{ $customer->customer_name }}</h1>

    {{-- لا يظهر زر حذف جميع الملاحظات للموظف --}}
    {{-- أزلت زر حذف جميع الملاحظات من هنا --}}

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

                        @if(!$isAdminReply && !$msg->is_read_by_employee)
                            <span class="ml-2 text-red-600 font-semibold">رسالة جديدة</span>
                        @endif
                    </div>

                    {{-- أيقونة حذف ملاحظة واحدة تظهر فقط لملاحظات الموظف --}}
                    @if(!$isAdminReply && $msg->employee_id == auth()->id())
                    <form method="POST" action="{{ route('employee.customer-communication.note.destroy', $msg->id) }}" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟');">
                        @csrf
                        @method('DELETE')
                        <!-- <button type="submit" class="text-red-600 hover:text-red-800 p-1 rounded" 
                            title="حذف الملاحظة">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button> -->
                    </form>
                    @endif
                </div>
            </div>
        @endforeach

    </div>

    {{-- Form to add employee note --}}
    <form action="{{ route('employee.customer-communication.store', $customer->id) }}" method="POST" class="mt-4 flex space-x-3 max-w-4xl">
        @csrf
        <input name="notes" required placeholder="اكتب ملاحظتك هنا..." 
            class="flex-grow border rounded p-3 resize-none text-right" rows="4">
        <button type="submit" 
            class="bg-red-700 hover:bg-red-800 text-white rounded px-6 py-4 font-semibold transition duration-300 shadow-lg shadow-red-500/50 flex items-center justify-center">
            إرسال
        </button>
    </form>

</div>
@endsection
