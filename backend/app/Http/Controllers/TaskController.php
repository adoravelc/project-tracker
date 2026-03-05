<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->query('search') . '%');
            })
            ->when($request->filled('project_id'), function ($query) use ($request) {
                $query->where('project_id', $request->query('project_id'));
            })
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data' => $tasks,
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
            'project_id' => ['required', 'exists:projects,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date'],
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => $user->id,
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task)
    {
        return response()->json([
            'message' => 'Task retrieved successfully.',
            'data' => $task,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'required', 'exists:projects,id'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'due_date' => ['sometimes', 'required', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => $task,
        ]);
    }

    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $task->deleted_by = $user->id;
        $task->save();
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }
}
