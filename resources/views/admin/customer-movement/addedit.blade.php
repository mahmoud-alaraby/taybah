@extends('admin.layouts.app')

@section('title', isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد')
@section('page-title', isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد')
@section('page-subtitle', isset($customerMovement) ? 'تعديل بيانات حركة العميل والاتفاق' : 'إضافة حركة عميل جديد وتفاصيل الاتفاق')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5 rounded-t-xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mr-4">
                            <h1 class="text-xl sm:text-2xl font-bold text-white">{{ isset($customerMovement) ? 'تعديل حركة العميل' : 'إضافة حركة عميل جديد' }}</h1>
                            <p class="text-red-100 text-sm mt-1">{{ isset($customerMovement) ? 'تحديث معلومات الاتفاق وتحرير بيانات العميل' : 'قم بإضافة حركة جديدة لعميل واتفاقه المالي' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.customer-movement.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors duration-200 border border-white/20">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ isset($customerMovement) ? route('admin.customer-movement.update', $customerMovement) : route('admin.customer-movement.store') }}"
                  method="POST" class="p-6 sm:p-8" novalidate>
                @csrf
                @if(isset($customerMovement))
                    @method('PUT')
                @endif

                <div class="space-y-8">

                    {{-- معلومات العميل --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12a4 4 0 108 0 4 4 0 00-8 0zM12 14c-5.523 0-10 2.239-10 5v3"></path>
                                </svg>
                            </div>
                            معلومات العميل
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- الموظف المسؤول --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4 text-red-500 ml-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    الموظف المسؤول
                                </label>
                                <select name="employee_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('employee_id') border-red-600 ring-2 ring-red-200 @enderror">
                                    <option value="">لا يوجد موظف محدد</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ (old('employee_id', $customerMovement->employee_id ?? '') == $employee->id) ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- اسم العميل --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4 text-indigo-600 ml-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.79.648 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    اسم العميل
                                </label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $customerMovement->customer_name ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('customer_name') border-red-600 ring-2 ring-red-200 @enderror"
                                       placeholder="أدخل اسم العميل" required>
                                @error('customer_name')
                                    <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            {{-- رقم جوال العميل --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4 text-green-600 ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h1l3 8 4-16 3 8h1"></path>
                                    </svg>
                                    رقم جوال العميل
                                </label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone', $customerMovement->customer_phone ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('customer_phone') border-red-600 ring-2 ring-red-200 @enderror"
                                       placeholder="مثال: 0501234567" required>
                                @error('customer_phone')
                                    <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                        <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- وصف العمل --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 mt-6 flex items-center space-x-2 space-x-reverse">
                            <svg class="w-4 h-4 text-yellow-600 ml-2" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h11l5 5v9a2 2 0 01-2 2z"></path>
                            </svg>
                            وصف العمل
                        </label>
                        <textarea name="work_description" rows="4"
                                  class="w-full px-4 py-3 ml-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('work_description') border-red-600 ring-2 ring-red-200 @enderror"
                                  placeholder="وصف تفصيلي للعمل المتفق عليه" required>{{ old('work_description', $customerMovement->work_description ?? '') }}</textarea>
                        @error('work_description')
                            <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-6">
                        {{-- نوعية العميل --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center space-x-2 space-x-reverse">
                                <svg class="w-4 h- ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-3-3v6m9 3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                نوعية العميل
                            </label>
                            <select name="customer_type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('employee_id') border-red-600 ring-2 ring-red-200 @enderror"
                                    required>
                                @foreach($customerTypes as $key => $type)
                                    <option value="{{ $key }}" {{ (old('customer_type', $customerMovement->customer_type ?? 'غير محدد') == $key) ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_type')
                                <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                    <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- حالة العمل --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center space-x-2 space-x-reverse">
                                <svg class="w-4 h-4 text-pink-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3"></path>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                </svg>
                                حالة العمل
                            </label>
                            <select name="work_status"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('employee_id') border-red-600 ring-2 ring-red-200 @enderror"
                                    required>
                                @foreach($workStatuses as $key => $status)
                                    <option value="{{ $key }}" {{ (old('work_status', $customerMovement->work_status ?? 'جاري العمل') == $key) ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('work_status')
                                <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                    <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- معلومات الاتفاق --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-green-600 ml-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                </svg>
                            </div>
                            معلومات الاتفاق
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4 ml-2 text-cyan-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    بداية الاتفاق
                                </label>
                                <input type="date" name="agreement_start_date"
                                       value="{{ old('agreement_start_date', isset($customerMovement) ? $customerMovement->agreement_start_date->format('Y-m-d') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('agreement_start_date') border-red-600 ring-2 ring-red-200 @enderror"
                                       required>
                                @error('agreement_start_date')
                                    <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                        <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                    <svg class="w-4 h-4 text-cyan- ml-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 10h1l3 8 4-16 3 8h1"></path>
                                    </svg>
                                    موعد التسليم الأولي
                                </label>
                                <input type="date" name="initial_delivery_date"
                                       value="{{ old('initial_delivery_date', isset($customerMovement) ? $customerMovement->initial_delivery_date->format('Y-m-d') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('initial_delivery_date') border-red-600 ring-2 ring-red-200 @enderror"
                                       required>
                                @error('initial_delivery_date')
                                    <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                        <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2 mt-6">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                <svg class="w-4 h-4 ml-2 text-cyan-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                موعد التسليم النهائي
                            </label>
                            <input type="date" name="final_delivery_date"
                                   value="{{ old('final_delivery_date', isset($customerMovement) ? $customerMovement->final_delivery_date->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('final_delivery_date') border-red-600 ring-2 ring-red-200 @enderror"
                                   required>
                            @error('final_delivery_date')
                                <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2 mt-6">
                            <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                <svg class="w-4 h-4 text-cyan-600 ml-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                                    <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                </svg>
                                المبلغ المتفق عليه (ريال)
                            </label>
                            <input type="number" name="agreed_amount" step="0.01" min="0"
                                   value="{{ old('agreed_amount', $customerMovement->agreed_amount ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('agreed_amount') border-red-600 ring-2 ring-red-200 @enderror"
                                   placeholder="0.00" required>
                            @error('agreed_amount')
                                <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            @foreach(['first_payment'=>'الدفعة الأولى', 'second_payment'=>'الدفعة الثانية', 'third_payment'=>'الدفعة الثالثة', 'fourth_payment'=>'الدفعة الرابعة'] as $field => $label)
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4 ml-2 text-cyan-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c1.38 0 2.5 1.12 2.5 2.5A2.5 2.5 0 0 1 12 13"></path>
                                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                        </svg>
                                        {{ $label }} (ريال)
                                    </label>
                                    <input type="number" name="{{ $field }}" step="0.01" min="0"
                                           value="{{ old($field, $customerMovement->$field ?? '0') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error($field) border-red-600 ring-2 ring-red-200 @enderror"
                                           placeholder="0.00">
                                    @error($field)
                                        <p class="text-red-600 text-xs mt-1 flex items-center space-x-1 space-x-reverse">
                                            <svg class="w-3 h-3 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                      clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        @if(isset($customerMovement))
                            <div class="bg-gray-50 p-4 rounded-lg mt-6">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">ملخص المبالغ</h5>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">إجمالي المدفوع:</span>
                                        <span class="font-medium text-green-600" id="totalPaid">{{ number_format($customerMovement->total_paid, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">المتبقي:</span>
                                        <span class="font-medium text-red-600" id="remaining">{{ number_format($customerMovement->remaining_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4 sm:space-x-reverse pt-8 border-t border-gray-200 mt-8">
                    <a href="{{ route('admin.customer-movement.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-700 to-red-800 hover:from-red-800 hover:to-red-900 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        {{ isset($customerMovement) ? 'تحديث' : 'حفظ' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function calculateRemaining() {
        const agreedAmount = parseFloat(document.querySelector('input[name="agreed_amount"]').value) || 0;
        const firstPayment = parseFloat(document.querySelector('input[name="first_payment"]').value) || 0;
        const secondPayment = parseFloat(document.querySelector('input[name="second_payment"]').value) || 0;
        const thirdPayment = parseFloat(document.querySelector('input[name="third_payment"]').value) || 0;
        const fourthPayment = parseFloat(document.querySelector('input[name="fourth_payment"]').value) || 0;

        const totalPaid = firstPayment + secondPayment + thirdPayment + fourthPayment;
        const remaining = agreedAmount - totalPaid;

        const totalPaidElement = document.getElementById('totalPaid');
        const remainingElement = document.getElementById('remaining');

        if (totalPaidElement && remainingElement) {
            totalPaidElement.textContent = totalPaid.toLocaleString('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            remainingElement.textContent = remaining.toLocaleString('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            remainingElement.className = remaining < 0 ? 'font-medium text-red-600' : 'font-medium text-green-600';
        }
    }

    ['agreed_amount', 'first_payment', 'second_payment', 'third_payment', 'fourth_payment'].forEach(name => {
        const input = document.querySelector(`input[name="${name}"]`);
        if(input){
            input.addEventListener('input', calculateRemaining);
        }
    });

    calculateRemaining();
});
</script>
@endpush
