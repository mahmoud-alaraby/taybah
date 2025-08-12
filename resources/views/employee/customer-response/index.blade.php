@extends('employee.layouts.app')
@section('title', 'قاموس الردود الجاهزة')

@section('content')
<div class="p-6 space-y-8">

    {{-- فلاتر الردود --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm mb-1">التصنيف</label>
            <select name="category_id" class="border rounded p-2">
                <option value="">الكل</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string)$cat->id === (string)request('category_id') ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">مصدر الإنشاء</label>
            <select name="creator_source" class="border rounded p-2">
                <option value="">الكل</option>
                <option value="admin" {{ request('creator_source')==='admin' ? 'selected':'' }}>أدمن</option>
                <option value="me" {{ request('creator_source')==='me' ? 'selected':'' }}>أنا</option>
                <option value="others" {{ request('creator_source')==='others' ? 'selected':'' }}>موظفون آخرون</option>
            </select>
        </div>
        <button class="bg-blue-600 text-white rounded px-4 py-2">تطبيق</button>
        <a href="{{ route('employee.customer-response.index') }}" class="bg-gray-200 px-4 py-2 rounded">إعادة ضبط</a>
        <a href="{{ route('employee.customer-response.create') }}" class="bg-green-600 text-white rounded px-4 py-2">إضافة رد</a>
    </form>

    {{-- عنوان التصنيفات + زر إضافة --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">التصنيفات</h2>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded" onclick="document.getElementById('addCategoryModal').showModal();">إضافة تصنيف</button>
    </div>

    {{-- Collapsible لعرض التصنيفات --}}
    <div class="border rounded mb-6">
        <button type="button" class="w-full text-right px-4 py-3 bg-gray-100 hover:bg-gray-200 font-semibold" onclick="toggleCategories()">
            عرض/إخفاء قائمة التصنيفات
        </button>
        <div id="categoriesPanel" class="p-4 space-y-2 hidden">
            @forelse($categories as $cat)
                <div class="flex items-center justify-between border rounded p-3">
                    <div class="flex items-center gap-5">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-indigo-600 text-xl">
                            <i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i>
                        </span>
                        <span class="font-semibold text-lg">{{ $cat->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="text-blue-600 hover:text-blue-900" onclick="openEditCategory({{ $cat->id }}, '{{ e($cat->name) }}', '{{ e($cat->icon ?? 'fas fa-tag') }}')" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('employee.customer-response.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('حذف التصنيف؟');" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-900" title="حذف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">لا توجد تصنيفات بعد.</p>
            @endforelse
        </div>
    </div>

    {{-- قائمة الردود --}}
    <div>
        <h2 class="text-xl font-bold mb-3">نصوص جاهزة للرد على العملاء حسب التصنيف</h2>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($responses as $resp)
                <div class="border rounded p-4 space-y-2 relative">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-indigo-600 text-xl">
                            <i class="{{ $resp->category->icon ?? 'fas fa-tag' }}"></i>
                        </span>
                        <span class="font-semibold">{{ $resp->category->name ?? 'بدون تصنيف' }}</span>
                    </div>
                    <div class="font-bold">{{ $resp->title }}</div>
                    <div class="text-sm text-gray-700 whitespace-pre-line" id="text_{{ $resp->id }}">{{ $resp->body }}</div>
                    <div class="flex items-center gap-2 pt-1">
                        <button title="نسخ النص" class="text-gray-600 hover:text-indigo-600" onclick="copyText({{ $resp->id }})">
                            <i class="fas fa-copy"></i>
                        </button>
                        <a href="{{ route('employee.customer-response.edit', $resp->id) }}" class="text-yellow-600 hover:text-yellow-800" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('employee.customer-response.destroy', $resp->id) }}" method="POST" onsubmit="return confirm('حذف الرد؟');" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800" title="حذف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                    <div class="text-xs text-gray-500">المنشئ: {{ $resp->creator_name }} ({{ $resp->created_by_type }})</div>
                </div>
            @empty
                <p class="text-gray-500">لا توجد ردود.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal إضافة وتعديل نفس السابق (انظر الرد السابق) --}}
{{-- ... --}}
<script>
function toggleCategories(){
    const el = document.getElementById('categoriesPanel');
    el.classList.toggle('hidden');
}
function openEditCategory(id, name, icon){
    const form = document.getElementById('editCategoryFormEmp');
    form.action = "{{ route('employee.customer-response.categories.update', ':id') }}".replace(':id', id);
    document.getElementById('editCatNameEmp').value = name;
    const sel = document.getElementById('editCatIconEmp');
    sel.value = icon || 'fas fa-tag';
    syncPreview('editCatIconEmp', 'editIconPreviewEmp');
    document.getElementById('editCategoryModal').showModal();
}
function syncPreview(selectId, previewId){
    const sel = document.getElementById(selectId);
    const cls = sel.value || 'fas fa-tag';
    const holder = document.getElementById(previewId);
    holder.innerHTML = '<i class=\"'+cls+'\"></i>';
}
function copyText(id){
    let el = document.getElementById('text_'+id);
    let text = el.innerText || el.textContent || '';
    navigator.clipboard.writeText(text);
}
document.addEventListener('DOMContentLoaded', function(){
    syncPreview('addCatIconEmp','addIconPreviewEmp');
});
</script>
@endsection
