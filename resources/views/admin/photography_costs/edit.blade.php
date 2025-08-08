@extends('admin.layouts.app')
@section('title', 'تعديل قيد تصوير')

@section('content')
<div class="max-w-4xl mx-auto">
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
                            <h1 class="text-xl sm:text-2xl font-bold text-white">   إضافة قيد تصوير جديد</h1>
                            <p class="text-blue-100 text-sm mt-1">أضف قيد تصوير جديد الى النظام</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.photography-costs.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
            <form action="{{ route('admin.photography-costs.update', $photographyCost) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-7 px-4 py-5 sm:p-6">

                    <!-- التاريخ -->
                    <div>
                        <label for="date" class="block text-base font-medium text-gray-800 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 ml-1"></i> التاريخ
                        </label>
                        <input type="date" name="date" id="date" required
                               class="mt-1 border-2 border-gray-300 focus:ring-blue-500 focus:border-blue-500 block w-full rounded-md py-3 px-4 shadow-sm @error('date') border-red-400 @enderror"
                               value="{{ old('date', $photographyCost->date->format('Y-m-d')) }}">
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
                            <option value="receipt" {{ old('type', $photographyCost->type) == 'receipt' ? 'selected' : '' }}>مقبوضات</option>
                            <option value="payment" {{ old('type', $photographyCost->type) == 'payment' ? 'selected' : '' }}>مدفوعات</option>
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
                               value="{{ old('amount', $photographyCost->amount) }}">
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
                                  placeholder="اكتب وصفاً أو بياناً للقيد">{{ old('note', $photographyCost->note) }}</textarea>
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
                        <i class="fas fa-save ml-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
