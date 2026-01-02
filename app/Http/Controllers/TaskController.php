<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->user()->id;

        $tasks = Task::where('user_id', $user_id)->get();

        return response()->json([
            "status" => true,
            "tasks" => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:ongoing,completed',
            'file_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Add user_id
        $data['user_id'] = auth()->user()->id;

        // Check if file is uploaded and store it
        if ($request->hasFile('file_url')) {
            $data['file_url'] = $request->file('file_url')->store('tasks', 'public');
        }

        // Create the task
        $task = Task::create($data);

        return response()->json([
            "status" => true,
            "message" => "Task created successfully",
            "task" => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Check if user owns this task
        if ($task->user_id !== auth()->user()->id) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized access"
            ], 403);
        }

        return response()->json([
            "status" => true,
            "message" => "Task retrieved successfully",
            "task" => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Check if user owns this task
        if ($task->user_id !== auth()->user()->id) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized access"
            ], 403);
        }

        // Validate request data
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:ongoing,completed',
            'file_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('file_url')) {
            // Delete old file if exists
            if ($task->file_url && Storage::disk('public')->exists($task->file_url)) {
                Storage::disk('public')->delete($task->file_url);
            }

            // Store new file
            $data['file_url'] = $request->file('file_url')->store('tasks', 'public');
        }

        // Update the task
        $task->update($data);

        return response()->json([
            "status" => true,
            "message" => "Task updated successfully",
            "task" => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Check if user owns this task
        if ($task->user_id !== auth()->user()->id) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized access"
            ], 403);
        }

        // Delete associated file if exists
        if ($task->file_url && Storage::disk('public')->exists($task->file_url)) {
            Storage::disk('public')->delete($task->file_url);
        }

        // Delete the task
        $task->delete();

        return response()->json([
            "status" => true,
            "message" => "Task deleted successfully"
        ]);
    }
}