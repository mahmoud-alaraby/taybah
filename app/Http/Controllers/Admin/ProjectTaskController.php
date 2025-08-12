<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectTask;
use App\Models\Employee;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function update(Request $request, ProjectTask $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_hours' => 'required|numeric|min:0',
            'assigned_to' => 'nullable|exists:employees,id',
            'status' => 'required|in:pending,in_progress,completed,paused',
        ]);

        $updateData = $request->only([
            'name', 'description', 'estimated_hours', 'assigned_to', 'status'
        ]);

        if ($request->status === 'completed' && $task->status !== 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($request->status !== 'completed') {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        return back()->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(ProjectTask $task)
    {
        $projectId = $task->project_id;
        $task->delete();
        
        return redirect()->route('admin.projects.show', $projectId)
                       ->with('success', 'تم حذف المهمة بنجاح');
    }
}