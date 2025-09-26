@extends('admin.layouts.app')
@section('title', 'قاموس الردود الجاهزة')
@section('page-title', 'قاموس الردود علي العملاء')
@section('page-subtitle', 'اداره الردود الجاهزه علي العملاء')

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
                <option value="others" {{ request('creator_source')==='others' ? 'selected':'' }}>موظفين آخرين</option>
            </select>
        </div>
        <button class="bg-red-600 text-white rounded px-4 py-2">تطبيق</button>
        <a href="{{ route('admin.customer-response.index') }}" class="bg-gray-200 px-4 py-2 rounded">إعادة ضبط</a>
        <a href="{{ route('admin.customer-response.create') }}" class="bg-green-600 text-white rounded px-4 py-2">إضافة رد</a>
    </form>

    {{-- عنوان التصنيفات + زر إضافة --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold">التصنيفات</h2>
        <button class="bg-red-600 text-white px-4 py-2 rounded" onclick="document.getElementById('addCategoryModal').showModal();">إضافة تصنيف</button>
    </div>

{{-- Collapsible لعرض التصنيفات --}}
<div class="border rounded mb-6">
    <button type="button" 
            class="flex justify-between items-center w-full text-right px-4 py-3 bg-gray-100 hover:bg-gray-200 font-semibold" 
            onclick="toggleCategories()">
        <span>عرض/إخفاء قائمة التصنيفات</span>
        <i id="toggleIcon" class="fas fa-chevron-down"></i>
    </button>

    <div id="categoriesPanel" class="p-4 space-y-2 hidden">
        @forelse($categories as $cat)
            <div class="flex items-center justify-between border rounded p-3">
                <div class="flex items-center gap-5">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 text-red-600 text-xl">
                        <i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i>
                    </span>
                    <span class="font-semibold text-lg">{{ $cat->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-blue-600 hover:text-blue-900" 
                            onclick="openEditCategory({{ $cat->id }}, '{{ e($cat->name) }}', '{{ e($cat->icon ?? 'fas fa-tag') }}')" 
                            title="تعديل">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.customer-response.categories.destroy', $cat->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('حذف التصنيف؟');" 
                          class="inline">
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
    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">📋 نصوص جاهزة للرد على العملاء حسب التصنيف</h2>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($responses as $resp)
            <div class="relative rounded-lg shadow-md p-4 bg-white border border-gray-200 overflow-hidden">

                {{-- البوردر المتدرج أعلى الكارت --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 via-yellow-500 to-green-500"></div>

                {{-- أيقونة + التصنيف --}}
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-50 text-red-600 text-xl shadow-sm">
                        <i class="{{ $resp->category->icon ?? 'fas fa-tag' }}"></i>
                    </span>
                    <span class="font-semibold text-gray-700">
                        {{ $resp->category->name ?? 'بدون تصنيف' }}
                    </span>
                </div>

                {{-- العنوان --}}
                <div class="font-bold text-gray-900 mb-1">{{ $resp->title }}</div>

                {{-- النص --}}
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line mb-3" id="text_{{ $resp->id }}">
                    {{ $resp->body }}
                </div>

                {{-- الإجراءات (نسخ / تعديل / حذف) --}}
                <div class="flex items-center gap-3 border-t pt-2 text-sm">
                    <button title="نسخ النص"
                        class="flex items-center gap-1 text-gray-600 hover:text-red-600 transition"
                        onclick="copyText({{ $resp->id }})">
                        <i class="fas fa-copy"></i>
                        <span>نسخ</span>
                    </button>

                    <a href="{{ route('admin.customer-response.edit', $resp->id) }}"
                        class="flex items-center gap-1 text-yellow-600 hover:text-yellow-800 transition" title="تعديل">
                        <i class="fas fa-edit"></i>
                        <span>تعديل</span>
                    </a>

                    <form action="{{ route('admin.customer-response.destroy', $resp->id) }}" method="POST"
                        onsubmit="return confirm('هل تريد بالتأكيد حذف الرد؟');" class="inline">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-1 text-red-600 hover:text-red-800 transition" title="حذف">
                            <i class="fas fa-trash-alt"></i>
                            <span>حذف</span>
                        </button>
                    </form>
                </div>

                {{-- معلومات إضافية --}}
                <div class="mt-2 text-xs text-gray-500">
                    ✍️ {{ $resp->creator_name }} ({{ $resp->created_by_type }})
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center">🙁 لا توجد ردود.</p>
        @endforelse
    </div>
</div>

</div>

{{-- Toast للنسخ --}}
<div id="copyToast" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg opacity-0 transition-opacity duration-300 z-50">
    تم نسخ الرد!
</div>

{{-- مودال إضافة تصنيف --}}
<dialog id="addCategoryModal" class="w-96 p-4 bg-white rounded shadow">
    <form action="{{ route('admin.customer-response.categories.store') }}" method="POST">
        @csrf
        <h3 class="text-lg font-bold mb-4">إضافة تصنيف</h3>
        <label class="block mb-2">الاسم</label>
        <input type="text" name="name" class="w-full border p-2 mb-3" required>

        <label class="block mb-2">الأيقونة</label>
        <select name="icon" id="addCatIcon" class="w-full border p-2 mb-3" onchange="syncPreview('addCatIcon', 'addIconPreview')">
            @foreach($iconPool as $icon)
                <option value="{{ $icon }}">{{ $icon }}</option>
            @endforeach
        </select>
        <div id="addIconPreview" class="text-2xl mb-3"></div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">حفظ</button>
        <button type="button" onclick="this.closest('dialog').close()" class="ml-2 px-4 py-2 border rounded">إلغاء</button>
    </form>
</dialog>

{{-- مودال تعديل تصنيف --}}
<dialog id="editCategoryModal" class="w-96 p-4 bg-white rounded shadow">
    <form id="editCategoryForm" method="POST">
        @csrf
        @method('PUT')
        <h3 class="text-lg font-bold mb-4">تعديل تصنيف</h3>
        <label class="block mb-2">الاسم</label>
        <input type="text" name="name" id="editCatName" class="w-full border p-2 mb-3" required>

        <label class="block mb-2">الأيقونة</label>
        <select name="icon" id="editCatIcon" class="w-full border p-2 mb-3" onchange="syncPreview('editCatIcon', 'editIconPreview')">
            @foreach($iconPool as $icon)
                <option value="{{ $icon }}">{{ $icon }}</option>
            @endforeach
        </select>
        <div id="editIconPreview" class="text-2xl mb-3"></div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">تحديث</button>
        <button type="button" onclick="this.closest('dialog').close()" class="ml-2 px-4 py-2 border rounded">إلغاء</button>
    </form>
</dialog>

<script>
function toggleCategories(){
    document.getElementById('categoriesPanel').classList.toggle('hidden');
}

    function toggleCategories() {
        let panel = document.getElementById('categoriesPanel');
        let icon = document.getElementById('toggleIcon');

        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            panel.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

function openEditCategory(id, name, icon){
    const form = document.getElementById('editCategoryForm');
    form.action = "{{ route('admin.customer-response.categories.update', ':id') }}".replace(':id', id);
    document.getElementById('editCatName').value = name;
    const sel = document.getElementById('editCatIcon');
    sel.value = icon || 'fas fa-tag';
    syncPreview('editCatIcon', 'editIconPreview');
    document.getElementById('editCategoryModal').showModal();
}
function syncPreview(selectId, previewId){
    const sel = document.getElementById(selectId);
    const cls = sel.value || 'fas fa-tag';
    document.getElementById(previewId).innerHTML = '<i class="'+cls+'"></i>';
}
function copyText(id){
    let text = document.getElementById('text_'+id).innerText;
    // إزالة الفراغات في البداية والنهاية واستبدال عدة أسطر جديدة أو فراغات بواحد فقط
    text = text.trim().replace(/\s*\n\s*/g, '\n').replace(/[ \t]+/g, ' ');
    navigator.clipboard.writeText(text).then(()=>{
        showCopyToast();
    });
}

function showCopyToast(){
    const toast = document.getElementById('copyToast');
    toast.classList.remove('opacity-0');
    toast.classList.add('opacity-100');
    setTimeout(()=>{
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
    }, 2000);
}
document.addEventListener('DOMContentLoaded', function(){
    syncPreview('addCatIcon','addIconPreview');
});
</script>
@endsection
