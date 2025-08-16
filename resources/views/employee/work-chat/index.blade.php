@extends('employee.layouts.app')

@section('title', 'شاتات العمل')
@section('page-title', 'شاتات العمل')
@section('page-subtitle', 'التواصل مع الإدارة حول مهام العمل')

@section('content')
<div class="space-y-6">
    <!-- Filter Tabs -->
    <div class="bg-white shadow rounded-lg p-4">
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('employee.design-follow-up') }}" 
               class="px-4 py-2 rounded-md {{ !$type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                <i class="fas fa-comments ml-2"></i>
                جميع الشاتات
            </a>
            
            @if(auth('employee')->user()->hasPermission('design_follow_up'))
                <a href="{{ route('employee.design-follow-up', ['type' => 'design']) }}" 
                   class="px-4 py-2 rounded-md {{ $type === 'design' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-pencil-ruler ml-2"></i>
                    شاتات التصميم
                </a>
            @endif
            
            @if(auth('employee')->user()->hasPermission('montage_follow_up'))
                <a href="{{ route('employee.montage-follow-up', ['type' => 'montage']) }}" 
                   class="px-4 py-2 rounded-md {{ $type === 'montage' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    <i class="fas fa-video ml-2"></i>
                    شاتات المونتاج
                </a>
            @endif
        </div>
    </div>

    <!-- Chats List -->
    <div class="bg-white shadow rounded-lg">
        @if($chats->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($chats as $chat)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-{{ $chat->type === 'design' ? 'blue' : 'purple' }}-500 flex items-center justify-center">
                                            <i class="fas {{ $chat->type === 'design' ? 'fa-pencil-ruler' : 'fa-video' }} text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $chat->title }}</h3>
                                        <p class="text-sm text-gray-500">مع: {{ $chat->admin->name ?? 'مدير' }}</p>
                                        @if($chat->messages->first())
                                            <p class="text-sm text-gray-600 mt-1">
                                                آخر رسالة: {{ Str::limit($chat->messages->first()->content ?? 'مرفق', 50) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 space-x-reverse">
                                @if($chat->unread_count > 0)
                                    <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                        {{ $chat->unread_count }}
                                    </span>
                                @endif
                                
                                <div class="text-sm text-gray-500">
                                    {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : 'لا توجد رسائل' }}
                                </div>
                                
                                <a href="{{ route('employee.work-chat.show', $chat) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-comment"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $chat->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($chat->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $chat->status === 'active' ? 'نشط' : ($chat->status === 'completed' ? 'مكتمل' : 'مؤرشف') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-comments text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد شاتات</h3>
                <p class="text-gray-600 mb-4">
                    @if($type)
                        لا توجد شاتات {{ $type === 'design' ? 'تصميم' : 'مونتاج' }} حتى الآن
                    @else
                        لا توجد شاتات عمل حتى الآن
                    @endif
                </p>
                <p class="text-sm text-gray-500">
                    سيتم إنشاء الشاتات من قبل الإدارة حسب المهام المطلوبة
                </p>
            </div>
        @endif
    </div>

    <!-- Quick Info -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200 p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
            <i class="fas fa-info-circle text-blue-500 ml-2"></i>
            معلومات مفيدة
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div class="flex items-start">
                <i class="fas fa-comment text-green-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>الرسائل:</strong> يمكنك إرسال رسائل نصية وصوتية ومرفقات
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-file text-blue-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>المرفقات:</strong> الحد الأقصى للملف 10 ميجابايت
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-bell text-yellow-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>الإشعارات:</strong> ستظهر لك الرسائل الجديدة فوراً
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-link text-purple-500 ml-2 mt-0.5"></i>
                <div>
                    <strong>المشاركة:</strong> جميع الملفات تحصل على رابط مباشر
                </div>
            </div>
        </div>
    </div>
</div>
@endsection