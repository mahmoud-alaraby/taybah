@extends('employee.layouts.app')
@section('title', 'تعديل رد جاهز')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="">
          {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">   إضافة رد جديد</h1>
                            <p class="text-blue-100 text-sm mt-1">أضف ردود جديدة   الى النظام</p>
                        </div>
                    </div>
                    <a href="{{ route('employee.customer-response.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
            <form action="{{ route('employee.customer-response.update', $customerResponse) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6 px-4 py-6 sm:p-8">
                    <div>
                        <label class="font-medium text-gray-700 flex items-center mb-1">
                            <svg class="w-5 h-5 text-indigo-400 ml-1"></svg> التصنيف
                        </label>
                        <select name="category_id" class="w-full border-2 border-gray-300 rounded px-4 py-2 focus:ring-indigo-400" required>
                            <option value="">اختر تصنيف...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id',$customerResponse->category_id)==$cat->id)>{{ $cat->name }}</option>
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
                               value="{{ old('title', $customerResponse->title) }}" placeholder="اكتب عنوان الرد...">
                        @error('title')<p class="text-sm mt-1 text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-medium text-gray-700 flex items-center mb-1">
                            <svg class="w-5 h-5 text-orange-400 ml-1"></svg> نص الرد
                        </label>
                        <textarea name="body" rows="5" required
                                  class="w-full border-2 border-gray-300 rounded px-4 py-2 focus:ring-orange-400"
                                  placeholder="النص الذي سيظهر للموظف ليقوم بنسخه">{{ old('body', $customerResponse->body) }}</textarea>
                        @error('body')<p class="text-sm mt-1 text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end gap-4 mt-10">
                    <a href="{{ route('employee.customer-response.index') }}"
                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md border">إلغاء</a>
                    <button type="submit"
                        class="bg-red-600 text-white px-7 py-2 rounded-md font-bold hover:bg-red-700">
                        <i class="fas fa-save ml-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
