<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Models\Task;

use App\Models\TaskStatus;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $tasks = Cache::remember('tasks', 60, function () {
            return Task::with('status')->get()->where('user_id', Auth::user()->id);
        });


        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,

            ], 404);
        }

        return  TaskResource::collection(Task::with('status')->get()->where('user_id', Auth::user()->id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);
        $defaultStatus = 3;
        $request->merge(['status_id' => $defaultStatus]);
        $user_id = Auth::user()->id;

        $request->merge(['user_id' => $user_id]);
        Task::create($request->all());


        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Task created successfully',
            'code'    => 201, 
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $task = Task::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('status')
            ->firstOrFail();

        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            
            ], 404);
        }
        return new TaskResource($task);
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
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            ], 404);
        }
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',

        ]);
        $task->update($request->only(['title', 'description', "priority", "status_id"]));
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Task updated successfully',
            'code'    => 200,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            ], 404);
        }
        $task->delete();
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Task deleted successfully',
            'code'    => 200,
        ], 200);
    }
}
