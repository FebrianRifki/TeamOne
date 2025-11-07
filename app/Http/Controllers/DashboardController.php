<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
       $tasks= Task::selectRaw("status, COUNT(*) as total")
       ->where("assigned_to", auth()->id())
       ->groupBy("status")
       ->pluck("total", "status");
       $task = $tasks['todo'] ?? 0;
       $task_in_progress = $tasks['in_progress'] ?? 0;
       $task_done = $tasks['done'] ?? 0;

       $project = Project::where("owner_id", auth()->id())->count();

       return view("dashboard.dashboard", compact("task", "task_in_progress", "task_done", "project"));
    }
}
