@extends('admin.layouts.app')

@section('title', $chat->potentialCustomer->customer_name ?? 'شات العميل')
@section('page-title', $chat->potentialCustomer->customer_name ?? 'شات العميل')
@section('page-subtitle', 'رقم العميل: ' . ($chat->potentialCustomer->phone ?? '---'))

@section('content')
<style>
.waveform-bar { animation: wave 1.5s ease-in-out infinite alternate; }
@keyframes wave {
    0% { transform: scaleY(0.3); opacity: 0.4;}
    100% { transform: scaleY(1); opacity: 0.8;}
}
.waveform-bar.playing { animation: wave 0.8s ease-in-out infinite alternate; opacity: 1;}
.audio-play-btn:hover { transform: scale(1.05);}
.audio-play-btn:active { transform: scale(0.95);}
.recording-pulse { animation: pulse 1s infinite;}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1;}
    50% { transform: scale(1.1); opacity: 0.8;}
    100% { transform: scale(1); opacity: 1;}
}
.message-appear { animation: slideIn 0.3s ease-out;}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(10px);}
    to { opacity: 1; transform: translateY(0);}
}
.new-message-indicator { animation: newMessagePulse 2s ease-in-out 3;}
@keyframes newMessagePulse {
    0%,100% { background-color: rgba(59,130,246,0.1);}
    50% { background-color: rgba(59,130,246,0.3);}
}
</style>

<div class="flex h-[calc(100vh-200px)] bg-white shadow rounded-lg overflow-hidden">
    <!-- Chat Messages Area -->
    <div class="flex-1 flex flex-col">
        <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div>
                <h3 class="font-medium">
                    {{ $chat->potentialCustomer->customer_name ?? '' }}
                </h3>
                <p class="text-sm text-blue-100">
                    رقم العميل: {{ $chat->potentialCustomer->phone ?? '---' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="showChatInfo()" class="text-white hover:text-blue-200 p-2" title="معلومات الشات">
                    <i class="fas fa-info-circle"></i>
                </button>
                <form method="POST" action="{{ route('admin.customer-communication.destroy', $chat) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-white hover:text-red-300 p-2" title="حذف الشات" onclick="return confirm('هل أنت متأكد؟ كل الرسائل ستحذف!')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                <button onclick="clearChatFiles()" class="text-white hover:text-blue-200 p-2" title="مسح الملفات">
                    <i class="fas fa-broom"></i>
                </button>
                <a href="{{ route('admin.customer-communication.index') }}" class="text-white hover:text-blue-200 p-2">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50">
            <div id="messagesList" class="space-y-4">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }} message-appear" data-message-id="{{ $message->id }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="rounded-2xl px-4 py-3 {{ $message->sender_type === 'admin' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 shadow-sm border' }}">
                                @if($message->message_type === 'text')
                                    <p class="break-words">{{ $message->content }}</p>
                                @elseif($message->message_type === 'file')
                                    @php
                                        $extension = pathinfo($message->file_name, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                                        $isPdf = strtolower($extension) === 'pdf';
                                        $isDoc = in_array(strtolower($extension), ['doc','docx']);
                                    @endphp
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
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
                                        <div class="flex gap-2 text-xs">
                                            <button onclick="previewFile('{{ $message->file_url }}', '{{ $message->file_name }}', '{{ $extension }}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"> <i class="fas fa-eye ml-1"></i> معاينة </button>
                                            <button onclick="copyToClipboard('{{ $message->file_url }}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"> <i class="fas fa-copy ml-1"></i> نسخ الرابط </button>
                                            <a href="{{ $message->file_url }}" target="_blank" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"> <i class="fas fa-download ml-1"></i> تحميل </a>
                                        </div>
                                    </div>
                                @elseif($message->message_type === 'voice')
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20' : 'bg-blue-500' }} flex items-center justify-center">
                                                <i class="fas fa-microphone text-white text-sm"></i>
                                            </div>
                                            <span class="text-sm opacity-90">رسالة صوتية</span>
                                            @if($message->duration)
                                                <span class="text-xs opacity-75 font-mono">{{ gmdate('i:s', $message->duration) }}</span>
                                            @endif
                                        </div>
                                        <div class="bg-black bg-opacity-10 rounded-xl p-3">
                                            <div class="flex items-center gap-3">
                                                <button onclick="toggleAudioPlay(this,'{{ $message->file_url }}')" class="w-10 h-10 rounded-full {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-blue-500 hover:bg-blue-600' }} flex items-center justify-center transition-colors audio-play-btn">
                                                    <i class="fas fa-play text-white text-sm"></i>
                                                </button>
                                                <div class="flex-1">
                                                    <div class="h-8 flex items-center gap-1">
                                                        @for ($i = 0; $i < 20; $i++)
                                                            <div class="w-1 bg-current opacity-40 rounded-full waveform-bar"
                                                                 style="height: {{ rand(20,100) }}%; animation-delay: {{ $i * 0.1 }}s"></div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <span class="text-xs opacity-75 font-mono duration-display">
                                                    {{ $message->duration ? gmdate('i:s', $message->duration) : '0:00' }}
                                                </span>
                                            </div>
                                            <audio class="hidden voice-audio" preload="metadata">
                                                <source src="{{ $message->file_url }}" type="audio/webm">
                                            </audio>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <button onclick="downloadAudio('{{ $message->file_url }}', 'voice_{{ $message->id }}.webm')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-download ml-1"></i> تحميل</button>
                                            <button onclick="copyToClipboard('{{ $message->file_url }}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-share ml-1"></i> مشاركة</button>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center mt-2 text-xs opacity-75">
                                    <span>{{ $message->created_at->format('H:i') }}</span>
                                    @if($message->sender_type === 'admin')
                                        <i class="fas {{ $message->is_read ? 'fa-check-double text-blue-200' : 'fa-check' }}"></i>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 {{ $message->sender_type === 'admin' ? 'text-right' : 'text-left' }}">
                                {{ $message->sender_name }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="border-t bg-white p-4">
            <form id="messageForm" class="flex items-end gap-3">
                <input type="hidden" id="messageType" value="text">
                <input type="file" id="fileInput" class="hidden" accept="*/*">
                <button type="button" onclick="toggleFileInput()" class="flex-shrink-0 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors"><i class="fas fa-paperclip text-gray-600"></i></button>
                <button type="button" id="voiceBtn" onclick="toggleVoiceRecording()" class="flex-shrink-0 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all duration-200 voice-record-btn"><i class="fas fa-microphone text-gray-600"></i></button>
                <div class="flex-1 relative">
                    <input type="text" id="messageInput" placeholder="اكتب رسالتك..." class="w-full px-4 py-3 rounded-full border-gray-300 focus:border-blue-500 pr-12">
                    <button type="submit" class="absolute left-2 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors"><i class="fas fa-paper-plane text-white text-sm"></i></button>
                </div>
            </form>
            <div id="recordingStatus" class="hidden mt-3 p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-white rounded-full mr-3 recording-pulse"></div>
                        <div>
                            <p class="font-medium">جاري التسجيل...</p>
                            <p class="text-sm text-red-100">اضغط الزر مرة أخرى للإنهاء</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div id="recordingTime" class="text-lg font-mono">0:00</div>
                        <div class="text-xs text-red-100">مدة التسجيل</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Panel -->
    <div id="previewPanel" class="hidden w-80 border-l bg-white flex flex-col">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="font-medium text-gray-900">معاينة الملف</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div id="previewContent" class="flex-1 overflow-auto p-4">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>

<!-- Upload Progress Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-sm w-full">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">جاري رفع الملف...</h3>
            <p class="text-sm text-gray-500">يرجى الانتظار</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let chatId = {{ $chat->id }};
let lastMessageId = 0;
let refreshInterval = null;
let isPageVisible = true;

document.addEventListener('visibilitychange', function() {
    isPageVisible = !document.hidden;
    if (isPageVisible) loadNewMessages();
});
function startAutoRefresh() { refreshInterval = setInterval(loadNewMessages, 5000);}
function stopAutoRefresh() { if (refreshInterval) { clearInterval(refreshInterval); refreshInterval = null; } }
function loadNewMessages() {
    if (!isPageVisible) return;
    fetch(`/admin/customer-communication/${chatId}/messages`).then(response => response.json()).then(data => {
        if (data.messages && data.messages.length > 0) {
            const newMessages = data.messages.filter(msg => msg.id > lastMessageId);
            if (newMessages.length > 0) {
                newMessages.forEach(message => { addNewMessageToChat(message); lastMessageId = Math.max(lastMessageId, message.id); });
                scrollToBottom();
            }
        }
    });
}
function addNewMessageToChat(message) {
    const messagesList = document.getElementById('messagesList');
    if (document.querySelector(`[data-message-id="${message.id}"]`)) return;
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${message.sender_type === 'admin' ? 'justify-end' : 'justify-start'} message-appear`;
    messageDiv.setAttribute('data-message-id', message.id);
    messageDiv.innerHTML = generateMessageHTML(message);
    messagesList.appendChild(messageDiv);
}
function generateMessageHTML(message) {
    let contentHTML = '';
    if (message.message_type === 'text') {
        contentHTML = `<p class="break-words">${message.content}</p>`;
    } else if (message.message_type === 'file') {
        const extension = message.file_name.split('.').pop().toLowerCase();
        let iconHTML = '';
        if (['jpg','jpeg','png','gif','webp'].includes(extension)) iconHTML = '<div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center"><i class="fas fa-image text-white"></i></div>';
        else if (extension === 'pdf') iconHTML = '<div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center"><i class="fas fa-file-pdf text-white"></i></div>';
        else if (['doc','docx'].includes(extension)) iconHTML = '<div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center"><i class="fas fa-file-word text-white"></i></div>';
        else iconHTML = '<div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center"><i class="fas fa-file text-white"></i></div>';
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center gap-3"><div class="flex-shrink-0">${iconHTML}</div><div class="flex-1 min-w-0"><p class="font-medium truncate">${message.file_name}</p><p class="text-xs opacity-75">${message.file_size_formatted}</p></div></div>
                <div class="flex gap-2 text-xs">
                    <button onclick="previewFile('${message.file_url}', '${message.file_name}', '${extension}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-eye ml-1"></i> معاينة</button>
                    <button onclick="copyToClipboard('${message.file_url}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-copy ml-1"></i> نسخ الرابط</button>
                    <a href="${message.file_url}" target="_blank" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-download ml-1"></i> تحميل</a>
                </div>
            </div>
        `;
    } else if (message.message_type === 'voice') {
        const duration = message.duration ? new Date(message.duration * 1000).toISOString().substr(14,5) : '0:00';
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full ${message.sender_type === 'admin' ? 'bg-white bg-opacity-20' : 'bg-blue-500'} flex items-center justify-center"><i class="fas fa-microphone text-white text-sm"></i></div><span class="text-sm opacity-90">رسالة صوتية</span><span class="text-xs opacity-75 font-mono">${duration}</span></div>
                <div class="bg-black bg-opacity-10 rounded-xl p-3"><div class="flex items-center gap-3"><button onclick="toggleAudioPlay(this,'${message.file_url}')" class="w-10 h-10 rounded-full ${message.sender_type === 'admin' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-blue-500 hover:bg-blue-600'} flex items-center justify-center transition-colors audio-play-btn"><i class="fas fa-play text-white text-sm"></i></button><div class="flex-1"><div class="h-8 flex items-center gap-1">${Array.from({length:20},(_,i)=>`<div class="w-1 bg-current opacity-40 rounded-full waveform-bar" style="height:${Math.random()*80+20}%;animation-delay:${i*0.1}s"></div>`).join('')}</div></div><span class="text-xs opacity-75 font-mono duration-display">${duration}</span></div><audio class="hidden voice-audio" preload="metadata"><source src="${message.file_url}" type="audio/webm"></audio></div>
                <div class="flex gap-2 text-xs"><button onclick="downloadAudio('${message.file_url}','voice_${message.id}.webm')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-download ml-1"></i> تحميل</button><button onclick="copyToClipboard('${message.file_url}')" class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30"><i class="fas fa-share ml-1"></i> مشاركة</button></div>
            </div>
        `;
    }
    return `<div class="max-w-xs lg:max-w-md"><div class="rounded-2xl px-4 py-3 ${message.sender_type === 'admin' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 shadow-sm border'}">${contentHTML}<div class="flex justify-between items-center mt-2 text-xs opacity-75"><span>${message.created_at}</span>${message.sender_type === 'admin' ? `<i class="fas ${message.is_read ? 'fa-check-double text-blue-200' : 'fa-check'}"></i>` : ''}</div></div><p class="text-xs text-gray-500 mt-1 ${message.sender_type === 'admin' ? 'text-right' : 'text-left'}">${message.sender_name}</p></div>`;
}
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const messageType = document.getElementById('messageType').value;
    if (messageType === 'text') sendTextMessage();
    else if (messageType === 'file') sendFileMessage();
});
function sendTextMessage() {
    const content = document.getElementById('messageInput').value.trim();
    if (!content) return;
    const formData = new FormData();
    formData.append('message_type', 'text');
    formData.append('content', content);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch(`/admin/customer-communication/${chatId}/send-message`, { method: 'POST', body: formData }).then(response => response.json()).then(data => {
        if (data.success) {
            document.getElementById('messageInput').value = '';
            addNewMessageToChat(data.message);
            lastMessageId = Math.max(lastMessageId, data.message.id);
            scrollToBottom();
        }
    });
}
function sendFileMessage() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) {
        alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
        return;
    }
    showUploadProgress();
    const formData = new FormData();
    formData.append('message_type', 'file');
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch(`/admin/customer-communication/${chatId}/send-message`, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            hideUploadProgress();
            if (data.success) {
                fileInput.value = '';
                document.getElementById('messageType').value = 'text';
                addNewMessageToChat(data.message);
                lastMessageId = Math.max(lastMessageId, data.message.id);
                scrollToBottom();
            }
        })
        .catch(() => hideUploadProgress());
}
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
document.getElementById('fileInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
            this.value = '';
            document.getElementById('messageType').value = 'text';
            return;
        }
        sendFileMessage();
    }
});
// تسجيل صوتي:
let mediaRecorder; let audioChunks = []; let isRecording = false; let recordingTimer = null;
function toggleVoiceRecording() { if (!isRecording) startRecording(); else stopRecording(); }
function startRecording() {
    navigator.mediaDevices.getUserMedia({audio:true}).then(stream => {
        mediaRecorder = new MediaRecorder(stream); audioChunks = [];
        mediaRecorder.ondataavailable = event => {audioChunks.push(event.data);}
        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks,{type:'audio/webm'}); sendVoiceMessage(audioBlob);
            stream.getTracks().forEach(track => track.stop());
        }
        mediaRecorder.start(); isRecording = true;
        const voiceBtn = document.getElementById('voiceBtn');
        voiceBtn.classList.remove('bg-gray-100','hover:bg-gray-200');
        voiceBtn.classList.add('bg-red-500','hover:bg-red-600','recording-pulse');
        voiceBtn.innerHTML = '<i class="fas fa-stop text-white"></i>';
        document.getElementById('recordingStatus').classList.remove('hidden');
        let startTime = Date.now();
        recordingTimer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime)/1000);
            const min = Math.floor(elapsed/60); const sec = elapsed%60;
            document.getElementById('recordingTime').textContent = `${min}:${sec.toString().padStart(2,'0')}`;
        },1000);
    }).catch(()=>alert('لا يمكن الوصول للميكروفون'));
}
function stopRecording() {
    if(mediaRecorder && isRecording) {
        mediaRecorder.stop(); isRecording = false;
        document.getElementById('voiceBtn').classList.remove('bg-red-500','hover:bg-red-600','recording-pulse');
        document.getElementById('voiceBtn').classList.add('bg-gray-100','hover:bg-gray-200');
        document.getElementById('voiceBtn').innerHTML='<i class="fas fa-microphone text-gray-600"></i>';
        document.getElementById('recordingStatus').classList.add('hidden');
        if(recordingTimer) {clearInterval(recordingTimer); recordingTimer=null;}
    }
}
function sendVoiceMessage(audioBlob) {
    const reader = new FileReader();
    reader.onload = function(){
        const estimatedDuration = Math.round(audioBlob.size/16000);
        const formData = new FormData();
        formData.append('message_type','voice');
        formData.append('voice',reader.result);
        formData.append('duration',estimatedDuration);
        formData.append('_token',document.querySelector('meta[name="csrf-token"]').content);
        fetch(`/admin/customer-communication/${chatId}/send-message`,{method:'POST',body:formData})
            .then(response=>response.json())
            .then(data=>{
                if(data.success) {
                    addNewMessageToChat(data.message);
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                    scrollToBottom();
                }
            });
    };
    reader.readAsDataURL(audioBlob);
}
// معاينة الملف
function previewFile(url,filename,extension) {
    document.getElementById('previewPanel').classList.remove('hidden');
    const previewContent = document.getElementById('previewContent');
    const isImage = ['jpg','jpeg','png','gif','webp'].includes(extension.toLowerCase());
    const isPdf = extension.toLowerCase() === 'pdf';
    if(isImage) {
        previewContent.innerHTML = `<div class="space-y-4"><div class="text-center"><img src="${url}" alt="${filename}" class="max-w-full h-auto rounded-lg shadow-sm"></div><div class="bg-gray-50 p-3 rounded-lg"><p class="text-sm font-medium">${filename}</p></div></div>`;
    } else if(isPdf) {
        previewContent.innerHTML = `<div class="space-y-4"><iframe src="${url}" class="w-full h-96 border rounded-lg"></iframe><div class="bg-gray-50 p-3 rounded-lg"><p class="text-sm font-medium">${filename}</p></div></div>`;
    } else {
        previewContent.innerHTML = `<div class="text-center space-y-4"><div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto flex items-center justify-center"><i class="fas fa-file text-gray-400 text-2xl"></i></div><div><p class="font-medium">${filename}</p><p class="text-sm text-gray-500">لا يمكن معاينة هذا النوع من الملفات</p></div></div>`;
    }
}
// التحكم في الصوت
let currentPlayingAudio = null;
function toggleAudioPlay(button,audioUrl) {
    const audioElement = button.parentElement.parentElement.querySelector('.voice-audio');
    const playIcon = button.querySelector('i');
    const waveformBars = button.parentElement.querySelectorAll('.waveform-bar');
    const durationDisplay = button.parentElement.querySelector('.duration-display');
    if (currentPlayingAudio && currentPlayingAudio !== audioElement) {
        currentPlayingAudio.pause();
        currentPlayingAudio.currentTime = 0;
        resetAudioButton(currentPlayingAudio);
    }
    if(audioElement.paused) {
        audioElement.play(); currentPlayingAudio = audioElement;
        playIcon.classList.remove('fa-play'); playIcon.classList.add('fa-pause');
        waveformBars.forEach(bar=>bar.classList.add('playing'));
        audioElement.addEventListener('timeupdate',function() {
            if(!audioElement.paused) {
                const t = Math.floor(audioElement.currentTime);
                const m = Math.floor(t/60); const s = t%60;
                durationDisplay.textContent = `${m}:${s.toString().padStart(2,'0')}`;
            }
        });
        audioElement.addEventListener('ended',function(){resetAudioButton(audioElement);});
    } else {
        audioElement.pause(); resetAudioButton(audioElement);
    }
}
function resetAudioButton(audioElement) {
    const container = audioElement.parentElement.parentElement;
    const button = container.querySelector('.audio-play-btn');
    const playIcon = button.querySelector('i');
    const waveformBars = container.querySelectorAll('.waveform-bar');
    playIcon.classList.remove('fa-pause'); playIcon.classList.add('fa-play');
    waveformBars.forEach(bar=>bar.classList.remove('playing'));
    currentPlayingAudio = null;
}
function downloadAudio(url,filename) {
    const link = document.createElement('a');
    link.href = url; link.download = filename;
    document.body.appendChild(link); link.click(); document.body.removeChild(link);
}
function closePreview() { document.getElementById('previewPanel').classList.add('hidden');}
function showUploadProgress() { document.getElementById('uploadModal').classList.remove('hidden');}
function hideUploadProgress() { document.getElementById('uploadModal').classList.add('hidden');}
function clearChatFiles() {
    if(!confirm('هل تريد حذف كل الملفات في هذا الشات؟')) return;
    fetch(`/admin/customer-communication/${chatId}/clear-files`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
        .then(response=>response.json())
        .then(data=>{
            if(data.success) {
                alert('تم حذف الملفات وتوفير مساحة: '+data.freed_space);
                loadNewMessages();
            }
        });
}
function showChatInfo() {
    alert(`معلومات الشات:\nالعنوان: {{$chat->potentialCustomer->customer_name ?? '---'}}\nرقم العميل: {{$chat->potentialCustomer->phone ?? '---'}}\nالموظف: {{$chat->employee->name ?? '---'}}\nتاريخ الإنشاء: {{$chat->created_at->format('Y-m-d H:i')}}`);
}
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
    scrollToBottom();
});
window.addEventListener('beforeunload',function(){stopAutoRefresh();});
window.addEventListener('pagehide',function(){stopAutoRefresh();});
window.addEventListener('pageshow',function(event){if(event.persisted){startAutoRefresh();loadNewMessages();}});
</script>
@endpush
