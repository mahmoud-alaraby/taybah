@extends($layout)

@section('title', 'محادثة مع ' . $otherNames)
@section('page-title', 'محادثة مع ' . $otherNames)
@section('page-subtitle', 'المحادثات الداخلية')

@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden flex flex-col" style="min-height: 420px;">
        {{-- Header --}}
        <div class="px-4 py-3 border-b bg-gray-50">
            <div class="flex items-center justify-between">
                <a href="{{ auth('admin')->check() ? route('admin.conversations.index') : route('employee.conversations.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                    <i class="fas fa-arrow-right ml-1"></i> رجوع للمحادثات
                </a>
                <span class="font-medium text-gray-900">{{ $otherNames }}</span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3" style="max-height: 320px;">
            @forelse($messages as $message)
                @php
                    $sender = $message->participation->messageable ?? null;
                    $isMe = $sender && $sender->getKey() === $currentUser->getKey() && $sender->getMorphClass() === $currentUser->getMorphClass();
                @endphp
                <div class="flex {{ $isMe ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-[80%] rounded-lg px-3 py-2 {{ $isMe ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                        @if(!$isMe && $sender)
                            <div class="text-xs text-gray-500 mb-0.5">{{ $sender->name ?? 'مستخدم' }}</div>
                        @endif
                        <div class="break-words">{{ e($message->body) }}</div>
                        <div class="text-xs mt-1 {{ $isMe ? 'text-red-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">لا توجد رسائل بعد. اكتب رسالة أدناه.</p>
            @endforelse
        </div>

        @if(method_exists($messages, 'links'))
            <div class="px-4 py-2 border-t">
                {{ $messages->links() }}
            </div>
        @endif

        {{-- Send form --}}
        <div class="p-4 border-t bg-gray-50">
            <form action="{{ route($sendRoute, $conversation->id) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="body" value="{{ old('body') }}" placeholder="اكتب رسالتك..." required
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                    maxlength="5000" />
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    إرسال
                </button>
            </form>
            @error('body')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
@endsection
