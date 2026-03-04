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
        {{-- New chat: searchable users --}}
        <div class="p-3 border-b border-gray-200 bg-white relative">
            <form id="new-chat-form" action="{{ route($storeRoute) }}" method="POST" class="space-y-2">
                @csrf
                <input type="hidden" name="participant" id="new-chat-participant" value="" required />
                <div class="relative">
                    <input type="text" id="new-chat-search" placeholder="بحث عن مستخدم..." autocomplete="off"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2 pr-8 pl-2" />
                    <i class="fas fa-search absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <div id="new-chat-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg z-20">
                        @foreach($usersForNewChat as $u)
                            <button type="button" class="new-chat-option w-full text-right px-3 py-2 text-sm hover:bg-gray-100 border-b border-gray-50 last:border-0" data-value="{{ $u['value'] }}" data-label="{{ $u['label'] }}">
                                {{ $u['label'] }}
                            </button>
                        @endforeach
                        <div id="new-chat-no-results" class="hidden px-3 py-2 text-sm text-gray-500">لا توجد نتائج</div>
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                    <span id="new-chat-selected-label" class="flex-1 text-sm text-gray-500 truncate min-w-0">اختر مستخدماً...</span>
                    <button type="submit" id="new-chat-submit" disabled class="p-2 rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed" title="بدء محادثة">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </form>
            <script>
                (function() {
                    var form = document.getElementById('new-chat-form');
                    var searchInput = document.getElementById('new-chat-search');
                    var hiddenInput = document.getElementById('new-chat-participant');
                    var dropdown = document.getElementById('new-chat-dropdown');
                    var selectedLabel = document.getElementById('new-chat-selected-label');
                    var submitBtn = document.getElementById('new-chat-submit');
                    var noResults = document.getElementById('new-chat-no-results');
                    var options = document.querySelectorAll('.new-chat-option');
                    var allOptions = Array.prototype.map.call(options, function(o) { return { el: o, value: o.getAttribute('data-value'), label: o.getAttribute('data-label') }; });
                    function filter(q) {
                        q = (q || '').trim().toLowerCase();
                        var shown = 0;
                        allOptions.forEach(function(o) {
                            var match = !q || o.label.toLowerCase().indexOf(q) !== -1;
                            o.el.style.display = match ? '' : 'none';
                            if (match) shown++;
                        });
                        noResults.classList.toggle('hidden', shown > 0);
                    }
                    function select(value, label) {
                        hiddenInput.value = value || '';
                        selectedLabel.textContent = label || 'اختر مستخدماً...';
                        submitBtn.disabled = !value;
                        dropdown.classList.add('hidden');
                        searchInput.value = '';
                        filter('');
                    }
                    searchInput.addEventListener('focus', function() { dropdown.classList.remove('hidden'); filter(searchInput.value); });
                    searchInput.addEventListener('input', function() { filter(searchInput.value); });
                    searchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') { dropdown.classList.add('hidden'); searchInput.blur(); }
                    });
                    allOptions.forEach(function(o) {
                        o.el.addEventListener('click', function() { select(o.value, o.label); });
                    });
                    document.addEventListener('click', function(e) {
                        if (!form.contains(e.target)) dropdown.classList.add('hidden');
                    });
                })();
            </script>
        </div>
        {{-- List --}}
        <div class="flex-1 overflow-y-auto relative">
            @forelse($conversationList as $item)
                @php $hasUnread = !empty($item['unreadCount']) && (int)$item['unreadCount'] > 0; @endphp
                <div class="group group-item flex items-center gap-2 px-4 py-3 hover:bg-gray-100 border-b border-gray-100 {{ ($selectedConversation && $selectedConversation->id == $item['id']) ? 'bg-red-50 border-r-4 border-r-red-600' : '' }} {{ $hasUnread ? 'bg-red-50/50' : '' }}">
                    <a href="{{ route($showRoute, $item['id']) }}" class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="relative h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 flex-shrink-0">
                            <i class="fas fa-user"></i>
                            @if($hasUnread)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center text-xs font-bold text-white bg-red-600 rounded-full">{{ $item['unreadCount'] > 99 ? '99+' : $item['unreadCount'] }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 text-right">
                            <div class="font-medium truncate {{ $hasUnread ? 'text-gray-900 font-semibold' : 'text-gray-900' }}">{{ $item['otherNames'] ?: 'محادثة #' . $item['id'] }}</div>
                            @if(!empty($item['lastMessage']))
                                <div class="text-xs {{ $hasUnread ? 'text-gray-700' : 'text-gray-500' }} truncate">{{ $item['lastMessage'] }}</div>
                            @endif
                        </div>
                    </a>
                    <form method="POST" action="{{ route($destroyRoute, $item['id']) }}" class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('حذف هذه المحادثة من قائمتك؟');" onclick="event.stopPropagation()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500" title="حذف المحادثة">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </form>
                </div>
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
                            <div class="rounded-2xl px-1 py-2.5 {{ $isMe ? 'bg-red-600 text-white rounded-tr-sm' : 'bg-white text-gray-900 border border-gray-200 rounded-tl-sm shadow-sm' }}">
                                @if(!$isMe && $sender)
                                    <div class="text-xs font-medium text-gray-500 mb-0.5">{{ $sender->name ?? 'مستخدم' }}</div>
                                @endif
                                @if($message->body && $message->body !== '📎 مرفقات')
                                    <div class="break-words text-sm">{{ e($message->body) }}</div>
                                @endif
                                @if(!empty($message->data['attachments']))
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($message->data['attachments'] as $att)
                                            @php
                                                $url = asset('storage/' . $att['path']);
                                                $isImage = isset($att['mime']) && str_starts_with($att['mime'], 'image/');
                                                $isPdf = isset($att['mime']) && $att['mime'] === 'application/pdf';
                                                $previewType = $isImage ? 'image' : ($isPdf ? 'pdf' : 'file');
                                                $attName = $att['name'] ?? ($isImage ? 'صورة' : 'ملف');
                                            @endphp
                                            @if($isImage)
                                                <a href="{{ $url }}" class="chat-preview-link block cursor-pointer" data-preview-url="{{ $url }}" data-preview-type="image" data-preview-name="{{ $attName }}">
                                                    <img src="{{ $url }}" alt="{{ $attName }}" class="rounded-lg max-h-40 max-w-full object-cover border border-gray-200 hover:opacity-90" loading="lazy" />
                                                </a>
                                                <a href="{{ $url }}" class="chat-preview-link text-xs {{ $isMe ? 'text-red-200' : 'text-gray-500' }} hover:underline" data-preview-url="{{ $url }}" data-preview-type="image" data-preview-name="{{ $attName }}">{{ $attName }}</a>
                                            @else
                                                <a href="{{ $url }}" class="chat-preview-link inline-flex items-center gap-1.5 text-sm {{ $isMe ? 'text-red-100 hover:text-white' : 'text-red-600 hover:text-red-700' }} cursor-pointer" data-preview-url="{{ $url }}" data-preview-type="{{ $previewType }}" data-preview-name="{{ $attName }}">
                                                    <i class="fas fa-paperclip"></i>
                                                    <span>{{ $attName }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <div class="text-xs mt-1 mx-2 flex items-center gap-3 {{ $isMe ? 'text-red-200' : 'text-gray-400' }}">
                                    <span class="font-[10px]">{{ $message->created_at->format('H:i') }} · {{ $message->created_at->format('d/m/Y') }}</span>
                                    @if($isMe)
                                        @php $status = $messageStatusMap[$message->id ?? $message->getKey()] ?? 'delivered'; @endphp
                                        <span class="inline-flex items-center" title="{{ $status === 'seen' ? 'مقروءة' : ($status === 'delivered' ? 'تم التسليم' : 'تم الإرسال') }}">
                                            @if($status === 'seen')
                                                <i class="fas fa-check-double text-blue-300" aria-hidden="true"></i>
                                            @else
                                                <i class="fas fa-check-double text-current opacity-80" aria-hidden="true"></i>
                                            @endif
                                        </span>
                                    @endif
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
                @if(session('error'))
                    <p class="text-red-600 text-sm mb-2">{{ session('error') }}</p>
                @endif
                <form id="chat-send-form" action="{{ route($sendRoute, $selectedConversation->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <div id="chat-file-preview" class="hidden flex flex-wrap gap-2 items-center p-2 rounded-lg bg-gray-100 border border-gray-200 text-sm"></div>
                    <div class="flex gap-2 items-end">
                        <label class="p-3 rounded-xl border border-gray-300 hover:bg-gray-50 cursor-pointer text-gray-600" title="إرفاق ملف (صور، PDF، Word، حتى 10 ميجا)">
                            <i class="fas fa-paperclip"></i>
                            <input id="chat-file-input" type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" class="hidden" />
                        </label>
                        <input type="text" name="body" value="{{ old('body') }}" placeholder="اكتب رسالة أو أرفق ملفاً..."
                            autocomplete="off"
                            class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm py-3 px-4"
                            maxlength="5000" />
                        <button type="submit" class="p-3 rounded-xl bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">صور، PDF، Word، Excel، نص، ZIP — حد أقصى 5 ملفات و 10 ميجابايت لكل ملف.</p>
                </form>
                <script>
                    (function() {
                        var form = document.getElementById('chat-send-form');
                        var fileInput = document.getElementById('chat-file-input');
                        var preview = document.getElementById('chat-file-preview');
                        if (!form || !fileInput || !preview) return;
                        function formatSize(bytes) {
                            if (bytes < 1024) return bytes + ' B';
                            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                        }
                        function removeFileAtIndex(indexToRemove) {
                            var files = fileInput.files;
                            if (!files || files.length <= 1) {
                                fileInput.value = '';
                                updatePreview();
                                return;
                            }
                            var dt = new DataTransfer();
                            for (var j = 0; j < files.length; j++) {
                                if (j !== indexToRemove) dt.items.add(files[j]);
                            }
                            fileInput.files = dt.files;
                            updatePreview();
                        }
                        function updatePreview() {
                            var files = fileInput.files;
                            if (!files || files.length === 0) {
                                preview.classList.add('hidden');
                                preview.innerHTML = '';
                                return;
                            }
                            preview.classList.remove('hidden');
                            var html = '<span class="text-gray-600 ml-1">المحدد للرفع:</span>';
                            for (var i = 0; i < files.length; i++) {
                                var f = files[i];
                                var isImg = f.type.indexOf("image/") === 0 && f.type.indexOf("svg") === -1;
                                html += '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white border border-gray-200">';
                                if (isImg) {
                                    var url = URL.createObjectURL(f);
                                    html += '<img src="' + url + '" alt="" class="h-8 w-8 object-cover rounded" />';
                                }
                                html += '<span class="max-w-[120px] truncate" title="' + (f.name || 'ملف') + '">' + (f.name || 'ملف') + '</span>';
                                html += '<span class="text-gray-400 text-xs">(' + formatSize(f.size) + ')</span>';
                                html += '<button type="button" class="p-0.5 rounded text-red-600 hover:bg-red-50 hover:text-red-700" title="إزالة" data-chat-remove-index="' + i + '"><i class="fas fa-times text-xs"></i></button>';
                                html += '</span>';
                            }
                            html += '<button type="button" class="text-red-600 hover:text-red-700 text-xs px-2 py-1" id="chat-clear-files">مسح الكل</button>';
                            preview.innerHTML = html;
                            document.getElementById('chat-clear-files').onclick = function() {
                                fileInput.value = '';
                                preview.classList.add('hidden');
                                preview.innerHTML = '';
                            };
                            preview.querySelectorAll('[data-chat-remove-index]').forEach(function(btn) {
                                btn.onclick = function() { removeFileAtIndex(parseInt(btn.getAttribute('data-chat-remove-index'), 10)); };
                            });
                        }
                        fileInput.addEventListener('change', updatePreview);
                        form.addEventListener('submit', function() {
                            setTimeout(function() { fileInput.value = ''; preview.classList.add('hidden'); preview.innerHTML = ''; }, 0);
                        });
                    })();
                </script>
                @error('body')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('attachments.*')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @if(config('musonza_chat.broadcasts'))
            {{-- Real-time chat config (Pusher + Echo) --}}
            <script>
                window.chatRealtime = {
                    enabled: true,
                    conversationId: {{ $selectedConversation->id }},
                    currentUser: { id: {{ $currentUser->getKey() }}, type: @json($currentUser->getMorphClass()) },
                    pusherKey: @json(config('broadcasting.connections.pusher.key')),
                    pusherCluster: @json(config('broadcasting.connections.pusher.options.cluster', 'mt1')),
                    authEndpoint: @json(url('/broadcasting/auth')),
                    csrfToken: @json(csrf_token()),
                    eventName: @json(\Musonza\Chat\Eventing\MessageWasSent::class),
                    sendUrl: @json(route($sendRoute, $selectedConversation->id)),
                };
            </script>
            @endif
            {{-- Preview modal for images and files --}}
            <div id="chat-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70" role="dialog" aria-modal="true" aria-label="معاينة الملف">
                <div class="relative max-w-4xl max-h-[90vh] w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between px-4 py-2 border-b bg-gray-100 flex-shrink-0">
                        <span id="chat-preview-title" class="font-medium text-gray-900 truncate"></span>
                        <div class="flex items-center gap-1">
                            <span id="chat-preview-counter" class="text-sm text-gray-500 ml-2"></span>
                            <button type="button" id="chat-preview-close" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600" aria-label="إغلاق">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0 overflow-auto p-2 flex items-center justify-center bg-gray-900/10 relative w-full">
                        <button type="button" id="chat-preview-prev" class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 hover:text-red-600 disabled:opacity-40 disabled:pointer-events-none" aria-label="السابق">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div id="chat-preview-content" class="w-full flex-1 min-h-0 flex items-center justify-center"></div>
                        <button type="button" id="chat-preview-next" class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 hover:text-red-600 disabled:opacity-40 disabled:pointer-events-none" aria-label="التالي">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('chat-messages')?.scrollTo({ top: 1e9, behavior: 'smooth' });
                (function() {
                    var modal = document.getElementById('chat-preview-modal');
                    var content = document.getElementById('chat-preview-content');
                    var titleEl = document.getElementById('chat-preview-title');
                    var counterEl = document.getElementById('chat-preview-counter');
                    var closeBtn = document.getElementById('chat-preview-close');
                    var prevBtn = document.getElementById('chat-preview-prev');
                    var nextBtn = document.getElementById('chat-preview-next');
                    if (!modal || !content) return;

                    var previewItems = [];
                    var currentIndex = 0;

                    function buildPreviewItems() {
                        previewItems = [];
                        document.querySelectorAll('.chat-preview-link').forEach(function(a) {
                            var url = a.getAttribute('data-preview-url');
                            if (!url) return;
                            previewItems.push({
                                url: url,
                                type: a.getAttribute('data-preview-type') || 'file',
                                name: a.getAttribute('data-preview-name') || ''
                            });
                        });
                    }

                    function renderContent(url, type, name) {
                        content.innerHTML = '';
                        if (type === 'image') {
                            var img = document.createElement('img');
                            img.src = url;
                            img.alt = name || '';
                            img.className = 'w-full max-h-[80vh] object-contain rounded-lg';
                            content.appendChild(img);
                        } else if (type === 'pdf') {
                            var iframe = document.createElement('iframe');
                            iframe.src = url + '#view=FitH';
                            iframe.className = 'w-full h-[80vh] min-h-[60vh] border-0 rounded-lg bg-white flex-1';
                            iframe.title = name || 'PDF';
                            content.appendChild(iframe);
                        } else {
                            var wrap = document.createElement('div');
                            wrap.className = 'w-full text-center p-6';
                            var icon = document.createElement('div');
                            icon.className = 'text-4xl text-gray-400 mb-3';
                            icon.innerHTML = '<i class="fas fa-file-alt"></i>';
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = name || 'file';
                            link.className = 'inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
                            link.textContent = 'تحميل الملف';
                            wrap.appendChild(icon);
                            wrap.appendChild(link);
                            content.appendChild(wrap);
                        }
                    }

                    function openPreviewAt(index) {
                        if (index < 0 || index >= previewItems.length) return;
                        currentIndex = index;
                        var item = previewItems[currentIndex];
                        titleEl.textContent = item.name || '';
                        counterEl.textContent = previewItems.length > 1 ? (currentIndex + 1) + ' / ' + previewItems.length : '';
                        renderContent(item.url, item.type, item.name);
                        if (prevBtn) prevBtn.disabled = currentIndex === 0;
                        if (nextBtn) nextBtn.disabled = currentIndex === previewItems.length - 1;
                        if (prevBtn && nextBtn) {
                            prevBtn.style.visibility = previewItems.length > 1 ? 'visible' : 'hidden';
                            nextBtn.style.visibility = previewItems.length > 1 ? 'visible' : 'hidden';
                        }
                    }

                    function openPreview(url, type, name) {
                        titleEl.textContent = name || '';
                        counterEl.textContent = '';
                        renderContent(url, type, name);
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                        if (prevBtn && nextBtn) { prevBtn.style.visibility = 'hidden'; nextBtn.style.visibility = 'hidden'; }
                    }

                    function closePreview() {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = '';
                        content.innerHTML = '';
                        previewItems = [];
                    }

                    function onKeydown(e) {
                        if (!modal.classList.contains('flex')) return;
                        if (e.key === 'Escape') { closePreview(); return; }
                        if (previewItems.length <= 1) return;
                        if (e.key === 'ArrowLeft') {
                            e.preventDefault();
                            if (currentIndex > 0) openPreviewAt(currentIndex - 1);
                        } else if (e.key === 'ArrowRight') {
                            e.preventDefault();
                            if (currentIndex < previewItems.length - 1) openPreviewAt(currentIndex + 1);
                        }
                    }

                    buildPreviewItems();
                    document.querySelectorAll('.chat-preview-link').forEach(function(a, index) {
                        a.addEventListener('click', function(e) {
                            e.preventDefault();
                            buildPreviewItems();
                            var url = a.getAttribute('data-preview-url');
                            var type = a.getAttribute('data-preview-type') || 'file';
                            var name = a.getAttribute('data-preview-name') || '';
                            var idx = Array.prototype.indexOf.call(document.querySelectorAll('.chat-preview-link'), a);
                            if (previewItems.length > 1 && idx >= 0) {
                                currentIndex = idx;
                                openPreviewAt(currentIndex);
                                modal.classList.remove('hidden');
                                modal.classList.add('flex');
                                document.body.style.overflow = 'hidden';
                            } else {
                                openPreview(url, type, name);
                                modal.classList.remove('hidden');
                                modal.classList.add('flex');
                                document.body.style.overflow = 'hidden';
                            }
                        });
                    });

                    if (prevBtn) prevBtn.addEventListener('click', function() { if (currentIndex > 0) openPreviewAt(currentIndex - 1); });
                    if (nextBtn) nextBtn.addEventListener('click', function() { if (currentIndex < previewItems.length - 1) openPreviewAt(currentIndex + 1); });
                    closeBtn.addEventListener('click', closePreview);
                    modal.addEventListener('click', function(e) { if (e.target === modal) closePreview(); });
                    document.addEventListener('keydown', onKeydown);
                })();
            </script>
            {{-- Real-time: Echo + AJAX send (when CHAT_REALTIME_ENABLED and Pusher configured) --}}
            @if(config('musonza_chat.broadcasts'))
            <script>
                (function() {
                    var cfg = window.chatRealtime;
                    if (!cfg || !cfg.enabled || !cfg.pusherKey) return;

                    function escapeHtml(s) {
                        if (!s) return '';
                        var d = document.createElement('div');
                        d.textContent = s;
                        return d.innerHTML;
                    }
                    function removeEmptyState() {
                        var container = document.getElementById('chat-messages');
                        if (!container) return;
                        var empty = container.querySelector('.flex.flex-col.items-center.justify-center');
                        if (empty && empty.textContent.indexOf('لا توجد رسائل') !== -1) empty.remove();
                    }
                    function buildMessageHtml(msg, isMe) {
                        var sender = msg.sender || {};
                        var name = sender.name || 'مستخدم';
                        var initial = (name || '؟').charAt(0);
                        var body = msg.body && msg.body !== '📎 مرفقات' ? '<div class="break-words text-sm">' + escapeHtml(msg.body) + '</div>' : '';
                        var attachments = (msg.data && msg.data.attachments) || [];
                        var attHtml = '';
                        if (attachments.length) {
                            attHtml = '<div class="mt-2 space-y-1.5">';
                            attachments.forEach(function(att) {
                                var url = (window.location.origin || '') + '/storage/' + (att.path || '');
                                var isImage = att.mime && att.mime.indexOf('image/') === 0;
                                var isPdf = att.mime === 'application/pdf';
                                var previewType = isImage ? 'image' : (isPdf ? 'pdf' : 'file');
                                var attName = att.name || (isImage ? 'صورة' : 'ملف');
                                if (isImage) {
                                    attHtml += '<a href="' + url + '" class="chat-preview-link block cursor-pointer" data-preview-url="' + escapeHtml(url) + '" data-preview-type="image" data-preview-name="' + escapeHtml(attName) + '"><img src="' + url + '" alt="' + escapeHtml(attName) + '" class="rounded-lg max-h-40 max-w-full object-cover border border-gray-200 hover:opacity-90" loading="lazy" /></a>';
                                    attHtml += '<a href="' + url + '" class="chat-preview-link text-xs ' + (isMe ? 'text-red-200' : 'text-gray-500') + ' hover:underline" data-preview-url="' + escapeHtml(url) + '" data-preview-type="image" data-preview-name="' + escapeHtml(attName) + '">' + escapeHtml(attName) + '</a>';
                                } else {
                                    attHtml += '<a href="' + url + '" class="chat-preview-link inline-flex items-center gap-1.5 text-sm ' + (isMe ? 'text-red-100 hover:text-white' : 'text-red-600 hover:text-red-700') + ' cursor-pointer" data-preview-url="' + escapeHtml(url) + '" data-preview-type="' + previewType + '" data-preview-name="' + escapeHtml(attName) + '"><i class="fas fa-paperclip"></i><span>' + escapeHtml(attName) + '</span></a>';
                                }
                            });
                            attHtml += '</div>';
                        }
                        var time = msg.created_at ? (typeof msg.created_at === 'string' ? msg.created_at : (msg.created_at.date || '')) : '';
                        if (time) {
                            var d = new Date(time);
                            time = d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0') + ' · ' + d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear();
                        }
                        var bubbleClass = isMe ? 'bg-red-600 text-white rounded-tr-sm' : 'bg-white text-gray-900 border border-gray-200 rounded-tl-sm shadow-sm';
                        var timeClass = isMe ? 'text-red-200' : 'text-gray-400';
                        var status = (msg.status || 'delivered');
                        var checkIcon = isMe ? '<span class="inline-flex items-center" title="' + (status === 'seen' ? 'مقروءة' : 'تم التسليم') + '"><i class="fas fa-check-double ' + (status === 'seen' ? 'text-blue-300' : 'text-current opacity-80') + '" aria-hidden="true"></i></span>' : '';
                        var row = '<div class="flex ' + (isMe ? 'justify-start' : 'justify-end') + '">';
                        row += '<div class="max-w-[75%] flex ' + (isMe ? 'flex-row' : 'flex-row-reverse') + '">';
                        if (!isMe) row += '<div class="flex-shrink-0 ml-2 mt-1 h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs">' + escapeHtml(initial) + '</div>';
                        row += '<div class="rounded-2xl px-4 py-2.5 ' + bubbleClass + '">';
                        if (!isMe) row += '<div class="text-xs font-medium text-gray-500 mb-0.5">' + escapeHtml(name) + '</div>';
                        row += body + attHtml;
                        row += '<div class="text-xs mt-1 flex items-center gap-1 ' + timeClass + '">' + (time || '') + checkIcon + '</div></div></div></div>';
                        return row;
                    }
                    function appendMessage(msg) {
                        var container = document.getElementById('chat-messages');
                        if (!container) return;
                        removeEmptyState();
                        var sender = msg.sender || {};
                        var senderId = sender.id != null ? sender.id : sender;
                        var senderType = sender.type || (msg.sender && msg.sender.morph_class) || '';
                        if (typeof senderId === 'object') { senderId = sender.id; senderType = sender.morph_class || sender.type || ''; }
                        var isMe = (String(senderId) === String(cfg.currentUser.id)) && (String(senderType || '') === String(cfg.currentUser.type || ''));
                        var html = buildMessageHtml(msg, isMe);
                        container.insertAdjacentHTML('beforeend', html);
                        container.scrollTop = container.scrollHeight;
                    }

                    var form = document.getElementById('chat-send-form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            if (!cfg.enabled) return;
                            e.preventDefault();
                            var bodyInput = form.querySelector('input[name="body"]');
                            var fileInput = form.querySelector('input[name="attachments[]"]');
                            var fd = new FormData(form);
                            var submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;
                            fetch(cfg.sendUrl, {
                                method: 'POST',
                                body: fd,
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                credentials: 'same-origin'
                            }).then(function(r) {
                                if (!r.ok) return r.json().then(function(j) { throw new Error(j.message || 'فشل الإرسال'); }).catch(function() { throw new Error('فشل الإرسال'); });
                                return r.json();
                            }).then(function(data) {
                                if (data.message) appendMessage(data.message);
                                if (bodyInput) bodyInput.value = '';
                                if (fileInput) fileInput.value = '';
                                var preview = document.getElementById('chat-file-preview');
                                if (preview) { preview.classList.add('hidden'); preview.innerHTML = ''; }
                            }).catch(function(err) {
                                alert(err.message || 'حدث خطأ أثناء الإرسال');
                            }).finally(function() {
                                if (submitBtn) submitBtn.disabled = false;
                            });
                            return false;
                        });
                    }

                    function initEcho() {
                        if (window._chatEchoInstance) {
                            return;
                        }
                        var s1 = document.createElement('script');
                        s1.src = 'https://js.pusher.com/8.3.0/pusher.min.js';
                        s1.onload = function() {
                            var s2 = document.createElement('script');
                            s2.src = 'https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js';
                            s2.onload = function() {
                                var EchoClass = window.Echo;
                                if (!EchoClass) return;
                                window._chatEchoInstance = new EchoClass({
                                    broadcaster: 'pusher',
                                    key: cfg.pusherKey,
                                    cluster: cfg.pusherCluster,
                                    forceTLS: true,
                                    authEndpoint: cfg.authEndpoint,
                                    auth: { headers: { 'X-CSRF-TOKEN': cfg.csrfToken, 'Accept': 'application/json' } }
                                });
                                window._chatEchoInstance.private('mc-chat-conversation.' + cfg.conversationId).listen(cfg.eventName, function(e) {
                                    if (e && e.message) appendMessage(e.message);
                                });
                            };
                            document.head.appendChild(s2);
                        };
                        document.head.appendChild(s1);
                    }
                    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initEcho);
                    else initEcho();
                })();
            </script>
            @endif
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
