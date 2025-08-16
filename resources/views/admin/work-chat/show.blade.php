@extends('admin.layouts.app')

@section('title', $workChat->title)
@section('page-title', $workChat->title)
@section('page-subtitle', 'شات مع ' . ($workChat->employee->name ?? 'موظف محذوف'))

@section('content')
<style>
/* Voice Message Animations */
.waveform-bar {
    animation: wave 1.5s ease-in-out infinite alternate;
}

@keyframes wave {
    0% { transform: scaleY(0.3); opacity: 0.4; }
    100% { transform: scaleY(1); opacity: 0.8; }
}

.waveform-bar.playing {
    animation: wave 0.8s ease-in-out infinite alternate;
    opacity: 1;
}

/* Custom Audio Player */
.audio-play-btn:hover {
    transform: scale(1.05);
}

.audio-play-btn:active {
    transform: scale(0.95);
}

/* Recording Animation */
.recording-pulse {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}

/* Message appear animation */
.message-appear {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="flex h-[calc(100vh-200px)] bg-white shadow rounded-lg overflow-hidden">
    
    <!-- Chat Messages Area -->
    <div class="flex-1 flex flex-col">
        
        <!-- Chat Header -->
        <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="h-10 w-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                    <i class="fas {{ $workChat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} text-white"></i>
                </div>
                <div>
                    <h3 class="font-medium">{{ $workChat->employee->name ?? 'موظف محذوف' }}</h3>
                    <p class="text-sm text-blue-100">{{ $workChat->type === 'design' ? 'مصمم' : 'مونتير' }} • متصل</p>
                </div>
            </div>
            
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="clearChatFiles()" class="text-white hover:text-blue-200 p-2" title="مسح الملفات">
                    <i class="fas fa-broom"></i>
                </button>
                <button onclick="showChatInfo()" class="text-white hover:text-blue-200 p-2" title="معلومات الشات">
                    <i class="fas fa-info-circle"></i>
                </button>
                <a href="{{ route('admin.work-chat.index', ['type' => $workChat->type]) }}" 
                   class="text-white hover:text-blue-200 p-2">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-gray-50">
            <div id="messagesList" class="space-y-4">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            
                            <!-- Message Bubble -->
                            <div class="rounded-2xl px-4 py-3 {{ $message->sender_type === 'admin' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 shadow-sm border' }}">
                                
                                @if($message->message_type === 'text')
                                    <p class="break-words">{{ $message->content }}</p>
                                    
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
                                            <button onclick="previewFile('{{ $message->file_url }}', '{{ $message->file_name }}', '{{ $extension }}')" 
                                                    class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                <i class="fas fa-eye ml-1"></i> معاينة
                                            </button>
                                            <button onclick="copyToClipboard('{{ $message->file_url }}')" 
                                                    class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                <i class="fas fa-copy ml-1"></i> نسخ الرابط
                                            </button>
                                            <a href="{{ $message->file_url }}" target="_blank" 
                                               class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                <i class="fas fa-download ml-1"></i> تحميل
                                            </a>
                                        </div>
                                    </div>
                                    
                                @elseif($message->message_type === 'voice')
                                    <div class="space-y-3">
                                        <!-- Voice Message Header -->
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <div class="w-8 h-8 rounded-full {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20' : 'bg-blue-500' }} flex items-center justify-center">
                                                <i class="fas fa-microphone {{ $message->sender_type === 'admin' ? 'text-white' : 'text-white' }} text-sm"></i>
                                            </div>
                                            <span class="text-sm opacity-90">رسالة صوتية</span>
                                            @if($message->duration)
                                                <span class="text-xs opacity-75">
                                                    @php
                                                        $minutes = floor($message->duration / 60);
                                                        $seconds = $message->duration % 60;
                                                    @endphp
                                                    {{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Custom Audio Player -->
                                        <div class="bg-black bg-opacity-10 rounded-xl p-3">
                                            <div class="flex items-center space-x-3 space-x-reverse">
                                                <!-- Play/Pause Button -->
                                                <button onclick="toggleAudioPlay(this, '{{ $message->file_url }}')" 
                                                        class="w-10 h-10 rounded-full {{ $message->sender_type === 'admin' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-blue-500 hover:bg-blue-600' }} flex items-center justify-center transition-colors audio-play-btn">
                                                    <i class="fas fa-play text-white text-sm"></i>
                                                </button>
                                                
                                                <!-- Waveform/Progress -->
                                                <div class="flex-1">
                                                    <div class="h-8 flex items-center space-x-1 space-x-reverse">
                                                        <!-- Fake waveform bars -->
                                                        @for($i = 0; $i < 20; $i++)
                                                            <div class="w-1 bg-current opacity-40 rounded-full waveform-bar" 
                                                                 style="height: {{ rand(20, 100) }}%; animation-delay: {{ $i * 0.1 }}s"></div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                
                                                <!-- Duration -->
                                                <span class="text-xs opacity-75 font-mono duration-display">
                                                    @if($message->duration)
                                                        @php
                                                            $minutes = floor($message->duration / 60);
                                                            $seconds = $message->duration % 60;
                                                        @endphp
                                                        {{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}
                                                    @else
                                                        0:00
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <!-- Hidden Audio Element -->
                                            <audio class="hidden voice-audio" preload="metadata">
                                                <source src="{{ $message->file_url }}" type="audio/webm">
                                            </audio>
                                        </div>
                                        
                                        <!-- Voice Actions -->
                                        <div class="flex space-x-2 space-x-reverse text-xs">
                                            <button onclick="downloadAudio('{{ $message->file_url }}', 'voice_{{ $message->id }}.webm')" 
                                                    class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                <i class="fas fa-download ml-1"></i> تحميل
                                            </button>
                                            <button onclick="copyToClipboard('{{ $message->file_url }}')" 
                                                    class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                                                <i class="fas fa-share ml-1"></i> مشاركة
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Message Time -->
                                <div class="flex justify-between items-center mt-2 text-xs opacity-75">
                                    <span>{{ $message->created_at->format('H:i') }}</span>
                                    @if($message->sender_type === 'admin')
                                        <i class="fas {{ $message->is_read ? 'fa-check-double text-blue-200' : 'fa-check' }}"></i>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Sender Name -->
                            <p class="text-xs text-gray-500 mt-1 {{ $message->sender_type === 'admin' ? 'text-right' : 'text-left' }}">
                                {{ $message->sender_name }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t bg-white p-4">
            <form id="messageForm" class="flex items-end space-x-3 space-x-reverse">
                <input type="hidden" id="messageType" value="text">
                
                <!-- File Input -->
                <input type="file" id="fileInput" class="hidden" accept="*/*">
                
                <!-- Attachment Button -->
                <button type="button" onclick="toggleFileInput()" 
                        class="flex-shrink-0 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-paperclip text-gray-600"></i>
                </button>
                
                <!-- Voice Button -->
                <button type="button" id="voiceBtn" onclick="toggleVoiceRecording()" 
                        class="flex-shrink-0 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all duration-200 voice-record-btn">
                    <i class="fas fa-microphone text-gray-600"></i>
                </button>
                
                <!-- Text Input -->
                <div class="flex-1 relative">
                    <input type="text" id="messageInput" placeholder="اكتب رسالتك..." 
                           class="w-full px-4 py-3 rounded-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-12">
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            class="absolute left-2 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
                        <i class="fas fa-paper-plane text-white text-sm"></i>
                    </button>
                </div>
            </form>
            
            <!-- Recording Status -->
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
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
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
let mediaRecorder;
let audioChunks = [];
let isRecording = false;
let chatId = {{ $workChat->id }};
let recordingTimer = null;

// تحديث الرسائل كل 5 ثواني
setInterval(loadMessages, 5000);

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
    
    fetch(`/admin/work-chat/${chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            addMessageToChat(data.message);
        }
    });
}

// إرسال ملف
function sendFileMessage() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    // إظهار مؤشر التحميل
    showUploadProgress();
    
    const formData = new FormData();
    formData.append('message_type', 'file');
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/admin/work-chat/${chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideUploadProgress();
        if (data.success) {
            fileInput.value = '';
            document.getElementById('messageType').value = 'text';
            addMessageToChat(data.message);
            
            // إظهار رابط الملف للنسخ
            showFileLink(data.message.file_url, data.message.file_name);
        }
    })
    .catch(error => {
        hideUploadProgress();
        console.error('Error:', error);
        Swal.fire('خطأ!', 'فشل في رفع الملف', 'error');
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
            Swal.fire('خطأ!', 'حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت', 'error');
            this.value = '';
            document.getElementById('messageType').value = 'text';
            return;
        }
        sendFileMessage();
    }
});

// تسجيل صوتي
function toggleVoiceRecording() {
    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }
}

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                sendVoiceMessage(audioBlob);
                stream.getTracks().forEach(track => track.stop());
            };
            
            mediaRecorder.start();
            isRecording = true;
            
            // تحديث شكل الزر
            const voiceBtn = document.getElementById('voiceBtn');
            voiceBtn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
            voiceBtn.classList.add('bg-red-500', 'hover:bg-red-600', 'recording-pulse');
            voiceBtn.innerHTML = '<i class="fas fa-stop text-white"></i>';
            
            // إظهار حالة التسجيل
            document.getElementById('recordingStatus').classList.remove('hidden');
            
            // تحديث عداد الوقت
            let startTime = Date.now();
            recordingTimer = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                document.getElementById('recordingTime').textContent = 
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        })
        .catch(error => {
            console.error('Error accessing microphone:', error);
            Swal.fire('خطأ!', 'لا يمكن الوصول للميكروفون', 'error');
        });
}

function stopRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        
        // إعادة تعيين شكل الزر
        const voiceBtn = document.getElementById('voiceBtn');
        voiceBtn.classList.remove('bg-red-500', 'hover:bg-red-600', 'recording-pulse');
        voiceBtn.classList.add('bg-gray-100', 'hover:bg-gray-200');
        voiceBtn.innerHTML = '<i class="fas fa-microphone text-gray-600"></i>';
        
        // إخفاء حالة التسجيل
        document.getElementById('recordingStatus').classList.add('hidden');
        
        // إيقاف عداد الوقت
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
    }
}

function sendVoiceMessage(audioBlob) {
    const reader = new FileReader();
    reader.onload = function() {
        const formData = new FormData();
        formData.append('message_type', 'voice');
        formData.append('voice', reader.result);
        formData.append('duration', Math.round(audioBlob.size / 1000));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(`/admin/work-chat/${chatId}/send`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessageToChat(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('خطأ!', 'حدث خطأ في إرسال التسجيل الصوتي', 'error');
        });
    };
    reader.readAsDataURL(audioBlob);
}

// معاينة الملف
function previewFile(url, filename, extension) {
    const previewPanel = document.getElementById('previewPanel');
    const previewContent = document.getElementById('previewContent');
    
    previewPanel.classList.remove('hidden');
    
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension.toLowerCase());
    const isPdf = extension.toLowerCase() === 'pdf';
    
    if (isImage) {
        previewContent.innerHTML = `
            <div class="space-y-4">
                <div class="text-center">
                    <img src="${url}" alt="${filename}" class="max-w-full h-auto rounded-lg shadow-sm">
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm font-medium">${filename}</p>
                    <div class="flex space-x-2 space-x-reverse mt-2">
                        <button onclick="copyToClipboard('${url}')" class="text-xs bg-blue-500 text-white px-2 py-1 rounded">
                            نسخ الرابط
                        </button>
                        <a href="${url}" target="_blank" class="text-xs bg-green-500 text-white px-2 py-1 rounded">
                            فتح في تبويب جديد
                        </a>
                    </div>
                </div>
            </div>
        `;
    } else if (isPdf) {
        previewContent.innerHTML = `
            <div class="space-y-4">
                <iframe src="${url}" class="w-full h-96 border rounded-lg"></iframe>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm font-medium">${filename}</p>
                    <div class="flex space-x-2 space-x-reverse mt-2">
                        <button onclick="copyToClipboard('${url}')" class="text-xs bg-blue-500 text-white px-2 py-1 rounded">
                            نسخ الرابط
                        </button>
                        <a href="${url}" target="_blank" class="text-xs bg-green-500 text-white px-2 py-1 rounded">
                            فتح في تبويب جديد
                        </a>
                    </div>
                </div>
            </div>
        `;
    } else {
        previewContent.innerHTML = `
            <div class="text-center space-y-4">
                <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto flex items-center justify-center">
                    <i class="fas fa-file text-gray-400 text-2xl"></i>
                </div>
                <div>
                    <p class="font-medium">${filename}</p>
                    <p class="text-sm text-gray-500">لا يمكن معاينة هذا النوع من الملفات</p>
                </div>
                <div class="flex space-x-2 space-x-reverse justify-center">
                    <button onclick="copyToClipboard('${url}')" class="bg-blue-500 text-white px-3 py-2 rounded text-sm">
                        نسخ الرابط
                    </button>
                    <a href="${url}" target="_blank" class="bg-green-500 text-white px-3 py-2 rounded text-sm">
                        تحميل الملف
                    </a>
                </div>
            </div>
        `;
    }
}

// التحكم في تشغيل الصوت
let currentPlayingAudio = null;

function toggleAudioPlay(button, audioUrl) {
    const audioElement = button.parentElement.parentElement.querySelector('.voice-audio');
    const playIcon = button.querySelector('i');
    const waveformBars = button.parentElement.querySelectorAll('.waveform-bar');
    const durationDisplay = button.parentElement.querySelector('.duration-display');
    
    // إيقاف أي صوت آخر يعمل
    if (currentPlayingAudio && currentPlayingAudio !== audioElement) {
        currentPlayingAudio.pause();
        currentPlayingAudio.currentTime = 0;
        resetAudioButton(currentPlayingAudio);
    }
    
    if (audioElement.paused) {
        // تشغيل الصوت
        audioElement.play();
        currentPlayingAudio = audioElement;
        
        // تغيير الأيقونة
        playIcon.classList.remove('fa-play');
        playIcon.classList.add('fa-pause');
        
        // تحريك الموجات
        waveformBars.forEach(bar => bar.classList.add('playing'));
        
        // تحديث الوقت
        audioElement.addEventListener('timeupdate', function() {
            if (!audioElement.paused) {
                const currentTime = Math.floor(audioElement.currentTime);
                const minutes = Math.floor(currentTime / 60);
                const seconds = currentTime % 60;
                durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        });
        
        // عند انتهاء التشغيل
        audioElement.addEventListener('ended', function() {
            resetAudioButton(audioElement);
        });
        
    } else {
        // إيقاف الصوت
        audioElement.pause();
        resetAudioButton(audioElement);
    }
}

function resetAudioButton(audioElement) {
    const container = audioElement.parentElement.parentElement;
    const button = container.querySelector('.audio-play-btn');
    const playIcon = button.querySelector('i');
    const waveformBars = container.querySelectorAll('.waveform-bar');
    
    // إعادة تعيين الأيقونة
    playIcon.classList.remove('fa-pause');
    playIcon.classList.add('fa-play');
    
    // إيقاف حركة الموجات
    waveformBars.forEach(bar => bar.classList.remove('playing'));
    
    currentPlayingAudio = null;
}

// تحميل الصوت
function downloadAudio(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function closePreview() {
    document.getElementById('previewPanel').classList.add('hidden');
}

// إضافة رسالة للشات
function addMessageToChat(message) {
    // يمكن تحسين هذه الدالة لاحقاً لإضافة الرسائل ديناميكياً
    location.reload(); // حل مؤقت
}

// تحميل الرسائل
function loadMessages() {
    fetch(`/admin/work-chat/${chatId}/messages`)
        .then(response => response.json())
        .then(data => {
            // تحديث عدد الرسائل إذا لزم الأمر
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

// مؤشرات التحميل
function showUploadProgress() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function hideUploadProgress() {
    document.getElementById('uploadModal').classList.add('hidden');
}

// إظهار رابط الملف
function showFileLink(fileUrl, fileName) {
    Swal.fire({
        title: 'تم رفع الملف بنجاح!',
        html: `
            <div class="text-center space-y-3">
                <div class="bg-green-50 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                    <p class="font-medium">${fileName}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">رابط المشاركة:</p>
                    <div class="flex items-center gap-2">
                        <input type="text" value="${fileUrl}" readonly 
                               class="flex-1 px-2 py-1 border rounded text-xs" id="fileUrlInput">
                        <button onclick="copyFileUrl()" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">
                            <i class="fas fa-copy"></i> نسخ
                        </button>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: 'موافق',
        timer: 5000,
        timerProgressBar: true
    });
}

function copyFileUrl() {
    const input = document.getElementById('fileUrlInput');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'تم نسخ الرابط!',
            showConfirmButton: false,
            timer: 2000
        });
    });
}

// نسخ الرابط
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'تم نسخ الرابط!',
            showConfirmButton: false,
            timer: 2000
        });
    });
}

// مسح ملفات الشات
function clearChatFiles() {
    Swal.fire({
        title: 'مسح الملفات',
        text: 'هل تريد حذف جميع الملفات في هذا الشات؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc143c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، امسح',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/work-chat/${chatId}/clear-files`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('تم!', `تم حذف ${data.deleted_count} ملف وتوفير ${data.freed_space}`, 'success');
                    location.reload();
                }
            });
        }
    });
}

// معلومات الشات
function showChatInfo() {
    Swal.fire({
        title: 'معلومات الشات',
        html: `
            <div class="text-right space-y-2">
                <p><strong>العنوان:</strong> {{ $workChat->title }}</p>
                <p><strong>النوع:</strong> {{ $workChat->type === 'design' ? 'تصميم' : 'مونتاج' }}</p>
                <p><strong>الموظف:</strong> {{ $workChat->employee->name ?? 'غير محدد' }}</p>
                <p><strong>تاريخ الإنشاء:</strong> {{ $workChat->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>الحالة:</strong> {{ $workChat->status === 'active' ? 'نشط' : $workChat->status }}</p>
            </div>
        `,
        confirmButtonText: 'موافق'
    });
}

// التمرير لأسفل
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

// تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// إيقاف التسجيل عند إغلاق الصفحة
window.addEventListener('beforeunload', function() {
    if (isRecording) {
        stopRecording();
    }
});
</script>
@endpush