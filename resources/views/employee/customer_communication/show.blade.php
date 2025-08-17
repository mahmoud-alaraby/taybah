@extends('employee.layouts.app')
@section('title', $chat->potentialCustomer->customer_name ?? 'شات العميل')
@section('page-title', $chat->potentialCustomer->customer_name ?? 'شات العميل')
@section('page-subtitle', 'رقم العميل: ' . ($chat->potentialCustomer->phone ?? '---'))

@section('content')

<div class="chat-header mb-3">
    <span>وصف العمل: {{ $chat->potentialCustomer->work_description ?? 'لا يوجد وصف' }}</span>
</div>

{{-- الرسائل --}}
<div id="chat-messages" class="chat-messages">
    @foreach($messages as $message)
        <div class="chat-message {{ $message->sender_type == 'employee' ? 'chat-me' : 'chat-other' }}">
            <div class="sender">
                <b>{{ $message->sender_name }}</b>
                <small class="msg-time">{{ $message->created_at->format('H:i') }}</small>
            </div>
            <div class="message-content">
                @if($message->message_type === 'text')
                    <div class="text-message">{{ $message->content }}</div>
                @elseif($message->message_type === 'file')
                    <div class="msg-file">
                        <a href="{{ $message->file_url }}" target="_blank" class="file-link">
                            <i class="fas fa-paperclip"></i> {{ $message->file_name }}
                        </a>
                        <span class="file-size">({{ $message->file_size_formatted }})</span>
                    </div>
                @elseif($message->message_type === 'voice')
                    <div class="msg-voice">
                        <audio controls src="{{ $message->file_url }}"></audio>
                        <span class="voice-duration">{{ $message->voice_duration['formatted'] ?? '-' }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- نموذج إرسال الرسائل - يدعم كل المزايا الأساسية --}}
<form id="chatSendForm" method="POST" enctype="multipart/form-data" action="{{ route('employee.customer-communication.sendMessage', $chat->id) }}">
    @csrf
    <div class="input-group mb-2">
        <input type="text" name="content" class="form-control" placeholder="اكتب رسالة نصية ..." />
        <button type="submit" name="message_type" value="text" class="btn btn-success">إرسال نص</button>
    </div>
    <div class="input-group mb-2">
        <input type="file" name="file" class="form-control" accept="image/*,application/pdf,.doc,.docx" />
        <button type="submit" name="message_type" value="file" class="btn btn-info">إرسال ملف</button>
    </div>
    <div class="input-group mb-2">
        <button type="button" id="startVoice" class="btn btn-warning">بدء التسجيل الصوتي</button>
        <input type="hidden" name="voice" id="voiceInput" />
        <input type="hidden" name="duration" id="voiceDuration" />
        <button type="submit" name="message_type" value="voice" class="btn btn-warning" id="sendVoice" disabled>إرسال صوت</button>
    </div>
</form>

{{-- قسم الجافاسكريبت - سجل من work_chat --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// يمكنك نقل سكربت التسجيل الصوتي وتحديث الرسائل AJAX من work_chat هنا
// الدعم الكامل للرسائل الصوتية والإدراج الفوري

// مثال بدائي جدا للتحديث
// setInterval(function(){
//     axios.get('{{ route("employee.customer-communication.messages", $chat->id) }}')
//         .then(function(response){
//             // تحديث DOM بالرسائل الجديدة حسب النظام
//         });
// }, 3000);

</script>
@endsection

@endsection
