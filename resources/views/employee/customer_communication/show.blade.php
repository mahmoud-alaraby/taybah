@extends('employee.layouts.app')

@section('title', 'التواصل بخصوص العميل: ' . $customer->customer_name)
@section('page-title', 'التواصل مع الإدارة')
@section('page-subtitle', 'بخصوص العميل: ' . $customer->customer_name)

@section('content')
<div class="flex h-screen max-h-[calc(100vh-200px)] bg-white shadow-xl rounded-2xl overflow-hidden">
    
    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        
        <!-- Chat Header -->
        <div class="flex items-center justify-between p-6 border-b bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Customer Avatar -->
                <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-lg font-bold">
                    {{ substr($customer->customer_name, 0, 1) }}
                </div>
                
                <div>
                    <h3 class="font-semibold text-lg">{{ $customer->customer_name }}</h3>
                    <div class="flex items-center space-x-3 space-x-reverse text-blue-100">
                        <span class="text-sm">{{ $customer->phone }}</span>
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
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-500 bg-opacity-80 text-white">
                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                عالي الأولوية
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <!-- Connection Status -->
                <div id="connectionStatus" class="flex items-center text-sm text-blue-100">
                    <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                    متصل
                </div>
                
                <!-- Chat Actions -->
                <div class="flex items-center space-x-2 space-x-reverse">
                    @if($chat->status !== 'completed')
                        <button onclick="markAsCompleted()" 
                                class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-check ml-1"></i>
                            تم التواصل
                        </button>
                    @endif
                    
                    <a href="{{ route('employee.customer-communication.index') }}" 
                       class="text-blue-100 hover:text-white p-2 transition-colors">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Info Bar -->
        <div class="p-4 bg-gray-50 border-b">
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
        <div id="messagesContainer" class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div id="messagesList" class="space-y-4">
                @if($messages->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-blue-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">ابدأ التواصل مع الإدارة</h3>
                        <p class="text-gray-500">اكتب رسالتك الأولى بخصوص هذا العميل</p>
                    </div>
                @else
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_type === 'employee' ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                            <div class="max-w-xs lg:max-w-md">
                                
                                <!-- Message Bubble -->
                                <div class="rounded-2xl px-4 py-3 {{ $message->sender_type === 'employee' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 shadow-sm border' }}">
                                    
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
                                                <div class="w-8 h-8 rounded-full {{ $message->sender_type === 'employee' ? 'bg-white bg-opacity-20' : 'bg-blue-500' }} flex items-center justify-center">
                                                    <i class="fas fa-microphone text-white text-sm"></i>
                                                </div>
                                                <span class="text-sm opacity-90">رسالة صوتية</span>
                                                @if($message->duration)
                                                    <span class="text-xs opacity-75">
                                                        {{ $message->duration_formatted }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Custom Audio Player -->
                                            <div class="bg-black bg-opacity-10 rounded-xl p-3">
                                                <div class="flex items-center space-x-3 space-x-reverse">
                                                    <!-- Play/Pause Button -->
                                                    <button onclick="toggleAudioPlay(this, '{{ $message->file_url }}')" 
                                                            class="w-10 h-10 rounded-full {{ $message->sender_type === 'employee' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-blue-500 hover:bg-blue-600' }} flex items-center justify-center transition-all transform hover:scale-105 active:scale-95 audio-play-btn">
                                                        <i class="fas fa-play text-white text-sm"></i>
                                                    </button>
                                                    
                                                    <!-- Waveform/Progress -->
                                                    <div class="flex-1">
                                                        <div class="h-8 flex items-center space-x-1 space-x-reverse">
                                                            @for($i = 0; $i < 20; $i++)
                                                                <div class="w-1 bg-current opacity-40 rounded-full transition-all duration-150 ease-in-out waveform-bar" 
                                                                     style="height: {{ rand(20, 100) }}%"></div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Duration -->
                                                    <span class="text-xs opacity-75 font-mono duration-display">
                                                        {{ $message->duration_formatted ?? '0:00' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Hidden Audio Element -->
                                                <audio class="hidden voice-audio" preload="metadata">
                                                    <source src="{{ $message->file_url }}" type="audio/webm">
                                                </audio>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Message Footer -->
                                    <div class="flex justify-between items-center mt-3 text-xs opacity-75">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <span>{{ $message->created_at->format('H:i') }}</span>
                                            @if($message->sender_type === 'employee' && $message->created_at->diffInMinutes(now()) <= 5)
                                                <button onclick="deleteMessage({{ $message->id }})" 
                                                        class="text-red-400 hover:text-red-300 transition-colors">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                        
                                        @if($message->sender_type === 'employee')
                                            <i class="fas {{ $message->is_read ? 'fa-check-double text-blue-200' : 'fa-check text-blue-300' }}"></i>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Sender Info -->
                                <div class="flex items-center mt-2 {{ $message->sender_type === 'employee' ? 'justify-end' : 'justify-start' }}">
                                    @if($message->sender_type === 'admin')
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                                                <i class="fas fa-user-tie text-white text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-500">الإدارة</span>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <span class="text-xs text-gray-500">أنت</span>
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                                <i class="fas fa-user text-white text-xs"></i>
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

        <!-- File Preview Area (Appears when file is selected) -->
        <div id="filePreview" class="hidden border-t bg-gray-50 p-4">
            <div class="transition-all duration-300 border-2 border-dashed border-gray-300 rounded-lg p-4" id="filePreviewBox">
                <div class="flex items-center gap-3" id="fileInfoContainer">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file text-blue-500 text-xl" id="fileIcon"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900" id="fileName"></p>
                        <p class="text-sm text-gray-500" id="fileSize"></p>
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-200 rounded-full mt-3 overflow-hidden hidden" id="uploadProgress">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300 w-0" id="uploadProgressBar"></div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button onclick="sendSelectedFile()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm transition-colors">
                        <i class="fas fa-paper-plane ml-1"></i> إرسال الملف
                    </button>
                    <button onclick="cancelFileSelection()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-sm transition-colors">
                        <i class="fas fa-times ml-1"></i> إلغاء
                    </button>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t bg-white p-6">
            <form id="messageForm" class="flex items-end space-x-4 space-x-reverse">
                <input type="hidden" id="messageType" value="text">
                
                <!-- File Input -->
                <input type="file" id="fileInput" class="hidden" accept="*/*">
                
                <!-- Voice Button -->
                <button type="button" id="voiceBtn" onclick="toggleVoiceRecording()" 
                        class="flex-shrink-0 w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all duration-200">
                    <i class="fas fa-microphone text-gray-600"></i>
                </button>
                
                <!-- Attachment Button -->
                <button type="button" onclick="toggleFileInput()" 
                        class="flex-shrink-0 w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-paperclip text-gray-600"></i>
                </button>
                
                <!-- Text Input -->
                <div class="flex-1 relative">
                    <input type="text" id="messageInput" placeholder="اكتب رسالتك للإدارة بخصوص هذا العميل..." 
                           class="w-full px-6 py-4 rounded-2xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 pr-16 text-lg">
                    
                    <!-- Send Button -->
                    <button type="submit" 
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-full flex items-center justify-center transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-paper-plane text-white"></i>
                    </button>
                </div>
            </form>
            
            <!-- Recording Status -->
            <div id="recordingStatus" class="hidden mt-4 p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-white rounded-full mr-3 animate-pulse"></div>
                        <div>
                            <p class="font-medium">جاري التسجيل...</p>
                            <p class="text-sm text-red-100">اضغط الزر مرة أخرى للإنهاء</p>
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

@endsection

@push('scripts')
<script>
let mediaRecorder;
let audioChunks = [];
let isRecording = false;
let chatId = {{ $chat->id }};
let recordingTimer = null;
let lastMessageId = 0;
let refreshInterval = null;
let isPageVisible = true;
let selectedFile = null;

// CSS للرسوم المتحركة
const style = document.createElement('style');
style.textContent = `
@keyframes wave {
    0% { transform: scaleY(0.3); opacity: 0.4; }
    100% { transform: scaleY(1); opacity: 0.8; }
}
.waveform-bar {
    animation: wave 1.5s ease-in-out infinite alternate;
}
.waveform-bar.playing {
    animation: wave 0.8s ease-in-out infinite alternate;
    opacity: 1;
}
`;
document.head.appendChild(style);

// تتبع رؤية الصفحة
document.addEventListener('visibilitychange', function() {
    isPageVisible = !document.hidden;
    if (isPageVisible) {
        loadNewMessages();
    }
});

// بدء تحديث الرسائل كل 5 ثواني
function startAutoRefresh() {
    refreshInterval = setInterval(loadNewMessages, 5000);
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
    
    fetch(`/employee/customer-communication/${chatId}/new-messages?last_message_id=${lastMessageId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            updateConnectionStatus('connected');
            
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    addNewMessageToChat(message);
                });
                lastMessageId = data.last_message_id;
                scrollToBottom();
                
                if (!isPageVisible) {
                    showNewMessageNotification();
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
            statusElement.innerHTML = '<span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>متصل';
            break;
        case 'loading':
            statusElement.innerHTML = '<span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>جاري التحديث...';
            break;
        case 'error':
            statusElement.innerHTML = '<span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>خطأ في الاتصال';
            break;
    }
}

// إرسال الرسالة
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageType = document.getElementById('messageType').value;
    
    if (messageType === 'text') {
        sendTextMessage();
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
    
    fetch(`/employee/customer-communication/${chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            addNewMessageToChat(data.message);
            lastMessageId = Math.max(lastMessageId, data.message.id);
            scrollToBottom();
        } else {
            alert('حدث خطأ في إرسال الرسالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في إرسال الرسالة');
    });
}

// تبديل إدخال الملف
function toggleFileInput() {
    const fileInput = document.getElementById('fileInput');
    fileInput.click();
}

// التعامل مع اختيار الملف
document.getElementById('fileInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
            this.value = '';
            return;
        }
        selectedFile = file;
        showFilePreview(file);
    }
});

// عرض معاينة الملف
function showFilePreview(file) {
    const previewArea = document.getElementById('filePreview');
    const fileIcon = document.getElementById('fileIcon');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const previewBox = document.getElementById('filePreviewBox');
    
    // تحديد أيقونة الملف
    const extension = file.name.split('.').pop().toLowerCase();
    let iconClass = 'fa-file';
    let bgColor = 'bg-gray-100';
    let iconColor = 'text-gray-500';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        iconClass = 'fa-image';
        bgColor = 'bg-green-100';
        iconColor = 'text-green-500';
    } else if (extension === 'pdf') {
        iconClass = 'fa-file-pdf';
        bgColor = 'bg-red-100';
        iconColor = 'text-red-500';
    } else if (['doc', 'docx'].includes(extension)) {
        iconClass = 'fa-file-word';
        bgColor = 'bg-blue-100';
        iconColor = 'text-blue-500';
    }
    
    fileIcon.className = `fas ${iconClass} ${iconColor} text-xl`;
    fileIcon.parentElement.className = `w-12 h-12 ${bgColor} rounded-lg flex items-center justify-center`;
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    previewBox.classList.remove('border-gray-300');
    previewBox.classList.add('border-blue-300', 'bg-blue-50');
    previewArea.classList.remove('hidden');
}

// إرسال الملف المحدد
function sendSelectedFile() {
    if (!selectedFile) return;
    
    const previewBox = document.getElementById('filePreviewBox');
    const progressBar = document.getElementById('uploadProgressBar');
    const progress = document.getElementById('uploadProgress');
    
    previewBox.classList.remove('border-blue-300', 'bg-blue-50');
    previewBox.classList.add('border-gray-300');
    progress.classList.remove('hidden');
    
    const formData = new FormData();
    formData.append('message_type', 'file');
    formData.append('file', selectedFile);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    // محاكاة التقدم
    let progressValue = 0;
    const progressInterval = setInterval(() => {
        progressValue += Math.random() * 30;
        if (progressValue > 90) progressValue = 90;
        progressBar.style.width = progressValue + '%';
    }, 200);
    
    fetch(`/employee/customer-communication/${chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressBar.classList.remove('from-blue-500', 'to-blue-600');
        progressBar.classList.add('from-green-500', 'to-green-600');
        
        setTimeout(() => {
            if (data.success) {
                cancelFileSelection();
                addNewMessageToChat(data.message);
                lastMessageId = Math.max(lastMessageId, data.message.id);
                scrollToBottom();
            } else {
                alert('فشل في إرسال الملف');
                cancelFileSelection();
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        console.error('Error:', error);
        alert('حدث خطأ في إرسال الملف');
        cancelFileSelection();
    });
}

// إلغاء تحديد الملف
function cancelFileSelection() {
    const previewArea = document.getElementById('filePreview');
    const previewBox = document.getElementById('filePreviewBox');
    const progress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('uploadProgressBar');
    
    selectedFile = null;
    document.getElementById('fileInput').value = '';
    previewArea.classList.add('hidden');
    previewBox.classList.remove('border-blue-300', 'bg-blue-50');
    previewBox.classList.add('border-gray-300');
    progress.classList.add('hidden');
    progressBar.style.width = '0%';
    progressBar.classList.remove('from-green-500', 'to-green-600');
    progressBar.classList.add('from-blue-500', 'to-blue-600');
}

// تنسيق حجم الملف
function formatFileSize(bytes) {
    if (bytes === 0) return '0 KB';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

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
            
            document.getElementById('voiceBtn').classList.remove('bg-gray-100', 'hover:bg-gray-200');
            document.getElementById('voiceBtn').classList.add('bg-red-500', 'hover:bg-red-600', 'animate-pulse');
            document.getElementById('voiceBtn').innerHTML = '<i class="fas fa-stop text-white"></i>';
            
            document.getElementById('recordingStatus').classList.remove('hidden');
            
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
            alert('لا يمكن الوصول للميكروفون');
        });
}

function stopRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        
        const voiceBtn = document.getElementById('voiceBtn');
        voiceBtn.classList.remove('bg-red-500', 'hover:bg-red-600', 'animate-pulse');
        voiceBtn.classList.add('bg-gray-100', 'hover:bg-gray-200');
        voiceBtn.innerHTML = '<i class="fas fa-microphone text-gray-600"></i>';
        
        document.getElementById('recordingStatus').classList.add('hidden');
        
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
    }
}

function sendVoiceMessage(audioBlob) {
    const reader = new FileReader();
    reader.onload = function() {
        const estimatedDuration = Math.round(audioBlob.size / 16000);
        
        const formData = new FormData();
        formData.append('message_type', 'voice');
        formData.append('voice', reader.result);
        formData.append('duration', estimatedDuration);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(`/employee/customer-communication/${chatId}/send`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addNewMessageToChat(data.message);
                lastMessageId = Math.max(lastMessageId, data.message.id);
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في إرسال التسجيل الصوتي');
        });
    };
    reader.readAsDataURL(audioBlob);
}

// إضافة رسالة جديدة للشات
function addNewMessageToChat(message) {
    const messagesList = document.getElementById('messagesList');
    
    if (document.querySelector(`[data-message-id="${message.id}"]`)) {
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `flex ${message.sender_type === 'employee' ? 'justify-end' : 'justify-start'} opacity-0 transform translate-y-2 transition-all duration-300`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    if (message.sender_type !== 'employee') {
        setTimeout(() => {
            messageDiv.classList.add('animate-pulse');
            setTimeout(() => messageDiv.classList.remove('animate-pulse'), 2000);
        }, 100);
    }
    
    messageDiv.innerHTML = generateMessageHTML(message);
    messagesList.appendChild(messageDiv);
    
    // تأثير الظهور
    setTimeout(() => {
        messageDiv.classList.remove('opacity-0', 'translate-y-2');
    }, 50);
}

function generateMessageHTML(message) {
    let contentHTML = '';
    
    if (message.message_type === 'text') {
        contentHTML = `<p class="break-words leading-relaxed">${message.content}</p>`;
    } else if (message.message_type === 'file') {
        const extension = message.file_name.split('.').pop().toLowerCase();
        let iconHTML = '';
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            iconHTML = '<div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center"><i class="fas fa-image text-white"></i></div>';
        } else if (extension === 'pdf') {
            iconHTML = '<div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center"><i class="fas fa-file-pdf text-white"></i></div>';
        } else if (['doc', 'docx'].includes(extension)) {
            iconHTML = '<div class="w-10 h-10 bg-blue-400 rounded-lg flex items-center justify-center"><i class="fas fa-file-word text-white"></i></div>';
        } else {
            iconHTML = '<div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center"><i class="fas fa-file text-white"></i></div>';
        }
        
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="flex-shrink-0">${iconHTML}</div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">${message.file_name}</p>
                        <p class="text-xs opacity-75">${message.file_size_formatted}</p>
                    </div>
                </div>
                <div class="flex space-x-2 space-x-reverse text-xs">
                    <button onclick="previewFile('${message.file_url}', '${message.file_name}', '${extension}')" 
                            class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-eye ml-1"></i> معاينة
                    </button>
                    <button onclick="copyToClipboard('${message.file_url}')" 
                            class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-copy ml-1"></i> نسخ الرابط
                    </button>
                    <a href="${message.file_url}" target="_blank" 
                       class="bg-black bg-opacity-20 px-3 py-1.5 rounded-lg hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-download ml-1"></i> تحميل
                    </a>
                </div>
            </div>
        `;
    } else if (message.message_type === 'voice') {
        const duration = message.duration_formatted || '0:00';
        contentHTML = `
            <div class="space-y-3">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <div class="w-8 h-8 rounded-full ${message.sender_type === 'employee' ? 'bg-white bg-opacity-20' : 'bg-blue-500'} flex items-center justify-center">
                        <i class="fas fa-microphone text-white text-sm"></i>
                    </div>
                    <span class="text-sm opacity-90">رسالة صوتية</span>
                    <span class="text-xs opacity-75">${duration}</span>
                </div>
                <div class="bg-black bg-opacity-10 rounded-xl p-3">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <button onclick="toggleAudioPlay(this, '${message.file_url}')" 
                                class="w-10 h-10 rounded-full ${message.sender_type === 'employee' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : 'bg-blue-500 hover:bg-blue-600'} flex items-center justify-center transition-all transform hover:scale-105 active:scale-95 audio-play-btn">
                            <i class="fas fa-play text-white text-sm"></i>
                        </button>
                        <div class="flex-1">
                            <div class="h-8 flex items-center space-x-1 space-x-reverse">
                                ${Array.from({length: 20}, (_, i) => 
                                    `<div class="w-1 bg-current opacity-40 rounded-full transition-all duration-150 ease-in-out waveform-bar" 
                                         style="height: ${Math.random() * 80 + 20}%; animation-delay: ${i * 0.1}s"></div>`
                                ).join('')}
                            </div>
                        </div>
                        <span class="text-xs opacity-75 font-mono duration-display">${duration}</span>
                    </div>
                    <audio class="hidden voice-audio" preload="metadata">
                        <source src="${message.file_url}" type="audio/webm">
                    </audio>
                </div>
            </div>
        `;
    }
    
    const canDelete = message.sender_type === 'employee' && message.can_delete;
    
    return `
        <div class="max-w-xs lg:max-w-md">
            <div class="rounded-2xl px-4 py-3 ${message.sender_type === 'employee' ? 'bg-blue-500 text-white' : 'bg-white text-gray-800 shadow-sm border'}">
                ${contentHTML}
                <div class="flex justify-between items-center mt-3 text-xs opacity-75">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span>${message.created_at}</span>
                        ${canDelete ? 
                            `<button onclick="deleteMessage(${message.id})" 
                                     class="text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-trash text-xs"></i>
                             </button>` : ''
                        }
                    </div>
                    ${message.sender_type === 'employee' ? 
                        `<i class="fas ${message.is_read ? 'fa-check-double text-blue-200' : 'fa-check text-blue-300'}"></i>` : ''
                    }
                </div>
            </div>
            <div class="flex items-center mt-2 ${message.sender_type === 'employee' ? 'justify-end' : 'justify-start'}">
                ${message.sender_type === 'admin' ? 
                    `<div class="flex items-center space-x-2 space-x-reverse">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center">
                            <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <span class="text-xs text-gray-500">الإدارة</span>
                     </div>` : 
                    `<div class="flex items-center space-x-2 space-x-reverse">
                        <span class="text-xs text-gray-500">أنت</span>
                        <div class="w-6 h-6 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                     </div>`
                }
            </div>
        </div>
    `;
}

// حذف الرسالة
function deleteMessage(messageId) {
    if (!confirm('هل أنت متأكد من حذف هذه الرسالة؟')) return;
    
    fetch(`/employee/customer-communication/message/${messageId}`, {
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
                messageElement.classList.add('opacity-0', 'scale-90', 'transition-all', 'duration-300');
                setTimeout(() => messageElement.remove(), 300);
            }
        } else {
            alert(data.message || 'حدث خطأ في حذف الرسالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في حذف الرسالة');
    });
}

// التحكم في تشغيل الصوت
let currentPlayingAudio = null;

function toggleAudioPlay(button, audioUrl) {
    const audioElement = button.parentElement.parentElement.querySelector('.voice-audio');
    const playIcon = button.querySelector('i');
    const waveformBars = button.parentElement.querySelectorAll('.waveform-bar');
    const durationDisplay = button.parentElement.querySelector('.duration-display');
    
    if (currentPlayingAudio && currentPlayingAudio !== audioElement) {
        currentPlayingAudio.pause();
        currentPlayingAudio.currentTime = 0;
        resetAudioButton(currentPlayingAudio);
    }
    
    if (audioElement.paused) {
        audioElement.play();
        currentPlayingAudio = audioElement;
        
        playIcon.classList.remove('fa-play');
        playIcon.classList.add('fa-pause');
        
        waveformBars.forEach(bar => bar.classList.add('playing'));
        
        audioElement.addEventListener('timeupdate', function() {
            if (!audioElement.paused) {
                const currentTime = Math.floor(audioElement.currentTime);
                const minutes = Math.floor(currentTime / 60);
                const seconds = currentTime % 60;
                durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        });
        
        audioElement.addEventListener('ended', function() {
            resetAudioButton(audioElement);
        });
        
    } else {
        audioElement.pause();
        resetAudioButton(audioElement);
    }
}

function resetAudioButton(audioElement) {
    const container = audioElement.parentElement.parentElement;
    const button = container.querySelector('.audio-play-btn');
    const playIcon = button.querySelector('i');
    const waveformBars = container.querySelectorAll('.waveform-bar');
    
    playIcon.classList.remove('fa-pause');
    playIcon.classList.add('fa-play');
    
    waveformBars.forEach(bar => bar.classList.remove('playing'));
    
    currentPlayingAudio = null;
}

// تحديد كتم التواصل
function markAsCompleted() {
    if (!confirm('هل أنت متأكد من أنه تم التواصل مع العميل؟')) return;
    
    fetch(`/employee/customer-communication/${chatId}/completed`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// معاينة الملف
function previewFile(url, filename, extension) {
    window.open(url, '_blank');
}

// نسخ الرابط
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300';
        notification.textContent = 'تم نسخ الرابط!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }).catch(() => {
        alert('فشل في نسخ الرابط');
    });
}

// التمرير لأسفل
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

// تحديد آخر معرف رسالة
function initializeLastMessageId() {
    const messages = document.querySelectorAll('[data-message-id]');
    if (messages.length > 0) {
        const ids = Array.from(messages).map(msg => parseInt(msg.getAttribute('data-message-id')));
        lastMessageId = Math.max(...ids);
    }
}

// إظهار إشعار الرسالة الجديدة
function showNewMessageNotification() {
    if ("Notification" in window && Notification.permission === "granted") {
        new Notification("رسالة جديدة من الإدارة", {
            body: "وصلت رسالة جديدة بخصوص العميل",
            icon: "/favicon.ico"
        });
    }
}

// طلب إذن الإشعارات
function requestNotificationPermission() {
    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission();
    }
}

// عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    initializeLastMessageId();
    requestNotificationPermission();
    startAutoRefresh();
    scrollToBottom();
    
    document.getElementById('messageInput').focus();
});

// إيقاف التحديث عند مغادرة الصفحة
window.addEventListener('beforeunload', function() {
    if (isRecording) {
        stopRecording();
    }
    stopAutoRefresh();
});

window.addEventListener('pagehide', function() {
    stopAutoRefresh();
});

window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        startAutoRefresh();
        loadNewMessages();
    }
});

</script>
@endpush