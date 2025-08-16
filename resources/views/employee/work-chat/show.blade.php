@extends('employee.layouts.app')

@section('title', $workChat->title)
@section('page-title', $workChat->title)
@section('page-subtitle', 'شات مع ' . ($workChat->admin->name ?? 'الإدارة'))

@section('content')
<div class="bg-white shadow rounded-lg h-96">
    <!-- Chat Header -->
    <div class="flex items-center justify-between p-4 border-b">
        <div class="flex items-center space-x-3 space-x-reverse">
            <div class="h-10 w-10 rounded-full bg-{{ $workChat->type === 'design' ? 'blue' : 'purple' }}-500 flex items-center justify-center">
                <i class="fas {{ $workChat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} text-white"></i>
            </div>
            <div>
                <h3 class="font-medium">{{ $workChat->admin->name ?? 'الإدارة' }}</h3>
                <p class="text-sm text-gray-500">{{ $workChat->type === 'design' ? 'مشروع تصميم' : 'مشروع مونتاج' }}</p>
            </div>
        </div>
        
        <div class="flex space-x-2 space-x-reverse">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $workChat->status === 'active' ? 'bg-green-100 text-green-800' : 
                   ($workChat->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                {{ $workChat->status === 'active' ? 'نشط' : ($workChat->status === 'completed' ? 'مكتمل' : 'مؤرشف') }}
            </span>
            <a href="{{ route('employee.' . ($workChat->type === 'design' ? 'design-follow-up' : 'montage-follow-up')) }}" 
               class="text-gray-600 hover:text-gray-800 p-2">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Messages Container -->
    <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 h-80" style="max-height: 320px;">
        <div id="messagesList">
            @foreach($messages as $message)
                <div class="message mb-3 {{ $message->sender_type === 'employee' ? 'text-left' : 'text-right' }}">
                    <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_type === 'employee' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-800' }}">
                        @if($message->message_type === 'text')
                            <p>{{ $message->content }}</p>
                        @elseif($message->message_type === 'file')
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <i class="fas fa-file"></i>
                                    <div class="flex-1">
                                        <a href="{{ $message->file_url }}" target="_blank" class="underline hover:text-blue-200">
                                            {{ $message->file_name }}
                                        </a>
                                        <p class="text-xs opacity-75">{{ $message->file_size_formatted }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2 space-x-reverse text-xs">
                                    <button onclick="copyToClipboard('{{ $message->file_url }}')" 
                                            class="bg-black bg-opacity-20 px-2 py-1 rounded hover:bg-opacity-30">
                                        <i class="fas fa-copy ml-1"></i> نسخ الرابط
                                    </button>
                                    <a href="{{ $message->file_url }}" target="_blank" 
                                       class="bg-black bg-opacity-20 px-2 py-1 rounded hover:bg-opacity-30">
                                        <i class="fas fa-external-link-alt ml-1"></i> فتح
                                    </a>
                                </div>
                            </div>
                        @elseif($message->message_type === 'voice')
                            <audio controls class="max-w-full">
                                <source src="{{ $message->file_url }}" type="audio/webm">
                                متصفحك لا يدعم تشغيل الصوت
                            </audio>
                        @endif
                        <p class="text-xs opacity-75 mt-1">{{ $message->created_at->format('H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Message Input -->
    <div class="border-t p-4">
        <form id="messageForm" class="flex space-x-2 space-x-reverse">
            <input type="hidden" id="messageType" value="text">
            
            <!-- Text Input -->
            <input type="text" id="messageInput" placeholder="اكتب رسالتك..." 
                   class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            
            <!-- File Input -->
            <input type="file" id="fileInput" class="hidden" accept="*/*">
            
            <!-- Voice Recording -->
            <audio id="voicePlayback" class="hidden" controls></audio>
            
            <!-- Buttons -->
            <button type="button" onclick="toggleFileInput()" class="p-2 text-gray-600 hover:text-gray-800" title="إرفاق ملف">
                <i class="fas fa-paperclip"></i>
            </button>
            
            <button type="button" id="voiceBtn" onclick="toggleVoiceRecording()" 
                    class="p-2 text-gray-600 hover:text-gray-800" title="تسجيل صوتي">
                <i class="fas fa-microphone"></i>
            </button>
            
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <!-- Recording Status -->
        <div id="recordingStatus" class="hidden mt-2 p-2 bg-red-100 text-red-700 rounded text-sm">
            <i class="fas fa-circle animate-pulse mr-1"></i>
            جاري التسجيل... اضغط الزر مرة أخرى للإنهاء
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
    
    fetch(`/employee/work-chat/${chatId}/send`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            addMessageToChat(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في إرسال الرسالة');
    });
}

// إرسال ملف
function sendFileMessage() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    if (file.size > 10 * 1024 * 1024) { // 10MB
        alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
        return;
    }
    
    // إظهار مؤشر التحميل
    showUploadProgress();
    
    const formData = new FormData();
    formData.append('message_type', 'file');
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch(`/employee/work-chat/${chatId}/send`, {
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
        alert('حدث خطأ في إرسال الملف');
    });
}

function showUploadProgress() {
    Swal.fire({
        title: 'جاري رفع الملف...',
        html: '<div class="text-center"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i><br><span class="text-sm text-gray-600">يرجى الانتظار</span></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideUploadProgress() {
    Swal.close();
}

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
    input.setSelectionRange(0, 99999);
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
            alert('حجم الملف كبير جداً. الحد الأقصى 10 ميجابايت');
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
            
            document.getElementById('voiceBtn').innerHTML = '<i class="fas fa-stop text-red-500"></i>';
            document.getElementById('recordingStatus').classList.remove('hidden');
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
        document.getElementById('voiceBtn').innerHTML = '<i class="fas fa-microphone"></i>';
        document.getElementById('recordingStatus').classList.add('hidden');
    }
}

function sendVoiceMessage(audioBlob) {
    const reader = new FileReader();
    reader.onload = function() {
        const formData = new FormData();
        formData.append('message_type', 'voice');
        formData.append('voice', reader.result);
        formData.append('duration', Math.round(audioBlob.size / 1000)); // تقدير تقريبي
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch(`/employee/work-chat/${chatId}/send`, {
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
            alert('حدث خطأ في إرسال التسجيل الصوتي');
        });
    };
    reader.readAsDataURL(audioBlob);
}

// إضافة رسالة للشات
function addMessageToChat(message) {
    const messagesList = document.getElementById('messagesList');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message mb-3 ${message.sender_type === 'employee' ? 'text-left' : 'text-right'}`;
    
    let content = '';
    if (message.message_type === 'text') {
        content = `<p>${message.content}</p>`;
    } else if (message.message_type === 'file') {
        content = `
            <div class="flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-file"></i>
                <div>
                    <a href="${message.file_url}" target="_blank" class="underline">${message.file_name}</a>
                    <p class="text-xs opacity-75">${message.file_size_formatted}</p>
                </div>
            </div>
        `;
    } else if (message.message_type === 'voice') {
        content = `
            <audio controls class="max-w-full">
                <source src="${message.file_url}" type="audio/webm">
                متصفحك لا يدعم تشغيل الصوت
            </audio>
        `;
    }
    
    messageDiv.innerHTML = `
        <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${message.sender_type === 'employee' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-800'}">
            ${content}
            <p class="text-xs opacity-75 mt-1">${message.created_at}</p>
        </div>
    `;
    
    messagesList.appendChild(messageDiv);
    scrollToBottom();
}

// تحميل الرسائل
function loadMessages() {
    fetch(`/employee/work-chat/${chatId}/messages`)
        .then(response => response.json())
        .then(data => {
            // تحديث عدد الرسائل إذا لزم الأمر
            // يمكن إضافة منطق لتحديث الرسائل الجديدة فقط
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        });
}

// التمرير لأسفل
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

// تحميل الرسائل عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// إيقاف التسجيل عند إغلاق الصفحة
window.addEventListener('beforeunload', function() {
    if (isRecording) {
        stopRecording();
    }
});

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
    }).catch(() => {
        // fallback للمتصفحات القديمة
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
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
</script>
@endpush