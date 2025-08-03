@extends('employee.layouts.app')
@section('title', 'إضافة رد جاهز')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-6 sm:p-8">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-black">
                <i class="fas fa-plus-circle  text-red-600 text-3xl"></i>
                إضافة رد جديد للقاموس
            </h2>
           
            <form action="{{ route('employee.customer-response.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="font-medium text-gray-700 flex items-center mb-1">
                            <svg class="w-5 h-5 text-indigo-400 ml-1"></svg> التصنيف
                        </label>
                        <select name="category_id" class="w-full border-2 border-gray-300 rounded px-4 py-2 focus:ring-indigo-400" required>
                            <option value="">اختر تصنيف...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-sm mt-1 text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-medium text-gray-700 flex items-center mb-1">
                            <svg class="w-5 h-5 text-green-500 ml-1"></svg> عنوان الرد
                        </label>
                        <input name="title" type="text" maxlength="80" required
                               class="w-full border-2 border-gray-300 rounded px-4 py-2 focus:ring-green-400"
                               value="{{ old('title') }}" placeholder="اكتب عنوان الرد...">
                        @error('title')<p class="text-sm mt-1 text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-medium text-gray-700 flex items-center mb-1">
                            <svg class="w-5 h-5 text-orange-400 ml-1"></svg> نص الرد
                        </label>
                        <textarea name="body" rows="5" required
                                  class="w-full border-2 border-gray-300 rounded px-4 py-2 focus:ring-orange-400"
                                  placeholder="النص الذي سيظهر للموظف ليقوم بنسخه">{{ old('body') }}</textarea>
                        @error('body')<p class="text-sm mt-1 text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-10">
                    <a href="{{ route('employee.customer-response.index') }}"
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md border">إلغاء</a>
                    <button type="submit"
                        class="bg-red-600 text-white px-7 py-2 rounded-md font-bold hover:bg-red-700">
                        <i class="fas fa-save ml-1"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
