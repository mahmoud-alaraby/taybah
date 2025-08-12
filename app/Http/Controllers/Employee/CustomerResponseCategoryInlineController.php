<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerResponseCategory;
use Illuminate\Support\Facades\DB;

class CustomerResponseCategoryInlineController extends Controller
{
    protected array $iconPool = [
        'fas fa-id-card', 'fas fa-camera-retro', 'fas fa-laptop-code', 'fas fa-paint-brush',
        'fas fa-bullhorn', 'fas fa-print', 'fas fa-comments', 'fas fa-question-circle',
        'fas fa-headset', 'fas fa-envelope-open-text', 'fas fa-lightbulb', 'fas fa-file-alt',
        'fas fa-clipboard-list', 'fas fa-chart-line', 'fas fa-check-circle', 'fas fa-star',
        'fas fa-tag', 'fas fa-sitemap', 'fas fa-tools', 'fas fa-bolt',
    ];

    public function icons()
    {
        return response()->json($this->iconPool);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:customer_response_categories,name',
            'icon' => 'nullable|string|max:100',
        ]);

        $icon = $request->icon ?: 'fas fa-tag';

        CustomerResponseCategory::insert([
            'name'       => $request->name,
            'icon'       => substr($icon, 0, 100),
            'created_at' => now(),
        ]);

        return back()->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function update(Request $request, CustomerResponseCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:customer_response_categories,name,' . $category->id,
            'icon' => 'nullable|string|max:100',
        ]);

        $icon = $request->icon ?: $category->icon ?: 'fas fa-tag';

        DB::table('customer_response_categories')
            ->where('id', $category->id)
            ->update([
                'name' => $request->name,
                'icon' => substr($icon, 0, 100),
            ]);

        return back()->with('success', 'تم تعديل التصنيف بنجاح');
    }

    public function destroy(CustomerResponseCategory $category)
    {
        $category->delete();
        return back()->with('success', 'تم حذف التصنيف بنجاح');
    }
}
