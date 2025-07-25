@extends('admin.layouts.app')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'مرحباً ' . auth("admin")->user()->name . '، إليك نظرة عامة على النظام')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-home text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mr-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        مرحباً بك في نظام شركة طيبة
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        نظام إدارة متكامل للتسويق والخدمات
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Admins -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                إجمالي المديرين
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ \App\Models\Admin::count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Status -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                حالة النظام
                            </dt>
                            <dd class="text-lg font-medium text-green-600">
                                نشط
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Login -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                آخر دخول
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                الآن
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الإجراءات السريعة</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.admins.index') }}" 
                   class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-400 hover:bg-gray-50 transition-colors">
                    <div class="text-center">
                        <i class="fas fa-users-cog text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">إدارة المديرين</span>
                    </div>
                </a>

                <div class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-project-diagram text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">المشاريع</span>
                        <span class="block text-xs text-gray-500">قريباً</span>
                    </div>
                </div>

                <div class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-bar text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">التقارير</span>
                        <span class="block text-xs text-gray-500">قريباً</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection