{{-- resources/views/admin/roles/addedit.blade.php --}}
@extends('admin.layouts.app')

@section('title', isset($role) ? 'تعديل دور' : 'إضافة دور جديد')

@section('content')
<div class="container-fluid p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-tag text-red-600 ml-2"></i>
                    {{ isset($role) ? 'تعديل دور' : 'إضافة دور جديد' }}
                </h1>
                <p class="text-gray-600 mt-1">{{ isset($role) ? 'تعديل بيانات الدور' : 'إضافة دور جديد إلى النظام' }}</p>
            </div> -->
            <!-- Breadcrumb -->
            <!-- <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                            <i class="fas fa-home ml-3"></i>
                            الرئيسية
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                            <a href="{{ route('admin.roles.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-red-600">
                                إدارة الأدوار
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500">{{ isset($role) ? 'تعديل دور' : 'إضافة دور جديد' }}</span>
                        </div>
                    </li>
                </ol>
            </nav> -->
        </div>
    </div>

    <!-- Main Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-xl p-6">
                <h3 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-user-tag ml-3"></i>
                    {{ isset($role) ? 'تعديل بيانات الدور' : 'إنشاء دور جديد' }}
                </h3>
                <p class="text-red-100 mt-1">{{ isset($role) ? 'تعديل بيانات الدور وتحديث الصلاحيات' : 'املأ بيانات الدور الجديدة بعناية' }}</p>
            </div>

            <!-- Form Body -->
            <div class="p-6">
                <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" id="roleForm">
                    @csrf
                    @if(isset($role))
                        @method('PUT')
                    @endif

                    <!-- اسم الدور -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag text-red-600 mr-1"></i>
                            اسم الدور <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', isset($role) ? $role->name : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('name') border-red-500 bg-red-50 @enderror"
                               placeholder="أدخل اسم الدور"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            يجب أن يكون اسم الدور واضحاً ومميزاً
                        </p>
                    </div>

                    <!-- وصف الدور -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left text-red-600 mr-1"></i>
                            وصف الدور <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('description') border-red-500 bg-red-50 @enderror"
                                  placeholder="أدخل وصف مفصل للدور ومسؤولياته"
                                  required>{{ old('description', isset($role) ? $role->description : '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الحالة (عند التعديل فقط) -->
                    @if(isset($role))
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-red-600 mr-1"></i>
                                الحالة <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('status') border-red-500 bg-red-50 @enderror"
                                    required>
                                <option value="active" {{ old('status', $role->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $role->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif

                    <!-- الصلاحيات -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            <i class="fas fa-key text-red-600 mr-1"></i>
                            الصلاحيات المتاحة
                        </label>
                        @php
                            $categoryNames = [
                                'financial' => 'الأنظمة المالية',
                                'customers' => 'أنظمة العملاء',
                                'operations' => 'الأنظمة التشغيلية',
                                'production' => 'أنظمة الإنتاج',
                                'design' => 'أنظمة التصميم',
                                'communication' => 'أنظمة التواصل',
                                'tasks' => 'إدارة المهام',
                                'scheduling' => 'أنظمة الجدولة',
                            ];

                            $selectedPermissions = old('permissions', isset($role) ? $role->permissions->pluck('id')->toArray() : []);
                        @endphp

                        @if($permissions->count() > 0)
                            <div class="space-y-6 max-h-[400px] overflow-y-auto">
                                @foreach($permissions as $category => $categoryPermissions)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <i class="fas fa-folder text-blue-500 ml-2"></i>
                                            {{ $categoryNames[$category] ?? $category }}
                                            <span class="mr-2 text-sm text-gray-500">({{ $categoryPermissions->count() }} صلاحيات)</span>
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($categoryPermissions as $permission)
                                                <div class="relative flex items-start bg-white rounded-md p-3 border border-gray-200 hover:bg-gray-50 transition-colors">
                                                    <div class="flex items-center h-5">
                                                        <input id="permission_{{ $permission->id }}" 
                                                               name="permissions[]" 
                                                               type="checkbox" 
                                                               value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}
                                                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded cursor-pointer">
                                                    </div>
                                                    <div class="mr-3 text-sm flex-1">
                                                        <label for="permission_{{ $permission->id }}" class="font-medium text-gray-700 block cursor-pointer">
                                                            {{ $permission->display_name }}
                                                        </label>
                                                        @if($permission->description)
                                                            <p class="text-gray-500 text-xs mt-1">{{ $permission->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- Buttons to select/deselect all in category -->
                                        <div class="mt-3 flex space-x-2 space-x-reverse">
                                            <button type="button" 
                                                    onclick="selectCategoryPermissions('{{ $category }}', true)"
                                                    class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                                                تحديد الكل
                                            </button>
                                            <button type="button" 
                                                    onclick="selectCategoryPermissions('{{ $category }}', false)"
                                                    class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition-colors">
                                                إلغاء الكل
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Global select/deselect all buttons -->
                                <div class="mt-4 flex justify-center space-x-4 space-x-reverse">
                                    <button type="button" 
                                            onclick="selectAllPermissions(true)"
                                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                        <i class="fas fa-check-double ml-1"></i>
                                        تحديد جميع الصلاحيات
                                    </button>
                                    <button type="button" 
                                            onclick="selectAllPermissions(false)"
                                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-times ml-1"></i>
                                        إلغاء جميع الصلاحيات
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-exclamation-triangle text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-500">لا توجد صلاحيات متاحة في النظام</p>
                            </div>
                        @endif
                        @error('permissions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center justify-center font-medium">
                            <i class="fas fa-save ml-2"></i>
                            {{ isset($role) ? 'تحديث الدور' : 'حفظ الدور' }}
                        </button>
                        <a href="{{ route('admin.roles.index') }}" 
                           class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors text-center inline-flex items-center justify-center font-medium">
                            <i class="fas fa-arrow-left ml-2"></i>
                            رجوع
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-question-circle text-gray-600 ml-2"></i>
                نصائح مفيدة
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 ml-2 mt-0.5"></i>
                    <div>
                        <strong>اسم الدور:</strong> اختر اسماً يعبر عن دور المستخدم وصلاحياته بوضوح
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-align-left text-blue-500 ml-2 mt-0.5"></i>
                    <div>
                        <strong>الوصف:</strong> اكتب وصفاً دقيقاً لمهام الدور واختصاصه لتسهيل الإدارة لاحقاً
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-key text-green-500 ml-2 mt-0.5"></i>
                    <div>
                        <strong>الصلاحيات:</strong> حدد الصلاحيات المطلوبة فقط لكل دور لتقليل الأخطاء
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-lock text-red-500 ml-2 mt-0.5"></i>
                    <div>
                        <strong>التحكم:</strong> يمكنك تغيير الصلاحيات لاحقاً من صفحة تعديل الدور بمرونة
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectCategoryPermissions(category, select) {
    const names = {
        'financial': 'الأنظمة المالية',
        'customers': 'أنظمة العملاء',
        'operations': 'الأنظمة التشغيلية',
        'production': 'أنظمة الإنتاج',
        'design': 'أنظمة التصميم',
        'communication': 'أنظمة التواصل',
        'tasks': 'إدارة المهام',
        'scheduling': 'أنظمة الجدولة',
    };
    const categoryName = names[category] || category;
    const containers = document.querySelectorAll('.bg-gray-50');
    containers.forEach(container => {
        const title = container.querySelector('h4');
        if (title && title.textContent.includes(categoryName)) {
            const boxes = container.querySelectorAll('input[name=\"permissions[]\"]');
            boxes.forEach(checkbox => { checkbox.checked = select; });
        }
    });
}
function selectAllPermissions(select) {
    document.querySelectorAll('input[name=\"permissions[]\"]').forEach(cb => { cb.checked = select; });
}
</script>
@endpush
