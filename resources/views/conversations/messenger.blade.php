@extends($layout)

@section('title', $selectedConversation ? 'محادثة مع ' . $otherNames : 'المحادثات')
@section('page-title', $selectedConversation ? $otherNames : 'المحادثات')
@section('page-subtitle', 'المحادثات الداخلية')

@php
    $indexUrl = auth('admin')->check() ? route('admin.conversations.index') : route('employee.conversations.index');
@endphp

@section('content')
<div class="h-[calc(100vh-8rem)] flex rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" dir="rtl">
    {{-- Left: Conversation list (sidebar) --}}
    <aside class="w-80 flex-shrink-0 flex flex-col border-l border-gray-200 bg-gray-50/80">
        {{-- New chat --}}
        <div class="p-3 border-b border-gray-200 bg-white">
            <form action="{{ route($storeRoute) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="participant" required class="flex-1 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2">
                    <option value="">محادثة جديدة...</option>
                    @foreach($usersForNewChat as $u)
                        <option value="{{ $u['value'] }}">{{ $u['label'] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="p-2 rounded-lg bg-red-600 text-white hover:bg-red-700" title="بدء محادثة">
                    <i class="fas fa-plus"></i>
                </button>
            </form>
        </div>
        {{-- List --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($conversationList as $item)
                <a href="{{ route($showRoute, $item['id']) }}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 border-b border-gray-100 {{ ($selectedConversation && $selectedConversation->id == $item['id']) ? 'bg-red-50 border-r-4 border-r-red-600' : '' }}">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="min-w-0 flex-1 text-right">
                        <div class="font-medium text-gray-900 truncate">{{ $item['otherNames'] ?: 'محادثة #' . $item['id'] }}</div>
                        @if(!empty($item['lastMessage']))
                            <div class="text-xs text-gray-500 truncate">{{ $item['lastMessage'] }}</div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-4 text-center text-gray-500 text-sm">لا توجد محادثات. ابدأ محادثة جديدة من الأعلى.</div>
            @endforelse
        </div>
    </aside>

    {{-- Right: Chat area --}}
    <main class="flex-1 flex flex-col min-w-0 bg-white" dir="rtl">
        @if($selectedConversation)
            {{-- Chat header --}}
            <header class="flex items-center gap-3 px-4 py-3 border-b border-gray-200 bg-white flex-shrink-0">
                <a href="{{ $indexUrl }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 md:hidden">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                    <i class="fas fa-user"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="font-semibold text-gray-900 truncate">{{ $otherNames }}</h2>
                    <p class="text-xs text-gray-500">المحادثات المباشرة</p>
                </div>
            </header>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50" id="chat-messages">
                @forelse($messages as $message)
                    @php
                        $sender = $message->participation->messageable ?? null;
                        $isMe = $sender && $sender->getKey() === $currentUser->getKey() && $sender->getMorphClass() === $currentUser->getMorphClass();
                    @endphp
                    <div class="flex {{ $isMe ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[75%] flex {{ $isMe ? 'flex-row' : 'flex-row-reverse' }}">
                            @if(!$isMe && $sender)
                                <div class="flex-shrink-0 ml-2 mt-1 h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs">
                                    {{ mb_substr($sender->name ?? '؟', 0, 1) }}
                                </div>
                            @endif
                            <div class="rounded-2xl px-4 py-2.5 {{ $isMe ? 'bg-red-600 text-white rounded-tr-sm' : 'bg-white text-gray-900 border border-gray-200 rounded-tl-sm shadow-sm' }}">
                                @if(!$isMe && $sender)
                                    <div class="text-xs font-medium text-gray-500 mb-0.5">{{ $sender->name ?? 'مستخدم' }}</div>
                                @endif
                                <div class="break-words text-sm">{{ e($message->body) }}</div>
                                <div class="text-xs mt-1 {{ $isMe ? 'text-red-200' : 'text-gray-400' }}">
                                    {{ $message->created_at->format('H:i') }} · {{ $message->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-500 py-12">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                        <p>لا توجد رسائل بعد. اكتب رسالة للبدء.</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($messages, 'links') && $messages->hasPages())
                <div class="px-4 py-2 border-t bg-white">
                    {{ $messages->links() }}
                </div>
            @endif

            {{-- Send form --}}
            <div class="p-4 border-t bg-white flex-shrink-0">
                @if(session('success'))
                    <p class="text-green-600 text-sm mb-2">{{ session('success') }}</p>
                @endif
                <form action="{{ route($sendRoute, $selectedConversation->id) }}" method="POST" class="flex gap-2 items-end">
                    @csrf
                    <input type="text" name="body" value="{{ old('body') }}" placeholder="اكتب رسالة..." required
                        class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4"
                        maxlength="5000" />
                    <button type="submit" class="p-3 rounded-xl bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                @error('body')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <script>
                document.getElementById('chat-messages')?.scrollTo({ top: 1e9, behavior: 'smooth' });
            </script>
        @else
            {{-- Empty state: no conversation selected --}}
            <div class="flex-1 flex flex-col items-center justify-center text-gray-500 p-8 bg-gray-50/50">
                <div class="rounded-full bg-gray-200 p-6 mb-4">
                    <i class="fas fa-comments text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">المحادثات</h3>
                <p class="text-sm text-center max-w-sm mb-6">اختر محادثة من القائمة أو ابدأ محادثة جديدة مع مدير أو موظف.</p>
                @if(session('success'))
                    <p class="text-green-600 text-sm mb-2">{{ session('success') }}</p>
                @endif
                @if(session('error'))
                    <p class="text-red-600 text-sm mb-2">{{ session('error') }}</p>
                @endif
            </div>
        @endif
    </main>
</div>
@endsection
