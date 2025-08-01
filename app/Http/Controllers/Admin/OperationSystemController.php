<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class OperationSystemController extends Controller
{
    /**
     * عرض الصفحة الرئيسية لنظام التشغيل العام
     */
    public function index(Request $request)
    {
        // التاريخ الحالي والشهر المحدد
        $currentDate = Carbon::now();
        $month = $request->get('month', $currentDate->month);
        $year = $request->get('year', $currentDate->year);
        
        // إنشاء تاريخ البداية والنهاية للشهر
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // بناء الاستعلام للمهام
        $query = DB::table('operation_tasks')
            ->join('employees', 'operation_tasks.assigned_person_id', '=', 'employees.id')
            ->select(
                'operation_tasks.*',
                'employees.name as employee_name',
                'employees.department',
                'employees.position',
                'employees.avatar as employee_avatar'
            )
            ->whereBetween('operation_tasks.task_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        // تطبيق الفلاتر
        if ($request->filled('task_type')) {
            $query->where('operation_tasks.task_type', $request->task_type);
        }
        
        if ($request->filled('is_reserved')) {
            $query->where('operation_tasks.is_reserved', $request->is_reserved);
        }
        
        if ($request->filled('status')) {
            $query->where('operation_tasks.status', $request->status);
        }
        
        if ($request->filled('employee_id')) {
            $query->where('operation_tasks.assigned_person_id', $request->employee_id);
        }
        
        if ($request->filled('department')) {
            $query->where('employees.department', $request->department);
        }
        
        // ترتيب النتائج
        $tasks = $query->orderBy('operation_tasks.created_at', 'desc')->get()->groupBy('task_date');
        
        // إنشاء مصفوفة أيام الشهر مع استبعاد الأيام الماضية بدون مهام
        $days = [];
        for ($day = 1; $day <= $startDate->daysInMonth; $day++) {
            $currentDay = $startDate->copy()->day($day);
            $dayTasks = $tasks->get($currentDay->format('Y-m-d'), collect());

            // تجاهل الأيام الماضية بدون مهام
            if ($currentDay->lt(Carbon::today()) && $dayTasks->count() == 0) {
                continue;
            }

            $days[] = [
                'date' => $currentDay->format('Y-m-d'),
                'day' => $day,
                'day_name' => $currentDay->translatedFormat('l'),
                'is_today' => $currentDay->isToday(),
                'is_weekend' => $currentDay->isWeekend(),
                'tasks' => $dayTasks,
                'design_tasks' => $dayTasks->where('task_type', 'design')->count(),
                'marketing_tasks' => $dayTasks->where('task_type', 'marketing')->count(),
                'total_tasks' => $dayTasks->count(),
                'reserved_tasks' => $dayTasks->where('is_reserved', 1)->count()
            ];
        }
        
        // جلب الموظفين النشطين
        $employees = DB::table('employees')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // جلب الأقسام المختلفة
        $departments = DB::table('employees')
            ->where('status', 'active')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();
        
        // إحصائيات الشهر
        $monthStats = [
            'total_tasks' => $tasks->flatten()->count(),
            'design_tasks' => $tasks->flatten()->where('task_type', 'design')->count(),
            'marketing_tasks' => $tasks->flatten()->where('task_type', 'marketing')->count(),
            'reserved_tasks' => $tasks->flatten()->where('is_reserved', 1)->count(),
            'completed_tasks' => $tasks->flatten()->where('status', 'completed')->count(),
            'pending_tasks' => $tasks->flatten()->whereIn('status', ['pending', 'in_progress'])->count(),
        ];
        
        return view('admin.operation-system.index', compact(
            'days',
            'month',
            'year',
            'startDate',
            'employees',
            'departments',
            'monthStats'
        ));
    }


    /**
     * عرض صفحة إضافة مهمة جديدة
     */
    public function create(Request $request)
    {
        $employees = DB::table('employees')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('admin.operation-system.create', compact('employees'));
    }


    /**
     * حفظ مهمة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_date' => 'required|date|after_or_equal:today',
            'task_type' => 'required|in:design,marketing',
            'assigned_person_id' => 'required|exists:employees,id',
            'task_description' => 'nullable|string|max:1000',
            'is_reserved' => 'boolean',
            'notes' => 'nullable|string|max:500'
        ], [
            'task_date.required' => 'تاريخ المهمة مطلوب',
            'task_date.after_or_equal' => 'لا يمكن إضافة مهمة في تاريخ سابق',
            'task_type.required' => 'نوع المهمة مطلوب',
            'task_type.in' => 'نوع المهمة يجب أن يكون تصميم أو تسويق',
            'assigned_person_id.required' => 'يجب اختيار موظف',
            'assigned_person_id.exists' => 'الموظف المحدد غير موجود',
            'task_description.max' => 'وصف المهمة لا يجب أن يتجاوز 1000 حرف',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 500 حرف'
        ]);

        // التحقق من عدم وجود مهمة من نفس النوع لنفس الموظف في نفس اليوم (للسماح بأكثر من مهمة لكن من أنواع مختلفة)
        $existingTask = DB::table('operation_tasks')
            ->where('task_date', $request->task_date)
            ->where('task_type', $request->task_type)
            ->where('assigned_person_id', $request->assigned_person_id)
            ->exists();

        if ($existingTask) {
            return redirect()->back()
                ->withErrors(['assigned_person_id' => 'يوجد بالفعل مهمة من نفس النوع لهذا الموظف في هذا التاريخ'])
                ->withInput();
        }

        try {
            DB::table('operation_tasks')->insert([
                'task_date' => $request->task_date,
                'task_type' => $request->task_type,
                'assigned_person_id' => $request->assigned_person_id,
                'task_description' => $request->task_description,
                'is_reserved' => $request->boolean('is_reserved'),
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => auth('admin')->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.operation-system.index')->with('success', 'تم إضافة المهمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المهمة. حاول مرة أخرى.')
                ->withInput();
        }
    }


    /**
     * عرض تفاصيل مهمة محددة
     */
    public function show($id)
    {
        $task = DB::table('operation_tasks')
            ->join('employees', 'operation_tasks.assigned_person_id', '=', 'employees.id')
            ->select(
                'operation_tasks.*',
                'employees.name as employee_name',
                'employees.department',
                'employees.position',
                'employees.employee_id',
                'employees.avatar as employee_avatar'
            )
            ->where('operation_tasks.id', $id)
            ->first();

        if (!$task) {
            return redirect()->route('admin.operation-system.index')->with('error', 'المهمة غير موجودة');
        }

        return view('admin.operation-system.show', compact('task'));
    }


    /**
     * عرض نموذج تعديل المهمة
     */
    public function edit($id)
    {
        $task = DB::table('operation_tasks')
            ->join('employees', 'operation_tasks.assigned_person_id', '=', 'employees.id')
            ->select(
                'operation_tasks.*',
                'employees.name as employee_name'
            )
            ->where('operation_tasks.id', $id)
            ->first();

        if (!$task) {
            return redirect()->route('admin.operation-system.index')->with('error', 'المهمة غير موجودة');
        }

        $employees = DB::table('employees')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.operation-system.edit', compact('task', 'employees'));
    }


    /**
     * تحديث مهمة موجودة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'task_type' => 'required|in:design,marketing',
            'assigned_person_id' => 'required|exists:employees,id',
            'task_description' => 'nullable|string|max:1000',
            'is_reserved' => 'boolean',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ], [
            'task_type.required' => 'نوع المهمة مطلوب',
            'task_type.in' => 'نوع المهمة يجب أن يكون تصميم أو تسويق',
            'assigned_person_id.required' => 'يجب اختيار موظف',
            'assigned_person_id.exists' => 'الموظف المحدد غير موجود',
            'task_description.max' => 'وصف المهمة لا يجب أن يتجاوز 1000 حرف',
            'status.required' => 'حالة المهمة مطلوبة',
            'status.in' => 'حالة المهمة غير صحيحة',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 500 حرف'
        ]);

        $task = DB::table('operation_tasks')->where('id', $id)->first();

        if (!$task) {
            return redirect()->route('admin.operation-system.index')->with('error', 'المهمة غير موجودة');
        }

        try {
            DB::table('operation_tasks')->where('id', $id)->update([
                'task_type' => $request->task_type,
                'assigned_person_id' => $request->assigned_person_id,
                'task_description' => $request->task_description,
                'is_reserved' => $request->boolean('is_reserved'),
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_at' => now()
            ]);

            return redirect()->route('admin.operation-system.show', $id)->with('success', 'تم تحديث المهمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث المهمة')->withInput();
        }
    }


    /**
     * حذف مهمة
     */
    public function destroy($id)
    {
        $task = DB::table('operation_tasks')->where('id', $id)->first();

        if (!$task) {
            return redirect()->route('admin.operation-system.index')->with('error', 'المهمة غير موجودة');
        }

        try {
            DB::table('operation_tasks')->where('id', $id)->delete();
            return redirect()->route('admin.operation-system.index')->with('success', 'تم حذف المهمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.operation-system.index')->with('error', 'حدث خطأ أثناء حذف المهمة');
        }
    }
}
