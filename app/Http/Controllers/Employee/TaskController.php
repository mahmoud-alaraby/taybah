<?php
// app/Http/Controllers/Employee/TaskController.php



namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->toDateString();
        $tasks = DailyTask::whereDate('task_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.tasks.index', compact('tasks'));
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
            'created_by_employee' => auth('employee')->id(),
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

    // إضافة دالة الحذف للموظف
    public function destroy(DailyTask $task)
    {
        // التأكد من أن الموظف يمكنه حذف المهمة (المهام التي أنشأها فقط)
        if ($task->created_by_employee !== auth('employee')->id()) {
            return redirect()->back()->with('error', 'غير مسموح لك بحذف هذه المهمة');
        }

        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح');
    }
}
