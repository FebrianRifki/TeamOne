<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::selectRaw("status, COUNT(*) as total")
            ->where('assigned_to', auth()->user()->id)
            ->where('deleted_at', null)
            ->groupBy("status")
            ->pluck("total", "status");
        $task = $tasks['todo'] ?? 0;
        $task_in_progress = $tasks['in_progress'] ?? 0;
        $task_done = $tasks['done'] ?? 0;
        $total_task = $task + $task_in_progress + $task_done;

        $todo_task_percent = $total_task > 0 ? ($task / $total_task) * 100 : 0;
        $in_progress_task_percent = $total_task > 0 ? ($task_in_progress / $total_task) * 100 : 0;
        $done_task_percent = $total_task > 0 ? ($task_done / $total_task) * 100 : 0;

        $project = Project::where("owner_id", auth()->id())->count();

        return view("dashboard.dashboard", compact("task", "task_in_progress", "task_done", "total_task", "project", "todo_task_percent", "in_progress_task_percent", "done_task_percent"));
    }
}
