<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesignerTaskAccount;
use Carbon\Carbon;

class EmployeeDesignerTaskAccountEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth('employee')->user();
        $designerId = $employee->id;

        $now = Carbon::now(config('app.timezone'));
        $today = $now->toDateString();
        $startOfWeek = $now->copy()->startOfWeek()->toDateString();
        $endOfWeek = $now->copy()->endOfWeek()->toDateString();
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        // قراءة تاريخ الفلتر من الطلب
        $filterDate = $request->query('date', $today);

        // جلب المهام مع تصفية حسب التاريخ إذا تم تحديده
        $query = DesignerTaskAccount::with('admin')
            ->where('designer_id', $designerId);

        if ($filterDate) {
            $query->whereDate('task_date', $filterDate);
        }

        // طلب الترتيب وعرض 10 مهمات في الصفحة (pagination)
        $tasks = $query->orderByDesc('task_date')->paginate(10)->withQueryString();

        $stats = [
            'day' => ['count' => 0, 'sum' => 0],
            'week' => ['count' => 0, 'sum' => 0],
            'month' => ['count' => 0, 'sum' => 0],
        ];

        foreach ($tasks as $task) {
            for ($i = 1; $i <= 5; $i++) {
                if ($task["task$i"]) {
                    $taskDate = Carbon::parse($task->task_date, config('app.timezone'))->toDateString();

                    if ($taskDate == $today) {
                        $stats['day']['count']++;
                        $stats['day']['sum'] += $task["price$i"] ?? 0;
                    }
                    if ($taskDate >= $startOfWeek && $taskDate <= $endOfWeek) {
                        $stats['week']['count']++;
                        $stats['week']['sum'] += $task["price$i"] ?? 0;
                    }
                    if ($taskDate >= $startOfMonth && $taskDate <= $endOfMonth) {
                        $stats['month']['count']++;
                        $stats['month']['sum'] += $task["price$i"] ?? 0;
                    }
                }
            }
        }

        return view('employee.designer-task-accounts.index', [
            'tasksToday' => $tasks,
            'stats' => $stats,
            'filterDate' => $filterDate,
        ]);
    }

    public function show($id)
    {
        $employee = auth('employee')->user();
        $task = DesignerTaskAccount::with('admin')
            ->where('designer_id', $employee->id)
            ->findOrFail($id);
        return view('employee.designer-task-accounts.show', compact('task'));
    }
}
