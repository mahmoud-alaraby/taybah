@extends('admin.layouts.app')

@section('title', 'التواصل مع الموظف بخصوص العميل: ' . $customer->customer_name)
@section('page-title', 'التواصل مع الموظف')
@section('page-subtitle', 'بخصوص العميل: ' . $customer->customer_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/voice-chat.css') }}">
<style>
/* تحسينات إضافية للإدارة */
.admin-chat-header {
    background: linear-gradient(135deg, #dc2626, #991b1b);
}

.admin-message-bubble {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

.admin-voice-player {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.15));
    border: 1px solid rgba(239, 68, 68, 0.2);
}
</style>
@endpush

@section('content')
<div class="flex h-[calc(100vh-200px)] bg-white shadow-xl rounded-2xl overflow-hidden">
    
    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        
        <!-- Chat Header -->
        <div class="flex items-center justify-between p-6 border-b admin-chat-header text-white">
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Customer Avatar -->
                <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-lg font-bold">
                    {{ substr($customer->customer_name, 0, 1) }}
                </div>
                
                <div>
                    <h3 class="font-semibold text-lg">{{ $customer->customer_name }}</h3>
                    <div class="flex items-center space-x-3 space-x-reverse text-red-100">
                        <span class="text-sm">{{ $customer->phone }}</span>
                        
                        @if($chat->employee)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-white bg-opacity-20 text-white">
                                <i class="fas fa-user ml-1"></i>
                                مع الموظف: {{ $chat->employee->name }}
                            </span>
                        @endif
                        
                        @php
                            $classifications = $customer->customer_classifications ?? [];
                            if (is_string($classifications)) {
                                $classifications = json_decode($classifications, true) ?? [];
                            }
                        @endphp
                        
                        @if(in_array('requested_call', $classifications))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orange-500 bg-opacity-80 text-white">
                                <i class="fas fa-phone ml-1"></i>
                                مكالمة مطلوبة
                            </span>
                        @endif
                        
                        @if(in_array('requested_visit', $classifications))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-500 bg-opacity-80 text-white">
                                <i class="fas fa-building ml-1"></i>
                                زيارة مطلوبة
                            </span>
                        @endif
                        
                        @if(in_array('difficult_customer', $classifications))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-500 bg-opacity-80 text-white">
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                عالي الأولوية
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <!-- Connection Status -->
                <div id="connectionStatus" class="flex items-center text-sm text-red-100">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                    متصل
                </div>
                
                <!-- Chat Actions -->
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button onclick="window.print()" 
                            class="text-red-100 hover:text-white p-2 transition-colors" title="طباعة المحادثة">
                        <i class="fas fa-print"></i>
                    </button>
                    
                    <a href="{{ route('admin.customer-communication.index') }}" 
                       class="text-red-100 hover:text-white p-2 transition-colors" title="العودة للقائمة">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Info Bar -->
        <div class="p-4 bg-gray-50 border-b customer-info-bar">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">وصف العمل:</span>
                        {{ $customer->work_description }}
                    </p>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                        {{ $chat->status === 'active' ? 'bg-green-100 text-green-800' : 
                           ($chat->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $chat->status === 'active' ? 'جاري التواصل' : 
                           ($chat->status === 'pending' ? 'في الانتظار' : 'تم التواصل') }}
                    </span>
                    
                    @if($chat->last_message_at)
                        <span class="text-xs text-gray-500">
                            آخر رسالة: {{ $chat->last_message_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 bg-gray-50 messages-container">
            <div id="messagesList" class="space-y-4">
                @if($messages->isEmpty())
                    <div class="text-center py-12 empty-state">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 empty-state-icon">
                            <i class="fas fa-comments text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">ابدأ التواصل مع الموظف</h3>
                        <p class="text-gray-500">اكتب رسالتك الأولى لمساعدة الموظف في التواصل مع هذا العميل</p>
                    </div>
                @else
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                            <div class="max-w-xs lg:max-w-md">
                                
                                <!-- Message Bubble -->
                                <div class="message-bubble {{ $message->sender_type === 'admin' ? 'admin' : 'employee' }} rounded-2xl px-4 py-3">
                                    
                                    @if($message->message_type === 'text')
                                        <p class="break-words leading-relaxed">{{ $message->content }}</p>
                                        
                                    @elseif($message->message_type === 'file')
                                        <div class="space-y-3">
                                            <!-- File Info -->
                                            <div class="flex items-center space-x-3 space-x-reverse">
                                                <div class="flex-shrink-0">
                                                    @php
                                                        $extension = pathinfo($message->file_name, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        $isPdf = strtolower($extension) === 'pdf';
                                                        $isDoc = in_array(strtolower($extension), ['doc', 'docx']);
                                                    @endphp
                                                    
                                                    @if($isImage)
                                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-image text-white"></i>
                                                        </div>
                                                    @elseif($isPdf)
                                                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-file-pdf text-white"></i>
                                                        </div>
                                                    @elseif($isDoc)
                                                        <div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-file-word text-white"></i>
                                                        </div>
                                                    @else
                                                        <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-file text-white"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium truncate">{{ $message->file_name }}</p>
                                                    <p class="text-xs opacity-75">{{ $message->file_size_formatted }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- File Actions -->
                                            <div class="flex space-x-2 space-x-reverse text-xs">
                                                @if($isImage)
                                                    <button onclick="previewImage('{{ $message->file_url }}', '{{ $message->file_name }}')" 
                                                            class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                        <i class="fas fa-eye ml-1"></i> معاينة
                                                    </button>
                                                @else
                                                    <button onclick="previewFile('{{ $message->file_url }}', '{{ $message->file_name }}', '{{ $extension }}')" 
                                                            class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                        <i class="fas fa-eye ml-1"></i> معاينة
                                                    </button>
                                                @endif
                                                <button onclick="copyToClipboard('{{ $message->file_url }}')" 
                                                        class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                    <i class="fas fa-copy ml-1"></i> نسخ الرابط
                                                </button>
                                                <a href="{{ $message->file_url }}" download="{{ $message->file_name }}" 
                                                   class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                    <i class="fas fa-download ml-1"></i> تحميل
                                                </a>
                                            </div>
                                        </div>
                                        
                                    @elseif($message->message_type === 'voice')
                                        <div class="space-y-3">
                                            <!-- Voice Message Header -->
                                            <div class="flex items-center space-x-2 space-x-reverse">
                                                <div class="w-8 h-8 rounded-full {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20' : 'bg-red-500' }} flex items-center justify-center">
                                                    <i class="fas fa-microphone text-white text-sm"></i>
                                                </div>
                                                <span class="text-sm opacity-90">رسالة صوتية</span>
                                                @if($message->duration)
                                                    <span class="text-xs opacity-75">
                                                        {{ $message->duration_formatted }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Enhanced Audio Player -->
                                            <div class="voice-player admin-voice-player rounded-xl p-3" data-message-id="{{ $message->id }}">
                                                <div class="flex items-center space-x-3 space-x-reverse">
                                                    <!-- Play/Pause Button -->
                                                    <button onclick="toggleVoicePlay(this, '{{ $message->file_url }}', {{ $message->id }})" 
                                                            class="w-10 h-10 rounded-full voice-btn {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-red-500 hover:bg-red-600' }} flex items-center justify-center transition-colors" 
                                                            data-playing="false">
                                                        <i class="fas fa-play text-white text-sm"></i>
                                                    </button>
                                                    
                                                    <!-- Enhanced Waveform/Progress -->
                                                    <div class="flex-1">
                                                        <div class="h-8 flex items-center justify-center space-x-1 space-x-reverse waveform-container">
                                                            @for($i = 0; $i < 25; $i++)
                                                                <div class="w-1 bg-current opacity-40 rounded-full waveform-bar transition-all duration-150" 
                                                                     style="height: {{ rand(15, 100) }}%; animation-delay: {{ $i * 0.08 }}s"></div>
                                                            @endfor
                                                        </div>
                                                        
                                                        <!-- Progress Bar -->
                                                        <div class="mt-2 h-1 bg-black bg-opacity-20 rounded-full overflow-hidden">
                                                            <div class="h-full bg-current opacity-60 rounded-full transition-all duration-150 progress-bar" style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Duration Display -->
                                                    <div class="text-right">
                                                        <span class="text-xs opacity-75 font-mono duration-display">
                                                            {{ $message->duration_formatted ?? '0:00' }}
                                                        </span>
                                                        <div class="text-xs opacity-50 current-time">0:00</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Hidden Audio Element -->
                                                <audio class="hidden voice-audio" preload="metadata" 
                                                       ontimeupdate="updateVoiceProgress(this)" 
                                                       onended="voiceEnded(this)"
                                                       onloadedmetadata="voiceLoaded(this)">
                                                    <source src="{{ $message->file_url }}" type="audio/webm">
                                                    <source src="{{ $message->file_url }}" type="audio/mpeg">
                                                    <source src="{{ $message->file_url }}" type="audio/wav">
                                                </audio>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Message Footer -->
                                    <div class="flex justify-between items-center mt-3 text-xs opacity-75">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <span>{{ $message->created_at->format('H:i') }}</span>
                                            @if($message->sender_type === 'admin' && $message->created_at->diffInMinutes(now()) <= 5)
                                                <button onclick="deleteMessage({{ $message->id }})" 
                                                        class="text-red-200 hover:text-red-100 transition-colors" title="حذف الرسالة">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        @if($message->sender_type === 'admin')
                                            <div class="flex items-center space-x-1">
                                                <i class="fas {{ $message->is_read ? 'fa-check-double text-red-200' : 'fa-check text-red-300' }}"></i>
                                                @if($message->is_read)
                                                    <span class="text-xs">{{ $message->read_at ? $message->read_at->format('H:i') : '' }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Sender Info -->
                                <div class="flex items-center mt-2 {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }} sender-info">
                                    @if($message->sender_type === 'employee')
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <div class="sender-avatar employee">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $message->sender_name }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <span class="text-xs text-gray-500">أنت (الإدارة)</span>
                                            <div class="sender-avatar admin">
                                                <i class="fas fa-user-tie text-white text-xs"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t bg-white p-6 message-input-form">
            <form id="messageForm" class="flex items-end space-x-4 space-x-reverse">
                @csrf
                <input type="hidden" id="messageType" value="text">
                <input type="hidden" id="chatId" value="{{ $chat->id }}">
                
                <!-- File Input -->
                <input type="file" id="fileInput" class="hidden" accept="*/*">
                
                <!-- Voice Button -->
                <button type="button" id="voiceBtn" onclick="toggleVoiceRecording()" 
                        class="flex-shrink-0 w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all duration-200 voice-record-btn btn-voice">
                    <i class="fas fa-microphone text-gray-600"></i>
                </button>
                
                <!-- Attachment Button -->
                <button type="button" onclick="toggleFileInput()" 
                        class="flex-shrink-0 w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-paperclip text-gray-600"></i>
                </button>
                
                <!-- Text Input -->
                <div class="flex-1 relative">
                    <input type="text" id="messageInput" placeholder="اكتب رسالتك للموظف لمساعدته في التواصل مع هذا العميل..." 
                           class="message-input w-full px-6 py-4 rounded-2xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:ring-opacity-20 pr-16 text-lg">
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            class="send-button absolute left-3 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-full flex items-center justify-center transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane text-white"></i>
                    </button>
                </div>
            </form>
            
            <!-- Recording Status -->
            <div id="recordingStatus" class="hidden mt-4 p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-white rounded-full mr-3 recording-pulse"></div>
                        <div>
                            <p class="font-medium">جاري التسجيل...</p>
                            <p class="text-sm text-red-100">اضغط الزر مرة أخرى للإنهاء أو Escape للإلغاء</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div id="recordingTime" class="text-xl font-mono">0:00</div>
                        <div class="text-xs text-red-100">مدة التسجيل</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="max-w-4xl max-h-full p-4">
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="imageTitle" class="text-lg font-semibold"></h3>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-4">
                <img id="previewImage" src="" alt="" class="max-w-full max-h-96 mx-auto">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/voice-handler.js') }}"></script>
<script>
// تعيين معرف الشات العام
window.chatId = {{ $chat->id }};

let lastMessageId = 0;
let refreshInterval = null;
let isPageVisible = true;

// متغيرات عامة للاستخدام في voice-handler.js
window.addNewMessageToChat = addNewMessageToChat;
window.scrollToBottom = scrollToBottom;
window.lastMessageId = lastMessageId;

// تتبع رؤية الصفحة
document.addEventListener('visibilitychange', function() {
    isPageVisible = !document.hidden;
    if (isPageVisible) {
        loadNewMessages();
    }
});

// بدء تحديث الرسائل كل 3 ثواني (أسرع من الموظف)
function startAutoRefresh() {
    refreshInterval = setInterval(loadNewMessages, 3000);
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

// تحميل الرسائل الجديدة
function loadNewMessages() {
    if (!isPageVisible) return;
    
    updateConnectionStatus('loading');
    
    fetch(`/admin/customer-communication/${window.chatId}/messages`)
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            updateConnectionStatus('connected');
            
            if (data.messages && data.messages.length > 0) {
                const newMessages = data.messages.filter(msg => msg.id > window.lastMessageId);
                
                if (newMessages.length > 0) {
                    newMessages.forEach(message => {
                        addNewMessageToChat(message);
                        window.lastMessageId = Math.max(window.lastMessageId || 0, message.id);
                    });
                    
                    scrollToBottom();
                    
                    if (!isPageVisible) {
                        showNewMessageNotification();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            updateConnectionStatus('error');
        });
}

// تحديث حالة الاتصال
function updateConnectionStatus(status) {
    const statusElement = document.getElementById('connectionStatus');
    
    switch (status) {
        case 'connected':
            statusElement.innerHTML = '<span class="w-2 h-2 bg-green-400 rounded-full mr-2 status-indicator connected"></span>متصل';
            break;
        case 'loading':
            statusElement.innerHTML = '<span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse status-indicator loading"></span>جاري التحديث...';
            break;
        case 'error':
            statusElement.innerHTML = '<span class="w-2 h-2 bg-red-400 rounded-full mr-2 status-indicator error"></span>خطأ في الاتصال';
            break;
    }
}

// إرسال الرسالة
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageType = document.getElementById('messageType').value;
    
    if (messageType === 'text') {
        sendTextMessage();
    } else if (messageType === 'file') {
        sendFileMessage();
    }
});

// إرسال رسالة نصية
function sendTextMessage() {
    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    
    if (!content) return;
    
    const formData = new FormData();
    formData.append('message_type', 'text');
    formData.append('content', content);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // تعطيل الزر مؤقتاً
    const submitBtn = document.querySelector('.send-button');
    submitBtn.disabled = true;
    
    fetch(`/admin/customer-communication/${window.chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        
        if (data.success) {
            input.value = '';
            addNewMessageToChat(data.message);
            window.lastMessageId = Math.max(window.lastMessageId || 0, data.message.id);
            scrollToBottom();
        } else {
            showError('حدث خطأ في إرسال الرسالة');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        console.error('Error:', error);
        showError('حدث خطأ في إرسال الرسالة');
    });
}

// تبديل إدخال الملف
function toggleFileInput() {
    const fileInput = document.getElementById('fileInput');
    const messageType = document.getElementById('messageType');
    
    if (messageType.value === 'file') {
        messageType.value = 'text';
        fileInput.value = '';
    } else {
        messageType.value = 'file';
        fileInput.click();
    }
}

// التعامل مع اختيار الملف
document.getElementById('fileInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            showError('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
            this.value = '';
            document.getElementById('messageType').value = 'text';
            return;
        }
        sendFileMessage();
    }
});

function sendFileMessage() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    const formData = new FormData();
    formData.append('message_type', 'file');
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // إظهار مؤشر التحميل
    const loadingMessage = addLoadingMessage('جاري رفع الملف...');
    
    fetch(`/admin/customer-communication/${window.chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // إزالة مؤشر التحميل
        removeLoadingMessage(loadingMessage);
        
        if (data.success) {
            fileInput.value = '';
            document.getElementById('messageType').value = 'text';
            addNewMessageToChat(data.message);
            window.lastMessageId = Math.max(window.lastMessageId || 0, data.message.id);
            scrollToBottom();
        } else {
            showError('حدث خطأ في إرسال الملف');
        }
    })
    .catch(error => {
        removeLoadingMessage(loadingMessage);
        console.error('Error:', error);
        showError('حدث خطأ في إرسال الملف');
    });
}

// إضافة رسالة جديدة للشات
function addNewMessageToChat(message) {
    const messagesList = document.getElementById('messagesList');
    
    if (document.querySelector(`[data-message-id="${message.id}"]`)) {
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${message.sender_type === 'admin' ? 'justify-end' : 'justify-start'} message-appear`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    if (message.sender_type !== 'admin') {
        messageDiv.classList.add('new-message-indicator');
        // إشعار صوتي للرسائل الجديدة من الموظفين
        playNotificationSound();
    }
    
    messageDiv.innerHTML = generateMessageHTML(message);
    messagesList.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.classList.remove('new-message-indicator');
    }, 3000);
}

function generateMessageHTML(message) {
    let contentHTML = '';
    
    if (message.message_type === 'text') {
        contentHTML = `<p class="break-words leading-relaxed">${escapeHtml(message.content)}</p>`;
    } else if (message.message_type === 'file') {
        const extension = message.file_name ? message.file_name.split('.').pop().toLowerCase() : '';
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
        const isPdf = extension === 'pdf';
        const isDoc = ['doc', 'docx'].includes(extension);
        
        let iconClass = 'fas fa-file';
        let iconColor = 'bg-gray-500';
        
        if (isImage) {
            iconClass = 'fas fa-image';
            iconColor = 'bg-green-500';
        } else if (isPdf) {
            iconClass = 'fas fa-file-pdf';
            iconColor = 'bg-red-500';
        } else if (isDoc) {
            iconClass = 'fas fa-file-word';
            iconColor = 'bg-blue-400';
        }
        
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 ${iconColor} rounded-lg flex items-center justify-center">
                            <i class="${iconClass} text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">${escapeHtml(message.file_name)}</p>
                        <p class="text-xs opacity-75">${message.file_size_formatted || '0 KB'}</p>
                    </div>
                </div>
                <div class="flex space-x-2 space-x-reverse text-xs">
                    ${isImage ? `<button onclick="previewImage('${message.file_url}', '${escapeHtml(message.file_name)}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-eye ml-1"></i> معاينة
                    </button>` : `<button onclick="previewFile('${message.file_url}', '${escapeHtml(message.file_name)}', '${extension}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-eye ml-1"></i> معاينة
                    </button>`}
                    <button onclick="copyToClipboard('${message.file_url}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-copy ml-1"></i> نسخ الرابط
                    </button>
                    <a href="${message.file_url}" download="${escapeHtml(message.file_name)}" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-download ml-1"></i> تحميل
                    </a>
                </div>
            </div>
        `;
    } else if (message.message_type === 'voice') {
        const uniqueId = `voice_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="w-8 h-8 rounded-full ${message.sender_type === 'admin' ? 'bg-white bg-opacity-20' : 'bg-red-500'} flex items-center justify-center">
                        <i class="fas fa-microphone text-white text-sm"></i>
                    </div>
                    <span class="text-sm opacity-90">رسالة صوتية</span>
                    <span class="text-xs opacity-75">${message.duration_formatted || '0:00'}</span>
                </div>
                
                <div class="voice-player admin-voice-player rounded-xl p-3" data-message-id="${message.id}">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <button onclick="toggleVoicePlay(this, '${message.file_url}', ${message.id})" 
                                class="w-10 h-10 rounded-full voice-btn ${message.sender_type === 'admin' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-red-500 hover:bg-red-600'} flex items-center justify-center transition-colors" 
                                data-playing="false">
                            <i class="fas fa-play text-white text-sm"></i>
                        </button>
                        
                        <div class="flex-1">
                            <div class="h-8 flex items-center justify-center space-x-1 space-x-reverse waveform-container">
                                ${Array.from({length: 25}, (_, i) => 
                                    `<div class="w-1 bg-current opacity-40 rounded-full waveform-bar transition-all duration-150" 
                                         style="height: ${Math.floor(Math.random() * 85) + 15}%; animation-delay: ${i * 0.08}s"></div>`
                                ).join('')}
                            </div>
                            
                            <div class="mt-2 h-1 bg-black bg-opacity-20 rounded-full overflow-hidden">
                                <div class="h-full bg-current opacity-60 rounded-full transition-all duration-150 progress-bar" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="text-xs opacity-75 font-mono duration-display">
                                ${message.duration_formatted || '0:00'}
                            </span>
                            <div class="text-xs opacity-50 current-time">0:00</div>
                        </div>
                    </div>
                    
                    <audio class="hidden voice-audio" preload="metadata" 
                           ontimeupdate="updateVoiceProgress(this)" 
                           onended="voiceEnded(this)"
                           onloadedmetadata="voiceLoaded(this)">
                        <source src="${message.file_url}" type="audio/webm">
                        <source src="${message.file_url}" type="audio/mpeg">
                        <source src="${message.file_url}" type="audio/wav">
                    </audio>
                </div>
            </div>
        `;
    }
    
    const readStatus = message.sender_type === 'admin' ? 
        `<div class="flex items-center space-x-1">
            <i class="fas ${message.is_read ? 'fa-check-double text-red-200' : 'fa-check text-red-300'}"></i>
            ${message.is_read && message.read_at ? `<span class="text-xs">${message.read_at}</span>` : ''}
        </div>` : '';
    
    return `
        <div class="max-w-xs lg:max-w-md">
            <div class="message-bubble ${message.sender_type === 'admin' ? 'admin' : 'employee'} rounded-2xl px-4 py-3">
                ${contentHTML}
                <div class="flex justify-between items-center mt-3 text-xs opacity-75">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span>${message.created_at}</span>
                        ${message.sender_type === 'admin' && message.created_at_minutes <= 5 ? 
                            `<button onclick="deleteMessage(${message.id})" class="text-red-200 hover:text-red-100 transition-colors" title="حذف الرسالة">
                                <i class="fas fa-trash text-xs"></i>
                            </button>` : ''
                        }
                    </div>
                    ${readStatus}
                </div>
            </div>
            
            <div class="flex items-center mt-2 ${message.sender_type === 'admin' ? 'justify-end' : 'justify-start'} sender-info">
                ${message.sender_type === 'employee' ? 
                    `<div class="flex items-center space-x-2 space-x-reverse">
                        <div class="sender-avatar employee">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <span class="text-xs text-gray-500">${escapeHtml(message.sender_name)}</span>
                    </div>` :
                    `<div class="flex items-center space-x-2 space-x-reverse">
                        <span class="text-xs text-gray-500">أنت (الإدارة)</span>
                        <div class="sender-avatar admin">
                            <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                    </div>`
                }
            </div>
        </div>
    `;
}

// وظائف مساعدة
function addLoadingMessage(text) {
    const messagesList = document.getElementById('messagesList');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'flex justify-end loading-message';
    loadingDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="rounded-2xl px-4 py-3 bg-gray-300 text-gray-600">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="animate-spin w-4 h-4 border-2 border-gray-500 border-t-transparent rounded-full loading-spinner"></div>
                    <span class="text-sm">${text}</span>
                </div>
            </div>
        </div>
    `;
    messagesList.appendChild(loadingDiv);
    scrollToBottom();
    return loadingDiv;
}

function removeLoadingMessage(loadingDiv) {
    if (loadingDiv && loadingDiv.parentNode) {
        loadingDiv.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            if (loadingDiv.parentNode) {
                loadingDiv.parentNode.removeChild(loadingDiv);
            }
        }, 300);
    }
}

// حذف الرسالة
function deleteMessage(messageId) {
    if (!confirm('هل أنت متأكد من حذف هذه الرسالة؟')) return;
    
    fetch(`/admin/customer-communication/message/${messageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            if (messageElement) {
                messageElement.classList.add('message-deleting');
                setTimeout(() => {
                    messageElement.remove();
                }, 300);
            }
            showSuccess('تم حذف الرسالة');
        } else {
            showError(data.message || 'حدث خطأ في حذف الرسالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('حدث خطأ في حذف الرسالة');
    });
}

// معاينة الصور
function previewImage(url, filename) {
    const modal = document.getElementById('imageModal');
    const image = document.getElementById('previewImage');
    const title = document.getElementById('imageTitle');
    
    image.src = url;
    title.textContent = filename;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// معاينة الملفات
function previewFile(url, filename, extension) {
    window.open(url, '_blank');
}

// نسخ الرابط
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showSuccess('تم نسخ الرابط!');
    }).catch(() => {
        // Fallback للمتصفحات القديمة
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showSuccess('تم نسخ الرابط!');
        } catch (err) {
            showError('فشل في نسخ الرابط');
        }
        document.body.removeChild(textArea);
    });
}

// التمرير لأسفل
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// إشعارات محسنة
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    const alert = document.createElement('div');
    alert.className = `notification-alert fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm ${type}`;
    alert.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span class="text-white">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // إزالة تلقائية بعد 4 ثواني
    setTimeout(() => {
        if (alert.parentNode) {
            alert.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => alert.remove(), 300);
        }
    }, 4000);
}

// إشعار صوتي للرسائل الجديدة
function playNotificationSound() {
    if (document.hidden) {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            
            oscillator.start();
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            console.log('Could not play notification sound:', e);
        }
    }
}

// تنظيف HTML
function escapeHtml(text) {
    if (typeof text !== 'string') return text;
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// تحديد آخر معرف رسالة
function initializeLastMessageId() {
    const messages = document.querySelectorAll('[data-message-id]');
    if (messages.length > 0) {
        const ids = Array.from(messages).map(msg => parseInt(msg.getAttribute('data-message-id')));
        window.lastMessageId = Math.max(...ids);
    }
}

// إغلاق نافذة الصورة عند النقر خارجها
document.getElementById('imageModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    initializeLastMessageId();
    startAutoRefresh();
    scrollToBottom();
    
    // تركيز على حقل الرسالة
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.focus();
    }
    
    // إعداد مفاتيح الاختصار
    document.addEventListener('keydown', function(e) {
        // Enter للإرسال السريع
        if (e.key === 'Enter' && !e.shiftKey && e.target.id === 'messageInput') {
            e.preventDefault();
            document.getElementById('messageForm').dispatchEvent(new Event('submit'));
        }
        
        // Escape لإغلاق النوافذ المنبثقة أو إيقاف التسجيل
        if (e.key === 'Escape') {
            const modal = document.getElementById('imageModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeImageModal();
            }
            
            if (window.voiceHandler && window.voiceHandler.isRecording) {
                window.voiceHandler.stopRecording();
            }
        }
    });
});

// إيقاف التحديث والتسجيل عند مغادرة الصفحة
window.addEventListener('beforeunload', function() {
    if (window.voiceHandler) {
        window.voiceHandler.cleanup();
    }
    stopAutoRefresh();
});

</script>
@endpush