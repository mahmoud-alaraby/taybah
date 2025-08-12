<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Employee;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['tasks', 'timeTracking']);

        // البحث
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%');
            });
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(10);

        // إحصائيات
        $stats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'active')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'total_hours' => Project::join('time_tracking', 'projects.id', '=', 'time_tracking.project_id')
                          ->sum('time_tracking.total_seconds') / 3600,
        ];

        return view('admin.projects.index', compact('projects', 'stats'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        return view('admin.projects.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,completed,on_hold,cancelled',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'client_name' => $request->client_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'created_by' => auth('admin')->id(),
            'created_by_type' => 'admin',
        ]);

        return redirect()->route('admin.projects.show', $project)
                       ->with('success', 'تم إنشاء المشروع بنجاح');
    }

    public function show(Project $project)
    {
        $project->load(['tasks.assignedEmployee', 'timeTracking.employee']);
        
        // إحصائيات المشروع
        $projectStats = [
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('status', 'completed')->count(),
            'in_progress_tasks' => $project->tasks()->where('status', 'in_progress')->count(),
            'total_hours' => $project->total_hours,
            'estimated_hours' => $project->tasks()->sum('estimated_hours'),
        ];

        return view('admin.projects.show', compact('project', 'projectStats'));
    }

    public function edit(Project $project)
    {
        $employees = Employee::active()->get();
        return view('admin.projects.edit', compact('project', 'employees'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,completed,on_hold,cancelled',
        ]);

        $project->update($request->only([
            'name', 'description', 'client_name', 'start_date', 'end_date', 'status'
        ]));

        return redirect()->route('admin.projects.show', $project)
                       ->with('success', 'تم تحديث المشروع بنجاح');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')
                       ->with('success', 'تم حذف المشروع بنجاح');
    }

    public function addTask(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'required|numeric|min:0',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        $task = $project->tasks()->create([
            'name' => $request->name,
            'description' => $request->description,
            'estimated_hours' => $request->estimated_hours,
            'assigned_to' => $request->assigned_to,
            'created_by' => auth('admin')->id(),
            'created_by_type' => 'admin',
        ]);

        return back()->with('success', 'تم إضافة المهمة بنجاح');
    }

    public function reports(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        // تقارير المشاريع
        $projectReports = Project::with(['tasks', 'timeTracking'])
            ->whereHas('timeTracking', function($q) use ($year, $month) {
                $q->whereYear('date', $year)->whereMonth('date', $month);
            })
            ->get()
            ->map(function($project) use ($year, $month) {
                $monthlyHours = $project->timeTracking()
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->sum('total_seconds') / 3600;

                return [
                    'project' => $project,
                    'monthly_hours' => $monthlyHours,
                    'total_hours' => $project->total_hours,
                    'completion_percentage' => $project->completion_percentage,
                ];
            });

        return view('admin.projects.reports', compact('projectReports', 'year', 'month'));
    }
}