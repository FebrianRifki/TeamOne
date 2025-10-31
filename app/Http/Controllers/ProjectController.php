<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;

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
        return view('projects.detail', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
