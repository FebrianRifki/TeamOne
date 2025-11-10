<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;

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
            
            $existingProject = Project::where('project_name', $request->project_name)->where('deleted_at', null)->first();
            if ($existingProject) {
                return redirect()->back()->with('error', 'Project name already exists.');
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

            $project = Project::latest()->first();

            // User Notification
            Notification::create([
                'title' => 'New Project Created',
                'message' => 'A new project has been created',
                'link' => route('projects.index').'/'. $project->id,
                'user_id' => Auth::user()->id,
            ]);

            // Admin Notification 
            Notification::create([
                'title' => 'New Project Created',
                'message' => 'A new project has been created',
                'link' => route('projects.index').'/'. $project->id,
                'user_id' => 1,
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
    public function edit(Project $project)
    {
        try {
            return view('projects.edit', compact('project'));
        } catch (\Throwable $th) {
            return view('projects.edit')->with('error', $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
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

            $project->update([
                'project_name' => $request->project_name,
                'description' => $request->project_description,
            ]);

            return redirect()->back()->with('success', 'Project updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }
}
