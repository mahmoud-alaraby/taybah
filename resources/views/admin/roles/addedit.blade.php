<!-- resources/views/admin/roles/addedit.blade.php -->
@extends('admin.layouts.app')

@section('title', isset($role) ? 'تعديل دور' : 'إضافة دور جديد')
@section('page-title', isset($role) ? 'تعديل دور' : 'إضافة دور جديد')
@section('page-subtitle', isset($role) ? 'تعديل بيانات الدور' : 'إضافة دور جديد إلى النظام')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
                @csrf
                @if(isset($role))
                    @method('PUT')
                @endif
                
                <div class="space-y-6">
                    <!-- اسم الدور -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user-tag ml-1"></i>
                            اسم الدور
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', isset($role) ? $role->name : '') }}"
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('name') border-red-300 @enderror"
                               placeholder="أدخل اسم الدور"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- وصف الدور -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left ml-1"></i>
                            وصف الدور
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-3 px-4 @error('description') border-red-300 @enderror"
                                  placeholder="أدخل وصف مفصل للدور ومسؤولياته"
                                  required>{{ old('description', isset($role) ? $role->description : '') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- الحالة (في حالة التعديل فقط) -->
                    @if(isset($role))
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-toggle-on ml-1"></i>
                                الحالة
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="mt-1 block w-full py-3 px-4 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm @error('status') border-red-300 @enderror"
                                    required>
                                <option value="active" {{ old('status', $role->status) == 'active' ? 'selected' : '' }}>
                                    نشط
                                </option>
                                <option value="inactive" {{ old('status', $role->status) == 'inactive' ? 'selected' : '' }}>
                                    غير نشط
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    <!-- الصلاحيات -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            <i class="fas fa-key ml-1"></i>
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
                            <div class="space-y-6">
                                @foreach($permissions as $category => $categoryPermissions)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                            <i class="fas fa-folder ml-2 text-blue-500"></i>
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
                                                               class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
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
                                        
                                        <!-- أزرار تحديد الكل/إلغاء الكل للفئة -->
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
                            </div>
                            
                            <!-- أزرار تحديد الكل/إلغاء الكل العامة -->
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
                </div>
                
                <!-- الأزرار -->
                <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
                    <a href="{{ route('admin.roles.index') }}" 
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-times ml-1"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-save ml-1"></i>
                        {{ isset($role) ? 'تحديث' : 'إضافة' }} الدور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// تحديد/إلغاء تحديد صلاحيات فئة معينة
function selectCategoryPermissions(category, select) {
    const categoryPermissions = document.querySelectorAll(`input[name="permissions[]"]`);
    const categoryElement = document.querySelector(`h4:contains("${category}")`);
    
    categoryPermissions.forEach(checkbox => {
        const permissionRow = checkbox.closest('.bg-white');
        const categoryContainer = checkbox.closest('.bg-gray-50');
        const categoryTitle = categoryContainer.querySelector('h4').textContent;
        
        // التحقق من الفئة بطريقة مختلفة
        if (categoryContainer.querySelector('h4').textContent.includes(getCategoryName(category))) {
            checkbox.checked = select;
        }
    });
}

// تحديد/إلغاء تحديد جميع الصلاحيات
function selectAllPermissions(select) {
    const allPermissions = document.querySelectorAll('input[name="permissions[]"]');
    allPermissions.forEach(checkbox => {
        checkbox.checked = select;
    });
}

// الحصول على اسم الفئة بالعربية
function getCategoryName(category) {
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
    return names[category] || category;
}

// تحديد/إلغاء تحديد صلاحيات فئة معينة - إصدار محسن
function selectCategoryPermissions(category, select) {
    // البحث عن العنصر الحاوي للفئة
    const categoryName = getCategoryName(category);
    const categoryContainers = document.querySelectorAll('.bg-gray-50');
    
    categoryContainers.forEach(container => {
        const titleElement = container.querySelector('h4');
        if (titleElement && titleElement.textContent.includes(categoryName)) {
            // العثور على جميع checkboxes في هذه الفئة
            const checkboxes = container.querySelectorAll('input[name="permissions[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = select;
            });
        }
    });
}

// تحديد/إلغاء تحديد جميع الصلاحيات
function selectAllPermissions(select) {
    const allPermissions = document.querySelectorAll('input[name="permissions[]"]');
    allPermissions.forEach(checkbox => {
        checkbox.checked = select;
    });
}

// إضافة مؤثرات بصرية عند التحديد
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const parentDiv = this.closest('.bg-white');
            if (this.checked) {
                parentDiv.classList.add('ring-2', 'ring-red-200', 'bg-red-50');
                parentDiv.classList.remove('bg-white');
            } else {
                parentDiv.classList.remove('ring-2', 'ring-red-200', 'bg-red-50');
                parentDiv.classList.add('bg-white');
            }
        });
        
        // تطبيق التأثير على الصلاحيات المحددة مسبقاً
        if (checkbox.checked) {
            const parentDiv = checkbox.closest('.bg-white');
            parentDiv.classList.add('ring-2', 'ring-red-200', 'bg-red-50');
            parentDiv.classList.remove('bg-white');
        }
    });
});

// عداد الصلاحيات المحددة
function updatePermissionCount() {
    const totalPermissions = document.querySelectorAll('input[name="permissions[]"]').length;
    const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked').length;
    
    // إنشاء أو تحديث عداد
    let counterElement = document.getElementById('permission-counter');
    if (!counterElement) {
        counterElement = document.createElement('div');
        counterElement.id = 'permission-counter';
        counterElement.className = 'mt-4 text-center text-sm text-gray-600 bg-blue-50 p-3 rounded-lg';
        
        const permissionsContainer = document.querySelector('label[class*="fas fa-key"]').parentElement;
        permissionsContainer.appendChild(counterElement);
    }
    
    counterElement.innerHTML = `
        <i class="fas fa-info-circle ml-1"></i>
        تم تحديد <span class="font-bold text-blue-600">${selectedPermissions}</span> من أصل <span class="font-bold">${totalPermissions}</span> صلاحيات
    `;
}

// تحديث العداد عند التحميل وعند التغيير
document.addEventListener('DOMContentLoaded', function() {
    updatePermissionCount();
    
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionCount);
    });
});

// التحقق من وجود صلاحيات محددة قبل الإرسال
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedPermissions = document.querySelectorAll('input[name="permissions[]"]:checked');
            
            if (selectedPermissions.length === 0) {
                e.preventDefault();
                
                // عرض رسالة تحذير
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تحذير!',
                        text: 'يجب اختيار صلاحية واحدة على الأقل',
                        icon: 'warning',
                        confirmButtonColor: '#dc143c',
                        confirmButtonText: 'موافق'
                    });
                } else {
                    alert('يجب اختيار صلاحية واحدة على الأقل');
                }
                
                // التمرير إلى قسم الصلاحيات
                const permissionsSection = document.querySelector('label[class*="fas fa-key"]');
                if (permissionsSection) {
                    permissionsSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});
</script>
@endpush