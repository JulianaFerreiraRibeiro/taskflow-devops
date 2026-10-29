<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => [
                'sometimes',
                Rule::in(['pendente', 'em andamento', 'concluída']),
            ],
        ]);

        $tasks = Task::query()
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->status)
            )
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => [
                'sometimes',
                Rule::in(['pendente', 'em andamento', 'concluída']),
            ],
        ]);

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => [
                'sometimes',
                Rule::in(['pendente', 'em andamento', 'concluída']),
            ],
        ]);

        $task->update($data);

        return response()->json($task->fresh());
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
