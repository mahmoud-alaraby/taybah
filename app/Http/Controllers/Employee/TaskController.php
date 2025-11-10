<?php
// app/Http/Controllers/Employee/TaskController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyTask::where('created_by_employee', auth('employee')->id());

        // فلتر الحالة
        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $query->where('status', $request->status);
        }

        // فلتر التاريخ الكامل فقط (yyyy-mm-dd)
        if ($request->filled('date')) {
            $query->whereDate('task_date', $request->date);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('employee.tasks.index', compact('tasks'));
    }

    public function print(Request $request)
    {
        $query = DailyTask::where('created_by_employee', auth('employee')->id());

        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('task_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return view('employee.tasks.print', compact('tasks'));
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

    // دالة لعرض بيانات مهمة واحدة
    public function show(DailyTask $task)
    {
        // التحقق من الملكية
        if ($task->created_by_employee !== auth('employee')->id()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول لهذه المهمة'], 403);
        }

        return response()->json($task);
    }

    // دالة التعديل الجديدة
    public function edit(Request $request, DailyTask $task)
    {
        // التحقق من الملكية
        if ($task->created_by_employee !== auth('employee')->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'غير مصرح لك بتعديل هذه المهمة'], 403);
            }
            return redirect()->back()->with('error', 'غير مصرح لك بتعديل هذه المهمة');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'nullable|string'
        ]);

        $task->update([
            'title' => $request->title,
            'details' => $request->details,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث المهمة بنجاح']);
        }

        return redirect()->back()->with('success', 'تم تحديث المهمة بنجاح');
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

    // حذف المهمة مع تحقق الملكية
    public function destroy(DailyTask $task)
    {
        if ($task->created_by_employee !== auth('employee')->id()) {
            return redirect()->back()->with('error', 'غير مسموح لك بحذف هذه المهمة');
        }

        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح');
    }
}