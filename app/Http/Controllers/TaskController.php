<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskStatus;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found', 'tasks' => '[]'], 200);
        }

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            // 'description' => 'required|string',
        ]);
        $defaultStatus = 3; // Assuming 3 is the default status ID
        $request->merge(['status_id' => $defaultStatus]);
        $user_id = auth()->user()->id;
        
        $request->merge(['user_id' => $user_id]);
        $task = Task::create($request->all());

        return \response()->json(['message' => 'Task created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json($task);
    }




    public function status(Request $request, string $id)
    {

        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $getTask = $task->status;
        if (! $getTask) {
            return response()->json(['message' => 'Status not found'], 404);
        }
        // $task->save();
        return response()->json(['message' => $getTask], 200);
    }

    public function updateStatus(Request $request, string $id)
    {
        $task = Task::find($id);
        if (! $task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $status = TaskStatus::find($request->status_id);
        if (! $status) {
            return response()->json(['message' => 'Status not found'], 404);
        }
        $task->status_id = $request->status_id;
        $task->save();
        return response()->json(['message' => 'Task status updated successfully'], 200);
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
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
