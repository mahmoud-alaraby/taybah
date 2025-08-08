@extends('employee.layouts.app')
@section('title', 'نظام تكاليف التصوير')
@section('content')

<div class="container-fluid p-6">
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <i class="fas fa-money-bill-wave text-red-600 ml-2"></i>
                    نظام تكاليف التصوير
                </h1>
                <p class="text-gray-600 mt-1">إدارة جميع المقبوضات والمدفوعات الخاصة بالتصوير</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('employee.photography-costs.create') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة قيد جديد
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي المقبوضات لهذا الشهر</p>
                        <p class="text-2xl font-bold">{{ number_format($totalReceipts,2) }} ريال</p>
                    </div>
                    <i class="fas fa-arrow-down w-8 h-8 opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي المدفوعات لهذا الشهر</p>
                        <p class="text-2xl font-bold">{{ number_format($totalPayments,2) }} ريال</p>
                    </div>
                    <i class="fas fa-arrow-up w-8 h-8 opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">الصافي لهذا الشهر</p>
                        <p class="text-2xl font-bold">{{ number_format($totalReceipts - $totalPayments,2) }} ريال</p>
                    </div>
                    <i class="fas fa-calculator w-8 h-8 opacity-80"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search text-red-600 ml-2"></i>
                البحث والتصفية
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('employee.photography-costs.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من المنشيء</label>
                        <select name="creator_source" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">كل الصادرين</option>
                            <option value="admin" {{ request('creator_source') == 'admin' ? 'selected' : '' }}>بواسطة الأدمن</option>
                            <option value="me" {{ request('creator_source') == 'me' ? 'selected' : '' }}>بواسطتي</option>
                            <option value="others" {{ request('creator_source') == 'others' ? 'selected' : '' }}>بواسطة آخرين</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="">جميع الأنواع</option>
                            <option value="receipt" {{ request('type')=='receipt' ? 'selected' : '' }}>مقبوضات</option>
                            <option value="payment" {{ request('type')=='payment' ? 'selected' : '' }}>مدفوعات</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنشئ</label>
                        <input type="text" name="creator_name" value="{{ request('creator_name') }}" placeholder="بحث باسم منشئ السطر"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input type="date" name="date_from"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                            value="{{ request('date_from') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input type="date" name="date_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                            value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                    <a href="{{ route('employee.photography-costs.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors text-center inline-flex items-center justify-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-table text-red-600 ml-2"></i>
                    قائمة المقبوضات والمدفوعات
                </h3>
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    إجمالي: {{ $costs->total() }} قيد
                </span>
            </div>
        </div>

        @if($costs->count() > 0)
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-3 py-3 text-right text-base font-bold text-gray-800 uppercase">التاريخ</th>
                        <th class="px-3 py-3 text-right text-base font-bold text-gray-800 uppercase">النوع</th>
                        <th class="px-3 py-3 text-right text-base font-bold text-gray-800 uppercase">المبلغ</th>
                        <th class="px-3 py-3 text-right text-base font-bold text-gray-800 uppercase">أنشئ بواسطة</th>
                        <th class="px-3 py-3 text-right text-base font-bold text-gray-800 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($costs as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-3 whitespace-nowrap text-base font-bold">{{ $item->date->format('Y-m-d') }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-base font-bold">
                                <span class="inline-flex items-center px-2 py-1 rounded text-base font-bold
                                    {{ $item->type == 'receipt' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->type == 'receipt' ? 'مقبوضات' : 'مدفوعات' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-base font-bold">{{ number_format($item->amount,2) }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-base font-bold text-red-700">{{ $item->creator_name }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('employee.photography-costs.show', $item) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded" title="عرض">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    @if($item->created_by_type == 'employee' && $item->created_by == auth('employee')->id())
                                    <a href="{{ route('employee.photography-costs.edit', $item) }}"
                                       class="text-yellow-600 hover:text-yellow-900 transition-colors p-1 rounded" title="تعديل">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('employee.photography-costs.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 transition-colors p-1 rounded"
                                            onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-camera text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد قيود</h3>
                <p class="text-gray-600 mb-6">لم يتم العثور على أي سجلات تطابق معايير البحث</p>
                <a href="{{ route('employee.photography-costs.create') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة قيد جديد
                </a>
            </div>
        @endif
        @if($costs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $costs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
