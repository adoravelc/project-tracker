<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->query('search') . '%');
            })
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Projects retrieved successfully.',
            'data' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:active,archived'],
        ]);

        $project = Project::create([
            ...$validated,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    public function show(Project $project)
    {
        $project->load('tasks');

        return response()->json([
            'message' => 'Project retrieved successfully.',
            'data' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:active,archived'],
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => $project,
        ]);
    }
}
