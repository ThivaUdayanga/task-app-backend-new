<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $data = $request->validate([
            'title' => 'required',
            'due_date' => 'required|date'
        ]);

        $data['user_id'] = auth()->user()->id;
        if($request->hasFile('file_url')){
            $data['file_url'] = $request->file('file_url')->store("tasks", "public");
        }

        Task::create($data);

        return response()->json([
            "status" => true,
            "message" => "Task created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json([
            "status" => true,
            "massage" => "Task retrieved successfully",
            "task" => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required',
            'due_date' => 'required|date'
        ]);

        if($request->hasFile('file_url')){

            if($task->file_url){
                Storage::disk('public')->delete($task->file_url);

            }

            $data['file_url'] = $request->file('file_url')->store("tasks", "public");
        }

        $task->update($data);

        return response()->json([
            "status" => true,
            "message" => "Task updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            "status"=> true,
            "massage"=> "Task deleted successfully"
        ]);
    }
}
