<!-- resources/views/employee/dashboard.blade.php -->
@extends('employee.layouts.app')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'مرحباً ' . auth("employee")->user()->name . '، إليك نظرة عامة على النظام')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mr-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        مرحباً بك {{ auth('employee')->user()->name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ auth('employee')->user()->position }} - {{ auth('employee')->user()->department_name }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        رقم الموظف: {{ auth('employee')->user()->employee_id }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Info Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Roles Count -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-user-tag text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                الأدوار المعينة
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ auth('employee')->user()->roles->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Count -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-key text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="mr-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                الصلاحيات المتاحة
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $permissions->count() }}
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

    <!-- Available Systems -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الأنظمة المتاحة لك</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($permissions as $permission)
                    <div class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-400 hover:bg-gray-50 transition-colors">
                        <div class="text-center">
                            @php
                                $icons = [
                                    'receipts_payments' => 'fa-money-bill-wave',
                                    'customer_movement' => 'fa-users',
                                    'potential_customers' => 'fa-user-plus',
                                    'stopwatch_system' => 'fa-stopwatch',
                                    'general_operations' => 'fa-cogs',
                                    'photography_booking' => 'fa-camera',
                                    'designers_account' => 'fa-palette',
                                    'customer_response' => 'fa-comments',
                                    'task_list' => 'fa-tasks',
                                    'renewal_dates' => 'fa-calendar-alt',
                                    'photography_costs' => 'fa-dollar-sign',
                                    'customer_communication' => 'fa-phone',
                                    'design_follow_up' => 'fa-pencil-ruler',
                                    'montage_follow_up' => 'fa-video',
                                ];
                            @endphp
                            <i class="fas {{ $icons[$permission->name] ?? 'fa-cog' }} text-4xl text-gray-400 mb-3"></i>
                            <span class="block text-sm font-medium text-gray-900">{{ $permission->display_name }}</span>
                            <span class="block text-xs text-gray-500 mt-1">{{ $permission->description }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-exclamation-triangle text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد صلاحيات</h3>
                        <p class="text-sm text-gray-500">لم يتم تعيين أي صلاحيات لحسابك حتى الآن</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Current Roles -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الأدوار المعينة لك</h3>
            <div class="space-y-3">
                @forelse(auth('employee')->user()->roles as $role)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <i class="fas fa-user-tag text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-900">{{ $role->name }}</p>
                                <p class="text-sm text-gray-500">{{ $role->description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-key ml-1"></i>
                            {{ $role->permissions->count() }} صلاحيات
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-times text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-500">لم يتم تعيين أي أدوار لحسابك</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection