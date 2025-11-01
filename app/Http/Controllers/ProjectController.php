<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\json;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $projects = Project::all();
            return view('dashboard', compact('projects'));
        } catch (\Throwable $th) {
            return view('dashboard')->with('error', $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_name' => 'required|string|max:255',
                'project_description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $name = $request->project_name;
            $initials = collect(explode(' ', $name))
                ->map(fn($word) => strtoupper($word[0]))
                ->join('');
            $projectCode = $initials . '-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

            Project::create([
                'project_name' => $request->project_name,
                'description' => $request->project_description,
                'project_code' => $projectCode,
                'owner_id' => Auth::user()->id,
            ]);

            return redirect()->back()->with('success', 'Project created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['tasks']);
        $users = User::where('role_id', '!=', 1)->get();
        return view('projects.detail', compact('project', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::with(['project', 'user'])->where('id', $id)->first();
        $users = User::where('role_id', '!=', 1)->get();
        return response()->json([$task, $users]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_name' => 'required|string|max:255',
                'description' => 'required|string',
                'assigned_to' => 'required|exists:users,id',
                'priority' => 'required|in:1,2,3',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $task = Task::findOrFail($id);
            $task->task_name = $request->task_name;
            $task->description = $request->description;
            $task->assigned_to = (int) $request->assigned_to;
            $task->priority = $request->priority;
            $task->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully.',
                'task' => $task
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
