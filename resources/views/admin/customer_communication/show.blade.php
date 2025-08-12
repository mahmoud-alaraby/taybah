{{-- resources/views/admin/customer_communication/show.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto h-[600px] flex flex-col">

    <h1 class="text-2xl font-bold mb-6">التواصل مع العميل: {{ $customer->customer_name }}</h1>

    <div id="chat-container" class="flex-grow overflow-y-auto bg-gray-50 rounded p-5 border flex flex-col space-y-3 text-sm font-sans">

        @foreach($communications as $msg)
            @php 
                $isAdmin = is_null($msg->employee_id);
            @endphp
            <div class="max-w-xl {{ $isAdmin ? 'self-start text-left' : 'self-end text-right' }}">
                <div class="inline-block p-3 rounded-lg shadow-md max-w-[80%] break-words 
                    {{ $isAdmin ? 'bg-gray-300 text-gray-900' : 'bg-red-600 text-white' }}">
                    {!! nl2br(e($msg->notes)) !!}
                </div>
                <div class="text-xs text-gray-500 mt-1 {{ $isAdmin ? 'text-left' : 'text-right' }}">
                    {{ \Carbon\Carbon::parse($msg->created_at)->format('Y-m-d H:i') }}
                    @if($isAdmin && !$msg->is_read_by_admin && request()->routeIs('admin.*'))
                        <span class="ml-2 text-red-600 font-semibold">رسالة جديدة</span>
                    @endif
                    @if(!$isAdmin && !$msg->is_read_by_employee && request()->routeIs('employee.*'))
                        <span class="ml-2 text-red-600 font-semibold">رسالة جديدة</span>
                    @endif
                </div>
            </div>
        @endforeach

    </div>

    {{-- Form to add admin reply --}}
    @if(request()->routeIs('admin.*'))
    <form action="{{ route('admin.customer-communication.reply', $customer->id) }}" method="POST" class="mt-4 flex space-x-3 max-w-4xl">
        @csrf
        <textarea name="admin_reply" required placeholder="اكتب ردك هنا..." class="flex-grow border rounded p-3 resize-none text-right" rows="4"></textarea>
        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white rounded px-6 py-4 font-semibold transition duration-300 shadow-lg shadow-red-500/50 flex items-center justify-center">
            إرسال
        </button>
    </form>
    @endif

    {{-- Form to add employee note --}}
    @if(request()->routeIs('employee.*'))
    <form action="{{ route('employee.customer-communication.store', $customer->id) }}" method="POST" class="mt-4 flex space-x-3 max-w-4xl">
        @csrf
        <textarea name="notes" required placeholder="اكتب ملاحظتك هنا..." class="flex-grow border rounded p-3 resize-none text-right" rows="4"></textarea>
        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white rounded px-6 py-4 font-semibold transition duration-300 shadow-lg shadow-red-500/50 flex items-center justify-center">
            إرسال
        </button>
    </form>
    @endif

</div>
@endsection
