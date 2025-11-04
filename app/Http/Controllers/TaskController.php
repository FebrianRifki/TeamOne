<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_name' => 'required|string|max:255',
                'description' => 'required|string',
                'assigned_to' => 'required|exists:users,id',
                'priority' => 'required|in:1,2,3',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $task = Task::create([
                'task_name' => $request->task_name,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'priority' => $request->priority,
                'project_id' => $request->project_id,
            ]);

            $task->save();

            return redirect()->back()->with('success', 'Task created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully.',
                'task' => $task
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }
}
