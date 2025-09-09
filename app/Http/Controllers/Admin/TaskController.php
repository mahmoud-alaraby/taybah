<?php
// app/Http/Controllers/Admin/TaskController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
 public function index(Request $request)
{
    $query = DailyTask::query();

    // فلتر الحالة
    if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
        $query->where('status', $request->status);
    }

    // فلترة يوم / شهر / سنة (يُسمح بإرسال أحدها أو جميعها)
    if ($request->filled('date')) {
        $query->whereDate('task_date', $request->date);
    } else {
        if ($request->filled('month')) {
            $query->whereMonth('task_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('task_date', $request->year);
        }
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('details', 'LIKE', "%{$search}%");
        });
    }

    $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.tasks.index', compact('tasks'));
}

// للطباعة (طباعة الحالي أو حسب الفلاتر)
public function print(Request $request)
{
    $query = DailyTask::query();

    if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
        $query->where('status', $request->status);
    }

    if ($request->filled('date')) {
        $query->whereDate('task_date', $request->date);
    } else {
        if ($request->filled('month')) {
            $query->whereMonth('task_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('task_date', $request->year);
        }
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('details', 'LIKE', "%{$search}%");
        });
    }

    $tasks = $query->orderBy('created_at', 'desc')->get();

    // يمكنك استخدام ملف blade مخصوص للطباعة tasks/print.blade.php
    return view('admin.tasks.print', compact('tasks'));
}


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string'
        ]);

        DailyTask::create([
            'title' => $request->title,
            'details' => $request->details,
            'task_date' => Carbon::now()->toDateString(),
            'created_by_admin' => auth('admin')->id(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة المهمة بنجاح');
    }

    public function update(DailyTask $task, Request $request)
    {
        $newStatus = $task->status === 'pending' ? 'completed' : 'pending';

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        return redirect()->back();
    }

    public function destroy(DailyTask $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة');
    }
}
