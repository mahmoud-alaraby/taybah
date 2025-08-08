<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignerTaskAccount;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DesignerTaskAccountAdminController extends Controller
{
    public function index(Request $request)
    {
        $designer_id = $request->input('designer_id');

        $designers = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'designers_account');
        })->active()->get();

        $accountsQuery = DesignerTaskAccount::with('designer')
            ->when($designer_id, fn($q) => $q->where('designer_id', $designer_id))
            ->orderByDesc('task_date');

        $accountsPaginated = $accountsQuery->paginate(10)->withQueryString();

        $tasksGroupedByDate = [];

        foreach ($accountsPaginated as $account) {
            $date = $account->task_date;
            $tasksGroupedByDate[$date][] = $account;
        }

        $allAccountsQuery = DesignerTaskAccount::query()
            ->when($designer_id, fn($q) => $q->where('designer_id', $designer_id));

        $allAccounts = $allAccountsQuery->get();

        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $stats = ['day' => ['count' => 0, 'sum' => 0], 'week' => ['count' => 0, 'sum' => 0], 'month' => ['count' => 0, 'sum' => 0]];

        foreach ($allAccounts as $acc) {
            for ($i = 1; $i <= 5; $i++) {
                if ($acc["task$i"]) {
                    $taskDate = Carbon::parse($acc->task_date)->toDateString();
                    if ($taskDate == $today) {
                        $stats['day']['count']++;
                        $stats['day']['sum'] += $acc["price$i"] ?? 0;
                    }
                    if ($taskDate >= $startOfWeek && $taskDate <= $endOfWeek) {
                        $stats['week']['count']++;
                        $stats['week']['sum'] += $acc["price$i"] ?? 0;
                    }
                    if ($taskDate >= $startOfMonth && $taskDate <= $endOfMonth) {
                        $stats['month']['count']++;
                        $stats['month']['sum'] += $acc["price$i"] ?? 0;
                    }
                }
            }
        }

        $designerStats = null;
        if ($designer_id) {
            $designerStats = ['day' => ['count' => 0, 'sum' => 0], 'week' => ['count' => 0, 'sum' => 0], 'month' => ['count' => 0, 'sum' => 0]];
            foreach ($allAccounts as $acc) {
                for ($i = 1; $i <= 5; $i++) {
                    if ($acc["task$i"]) {
                        $taskDate = Carbon::parse($acc->task_date)->toDateString();

                        if ($taskDate == $today) {
                            $designerStats['day']['count']++;
                            $designerStats['day']['sum'] += $acc["price$i"] ?? 0;
                        }
                        if ($taskDate >= $startOfWeek && $taskDate <= $endOfWeek) {
                            $designerStats['week']['count']++;
                            $designerStats['week']['sum'] += $acc["price$i"] ?? 0;
                        }
                        if ($taskDate >= $startOfMonth && $taskDate <= $endOfMonth) {
                            $designerStats['month']['count']++;
                            $designerStats['month']['sum'] += $acc["price$i"] ?? 0;
                        }
                    }
                }
            }
        }

        return view('admin.designer-task-accounts.index', compact(
            'designers', 'designer_id', 'accountsPaginated', 'tasksGroupedByDate', 'stats', 'designerStats'
        ));
    }

    public function create(Request $request)
    {
        $designers = Employee::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'designers_account');
        })->active()->get();

        $date = $request->input('date', now()->toDateString());

        $totalTasksCount = DesignerTaskAccount::where('task_date', $date)->count();

        return view('admin.designer-task-accounts.create', compact('designers', 'date', 'totalTasksCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'designer_id' => 'required|exists:employees,id',
            'task_date' => 'required|date',
            'tasks' => 'required|array|min:1|max:5',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.price' => 'required|numeric|min:0',
        ]);

        $existingTaskCount = DesignerTaskAccount::where('task_date', $request->task_date)->count();
        $newTasksCount = count($request->tasks);

        if ($existingTaskCount + $newTasksCount > 5) {
            return redirect()->back()->withInput()->withErrors("لقد قمت بإضافة {$existingTaskCount} مهام خلال هذا اليوم والحد الأقصى هو 5 مهام فقط.");
        }

        $data = [
            'designer_id' => $request->designer_id,
            'task_date' => $request->task_date,
            'created_by_admin' => auth('admin')->id(),
        ];

        for ($i = 1; $i <= 5; $i++) {
            $data["task$i"] = $request->tasks[$i - 1]['title'] ?? null;
            $data["price$i"] = $request->tasks[$i - 1]['price'] ?? null;
            $data["desc$i"] = $request->tasks[$i - 1]['description'] ?? null;
        }

        DesignerTaskAccount::create($data);

        return redirect()->route('admin.designer-task-accounts.index', ['date' => $request->task_date])
            ->with('success', 'تم حفظ التاسكات بنجاح');
    }

   public function edit($id)
{
    $account = DesignerTaskAccount::findOrFail($id);

    $designers = Employee::whereHas('roles.permissions', function ($q) {
        $q->where('name', 'designers_account');
    })->active()->get();

    $tasks = [];
    for ($i = 1; $i <= 5; $i++) {
        if ($account["task$i"]) {
            $tasks[] = [
                'title' => $account["task$i"],
                'description' => $account["desc$i"] ?? '',
                'price' => $account["price$i"]
            ];
        }
    }

    return view('admin.designer-task-accounts.edit', compact('account', 'designers', 'tasks'));
}


    public function update(Request $request, $id)
    {
        $account = DesignerTaskAccount::findOrFail($id);

        $request->validate([
            'designer_id' => 'required|exists:employees,id',
            'task_date' => 'required|date',
            'tasks' => 'required|array|min:1|max:5',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.price' => 'required|numeric|min:0',
        ]);

        $data = [
            'designer_id' => $request->designer_id,
            'task_date' => $request->task_date,
        ];

        for ($i = 1; $i <= 5; $i++) {
            $data["task$i"] = $request->tasks[$i - 1]['title'] ?? null;
            $data["price$i"] = $request->tasks[$i - 1]['price'] ?? null;
            $data["desc$i"] = $request->tasks[$i - 1]['description'] ?? null;
        }

        $account->update($data);

        return redirect()->route('admin.designer-task-accounts.index', ['date' => $request->task_date])
            ->with('success', 'تم تعديل المهام بنجاح');
    }

    public function show($id)
    {
        $account = DesignerTaskAccount::with('designer')->findOrFail($id);
        return view('admin.designer-task-accounts.show', compact('account'));
    }

    public function destroy($id)
    {
        DesignerTaskAccount::findOrFail($id)->delete();
        return redirect()->route('admin.designer-task-accounts.index')->with('success', 'تم الحذف بنجاح');
    }

    public function destroyAllTasksForDay(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return redirect()->back()->withErrors('يجب تحديد التاريخ.');
        }

        DesignerTaskAccount::where('task_date', $date)->delete();

        return redirect()->route('admin.designer-task-accounts.index', ['date' => $date])
            ->with('success', 'تم حذف جميع التاسكات لهذا اليوم.');
    }
}
