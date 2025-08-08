@extends('admin.layouts.app')
@section('title', 'قاموس الردود الجاهزة')

@php
// تعيين الأيقونة واللون المناسب لكل تصنيف
$catIcons = [
    'أسئلة بروفايل'       => ['icon' => 'fas fa-id-card',            'bg' => 'bg-blue-100',    'color' => 'text-blue-700'],
    'أسئلة تصوير'         => ['icon' => 'fas fa-camera-retro',       'bg' => 'bg-yellow-100',  'color' => 'text-yellow-800'],
    'أسئلة تصميم مواقع'   => ['icon' => 'fas fa-laptop-code',        'bg' => 'bg-green-100',   'color' => 'text-green-700'],
    'أسئلة تصميم بشكل عام'=> ['icon' => 'fas fa-paint-brush',        'bg' => 'bg-indigo-100',  'color' => 'text-indigo-700'],
    'أسئلة تسويق'         => ['icon' => 'fas fa-bullhorn',           'bg' => 'bg-rose-100',    'color' => 'text-rose-700'],
    'أسئلة طباعة'         => ['icon' => 'fas fa-print',              'bg' => 'bg-orange-100',  'color' => 'text-orange-700'],
];
@endphp

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none"><!-- icon --></svg>
                قاموس الردود الجاهزة
            </h1>
            <p class="text-gray-500 mt-1">نصوص جاهزة للرد على العملاء حسب التصنيف</p>
        </div>
        <div>
            <a href="{{ route('admin.customer-response.create') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold transition">+ إضافة رد جديد</a>
        </div>
    </div>
    <div class="flex flex-row justify-between gap-4 mb-6">

    <div class="flex gap-2">

        <a href="{{route('admin.customer-response.categories.index') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold transition">إدارة التصنيفات</a>
    </div>
</div>


    <form method="get" class="flex flex-wrap items-center gap-3 mb-8">
        <select name="category_id" class="border border-gray-300 rounded px-3 py-2">
            <option value="">كل التصنيفات</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="creator_source" class="border border-gray-300 rounded px-3 py-2">
            <option value="">جميع المنشئين</option>
            <option value="admin" @selected(request('creator_source')=='admin')>تم الإنشاء بواسطة الأدمن</option>
            <option value="me" @selected(request('creator_source')=='me')>تم الإنشاء بواسطتي</option>
            <option value="others" @selected(request('creator_source')=='others')>تم الإنشاء بواسطة آخرين</option>
        </select>
        <button type="submit" class="bg-gray-800  text-white px-3 py-2 rounded-lg"><i class="fas fa-search ml-1"></i>بحث</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($responses as $res)
            @php
                $cat = $catIcons[$res->category->name] ?? ['icon'=>'fas fa-question-circle', 'bg'=>'bg-gray-100', 'color'=>'text-gray-500'];
            @endphp
            <div class="relative bg-white shadow p-5 flex flex-col border border-gray-200 rounded-2xl min-h-[260px] group transition duration-200 hover:-translate-y-1 hover:shadow-lg"
                style="height:270px;">
                <div class="flex items-start gap-4 mb-2">
                    <div class="shrink-0 {{$cat['bg']}} rounded-lg flex items-center justify-center w-12 h-12">
                        <i class="{{ $cat['icon'] }} {{$cat['color']}} text-xl"></i>
                    </div>
                    <span class="block flex-1 text-md font-bold text-gray-800 truncate">
                        {{ $res->title }}
                    </span>

                         <div class="flex items-center gap-1">
                        <a href="{{ route('admin.customer-response.edit', $res) }}" title="تعديل"
                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-50 hover:bg-yellow-100 text-yellow-600 border border-yellow-200 ml-1">
                            <i class="fas fa-edit text-base"></i>
                        </a>
                        <form action="{{ route('admin.customer-response.destroy',$res) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" title="حذف"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 ml-1">
                                <i class="fas fa-trash text-base"></i>
                            </button>
                        </form>
                        <button type="button"
                                onclick="navigator.clipboard.writeText(`{!! str_replace(['`','\\'],['\`','\\\\'],preg_replace('/\r?\n/','\\n',$res->body)) !!}`);"
                                title="نسخ النص"
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 hover:bg-green-200 text-green-800 border border-green-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M8 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 10.828 2H8zm2 2l6 6m-5-1v5a1 1 0 0 1-2 0V8m0-2h2a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="text-gray-700 text-sm mb-3 break-all flex-1 overflow-hidden" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;">
                    {{ $res->body }}
                </div>
                <div class="flex items-end justify-between mt-auto">
                    <span class="text-xs text-gray-500 flex items-center gap-2">
                        <i class="fas fa-folder text-gray-400"></i> {{ $res->category->name }}
                        <span class="inline-block w-1 h-1 rounded-full mx-1 bg-gray-300"></span>
                        <i class="fas fa-user text-gray-400"></i>
                        <span class="font-semibold text-indigo-600">
                            {{ $res->created_by_type == 'admin' ? ($res->admin->name ?? 'أدمن') : ($res->employee->name ?? 'موظف') }}
                        </span>
                    </span>
               
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
