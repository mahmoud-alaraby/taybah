@extends($layout)

@section('title', 'المحادثات')
@section('page-title', 'المحادثات')
@section('page-subtitle', 'عرض وإدارة المحادثات الداخلية بين جميع المستخدمين')

@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- بدء محادثة جديدة --}}
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-3">بدء محادثة جديدة</h3>
        <form action="{{ route($storeRoute) }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div class="min-w-[200px]">
                <label for="participant" class="block text-sm font-medium text-gray-700 mb-1">اختر المستخدم</label>
                <select name="participant" id="participant" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                    <option value="">-- اختر --</option>
                    @foreach($usersForNewChat as $user)
                        <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                بدء المحادثة
            </button>
        </form>
    </div>

    {{-- قائمة المحادثات --}}
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-3">محادثاتك</h3>
        @if($conversations->isEmpty())
            <p class="text-gray-500">لا توجد محادثات حتى الآن. استخدم النموذج أعلاه لبدء محادثة مع مدير أو موظف.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($conversations as $conversation)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route($showRoute, $conversation->id) }}" class="font-medium text-red-600 hover:text-red-700">
                                محادثة #{{ $conversation->id }}
                            </a>
                            @if($conversation->data)
                                <span class="text-gray-500 text-sm mr-2">— {{ is_array($conversation->data) ? json_encode($conversation->data) : $conversation->data }}</span>
                            @endif
                        </div>
                        <a href="{{ route($showRoute, $conversation->id) }}" class="text-sm text-gray-500 hover:text-gray-700">
                            فتح المحادثة
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
