@extends('admin.layouts.app')
@section('title', 'تصنيفات الردود الجاهزة')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-indigo-700">تصنيفات الردود الجاهزة</h1>
        <a href="{{ route('admin.customer-response.categories.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">+ إضافة تصنيف جديد</a>
    </div>
    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 text-red-600">{{ session('error') }}</div>@endif

    <table class="w-full table-auto bg-white rounded-lg shadow">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-right">الاسم</th>
                <th class="p-2 text-right">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
                <tr class="border-b">
                    <td class="p-2">{{ $cat->name }}</td>
                    <td class="p-2 flex gap-2">
                        <a href="{{ route('admin.customer-response.categories.edit', $cat) }}" title="تعديل" class="text-yellow-700 hover:underline">تعديل</a>
                        <form action="{{ route('admin.customer-response.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-700 hover:underline">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
