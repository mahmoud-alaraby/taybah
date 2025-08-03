@extends('admin.layouts.app')
@section('title', 'إضافة قيد تصوير جديد')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-2xl font-bold text-black mb-7 flex items-center gap-2">
                <i class="fas fa-plus-circle text-red-600 text-3xl"></i>
                إضافة قيد تصوير جديد
            </h2>
            <form action="{{ route('admin.photography-costs.store') }}" method="POST">
                @csrf
                <div class="space-y-7">

                    <!-- التاريخ -->
                    <div>
                        <label for="date" class="block text-base font-medium text-gray-800 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 ml-1"></i> التاريخ
                        </label>
                        <input type="date" name="date" id="date" required
                               class="mt-1 border-2 border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full rounded-md py-3 px-4 shadow-sm @error('date') border-red-400 @enderror"
                               value="{{ old('date') }}">
                        @error('date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- النوع -->
                    <div>
                        <label for="type" class="block text-base font-medium text-gray-800 mb-1">
                            <i class="fas fa-exchange-alt text-yellow-600 ml-1"></i> النوع
                        </label>
                        <select name="type" id="type" required
                            class="mt-1 border-2 border-gray-300 focus:ring-yellow-400 focus:border-yellow-400 block w-full rounded-md py-3 px-4 shadow-sm @error('type') border-red-400 @enderror">
                            <option value="receipt" {{ old('type') === 'receipt' ? 'selected' : '' }}>مقبوضات</option>
                            <option value="payment" {{ old('type') === 'payment' ? 'selected' : '' }}>مدفوعات</option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- المبلغ -->
                    <div>
                        <label for="amount" class="block text-base font-medium text-gray-800 mb-1">
                            <i class="fas fa-coins text-orange-500 ml-1"></i> المبلغ
                        </label>
                        <input type="number" name="amount" id="amount" step="0.01" required
                               class="mt-1 border-2 border-gray-300 focus:ring-orange-400 focus:border-orange-400 block w-full rounded-md py-3 px-4 shadow-sm @error('amount') border-red-400 @enderror"
                               value="{{ old('amount') }}">
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- البيان / الوصف -->
                    <div>
                        <label for="note" class="block text-base font-medium text-gray-800 mb-1">
                            <i class="fas fa-align-left text-purple-500 ml-1"></i> البيان / الوصف
                        </label>
                        <textarea name="note" id="note" rows="4"
                                  class="mt-1 border-2 border-gray-300 focus:ring-purple-400 focus:border-purple-400 block w-full rounded-md py-3 px-4 shadow-sm @error('note') border-red-400 @enderror"
                                  placeholder="اكتب وصفاً أو بياناً للقيد">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end space-x-4 space-x-reverse">
                    <a href="{{ route('admin.photography-costs.index') }}"
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-arrow-right ml-1"></i> رجوع
                    </a>
                    <button type="submit"
                            class="bg-red-600 py-2 px-7 border border-transparent rounded-md shadow-sm text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-save ml-1"></i> إضافة القيد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
