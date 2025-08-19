@extends('admin.layouts.app')
@section('title', 'عرض قيد التصوير')
@section('content')

@php
$month = $photographyCost->date->format('m');
$year = $photographyCost->date->format('Y');
$receipts = \App\Models\PhotographyCost::where('type', 'receipt')
    ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
$payments = \App\Models\PhotographyCost::where('type', 'payment')
    ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center space-x-5 space-x-reverse">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-red-600 flex items-center justify-center">
                        <i class="fas fa-camera text-gray-200 text-3xl"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        تفاصيل القيد رقم #{{ $photographyCost->id }}
                    </h2>
                    <div class="flex flex-wrap gap-3 text-base text-gray-700">
                        <span>
                            <i class="fas fa-calendar-alt ml-1"></i>
                            التاريخ: <span class="font-semibold">{{ $photographyCost->date->format('Y-m-d') }}</span>
                        </span>
                        <span>
                            <i class="fas fa-sort-amount-up ml-1"></i>
                            النوع: 
                            <span class="font-semibold {{ $photographyCost->type == 'receipt' ? 'text-green-600' : 'text-yellow-700' }}">
                                {{ $photographyCost->type == 'receipt' ? 'مقبوضات' : 'مدفوعات' }}
                            </span>
                        </span>
                        <span>
                            <i class="fas fa-money-bill mx-1"></i>
                            المبلغ: <span class="font-semibold text-gray-900">{{ number_format($photographyCost->amount,2) }} ريال</span>
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-user-edit ml-1"></i>
                        أنشئ بواسطة: 
                        <span class="font-semibold text-red-600">{{ $photographyCost->creator_name }}</span>
                    </div>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('admin.photography-costs.edit', $photographyCost) }}" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        <i class="fas fa-edit ml-2"></i>تعديل
                    </a>
                    <a href="{{ route('admin.photography-costs.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                        <i class="fas fa-arrow-right ml-2"></i>العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- كارد البيان/الوصف -->
    <div class="bg-gray-50 shadow border border-gray-200 rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                <i class="fas fa-align-left ml-2 text-gray-400"></i> البيان / الوصف
            </h3>
            <div class="text-base text-gray-800 leading-relaxed break-words">
                {!! $photographyCost->note ? nl2br(e($photographyCost->note)) : '<span class="text-gray-400">لا يوجد بيان مضاف</span>' !!}
            </div>
        </div>
    </div>

    <!-- مقارنة المقبوضات والمدفوعات -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-bar ml-2 text-green-500"></i>
                مقارنة المقبوضات والمدفوعات خلال شهر {{ $photographyCost->date->format('Y-m') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="mb-2 text-gray-800 font-semibold text-base">المقبوضات</div>
                    <div class="w-full bg-gray-100 rounded-full h-6">
                        <div style="width: {{ $receipts + $payments > 0 ? ($receipts/($receipts+$payments))*100 : 0 }}%" class="h-6 bg-green-500 rounded-full flex items-center pl-2 text-white font-bold text-base">
                            {{ number_format($receipts,2) }} ريال
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mb-2 text-gray-800 font-semibold text-base">المدفوعات</div>
                    <div class="w-full bg-gray-100 rounded-full h-6">
                        <div style="width: {{ $receipts + $payments > 0 ? ($payments/($receipts+$payments))*100 : 0 }}%" class="h-6 bg-yellow-400 rounded-full flex items-center pl-2 text-white font-bold text-base">
                            {{ number_format($payments,2) }} ريال
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 text-center font-bold">
                الصافي = {{ number_format($receipts - $payments,2) }} ريال
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $photographyCost->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">آخر تحديث</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $photographyCost->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
