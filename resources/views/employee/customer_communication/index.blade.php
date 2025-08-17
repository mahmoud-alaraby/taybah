@extends('employee.layouts.app')
@section('title', 'الشاتات مع العملاء')
@section('page-title', 'تواصل العملاء')
@section('page-subtitle', 'التواصل مع الإدارة حول الطلبات والملاحظات')

@section('content')

{{-- إحصائيات العملاء ويمكنك تعديل التنسيق بحرية --}}
<div class="stats-section mb-3">
    <span>عدد العملاء الكليون: <b>{{ $stats['total'] ?? 0 }}</b></span>
    <span>مكالمات مطلوبة: <b>{{ $stats['calls_pending'] ?? 0 }}</b></span>
    <span>زيارات مطلوبة: <b>{{ $stats['visits_pending'] ?? 0 }}</b></span>
</div>

{{-- فلاتر --}}
<div class="filters mb-4">
    <a href="{{ route('employee.customer-communication.index', ['filter'=>'all']) }}" class="{{ request('filter', 'all')=='all' ? 'active' : '' }}">الكل</a>
    <a href="{{ route('employee.customer-communication.index', ['filter'=>'calls_pending']) }}" class="{{ request('filter')=='calls_pending' ? 'active' : '' }}">مكالمات معلقة</a>
    <a href="{{ route('employee.customer-communication.index', ['filter'=>'visits_pending']) }}" class="{{ request('filter')=='visits_pending' ? 'active' : '' }}">زيارات معلقة</a>
    <a href="{{ route('employee.customer-communication.index', ['filter'=>'contacted']) }}" class="{{ request('filter')=='contacted' ? 'active' : '' }}">عملاء تم التواصل معهم</a>
    <a href="{{ route('employee.customer-communication.index', ['filter'=>'not_contacted']) }}" class="{{ request('filter')=='not_contacted' ? 'active' : '' }}">لم يتم التواصل</a>
</div>

{{-- عرض الشاتات - تصميم مشابه تماماً للوورك شات --}}
<div class="chat-list">
    @forelse($chats as $chat)
        <div class="chat-card">
            <div class="chat-header">
                <span class="chat-title">{{ $chat->potentialCustomer->customer_name ?? '---' }}</span>
                <small><i class="fas fa-phone"></i> {{ $chat->potentialCustomer->phone ?? '' }}</small>
            </div>
            <div class="chat-info">
                <span>آخر رسالة:
                    @if($chat->messages->first())
                        @if($chat->messages->first()->message_type === 'text')
                            {{ Str::limit($chat->messages->first()->content, 50) }}
                        @elseif($chat->messages->first()->message_type === 'file')
                            ملف مرفق
                        @elseif($chat->messages->first()->message_type === 'voice')
                            رسالة صوتية
                        @endif
                    @else
                        لا يوجد رسائل
                    @endif
                </span>
                <span class="badge badge-warning">غير مقروء: {{ $chat->unread_count }}</span>
            </div>
            <a href="{{ route('employee.customer-communication.show', $chat->id) }}" class="btn btn-primary mt-2">فتح الشات</a>
        </div>
    @empty
        <div class="empty-state">لا توجد شاتات عملاء حتى الآن</div>
    @endforelse
</div>

{{-- صلاحيات وملاحظات --}}
<div class="chat-help mt-4">
    <ul>
        <li>يمكنك إرسال نصوص وصور ومستندات وملفات وحتى رسائل صوتية</li>
        <li>الملفات حتى 10 ميجابايت لكل ملف</li>
        <li>الرسائل الجديدة تظهر تلقائيًا مع تنبيه إذا وصلك رد من الإدارة</li>
        <li>كل المرفقات تظهر مع رابط للتحميل أو المشاهدة مباشرة</li>
    </ul>
</div>
@endsection
