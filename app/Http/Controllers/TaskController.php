<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $key = 'tasks_user_' . Auth::id();
        $tasks = Cache::remember($key, 60, function () {
            return Task::where('user_id', Auth::id())->get();
        });


        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,

            ], 404);
        }

        return TaskResource::collection($tasks)
            ->additional([
                'success' => true,
                'status' => 'success',
                'message' => 'Task retrieved successfully',
                'code' => 200
            ]);
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
        Cache::forget('tasks_user_' . Auth::id());



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
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            ], 404);
        }

        return (new TaskResource($task))
            ->additional([
                'success' => true,
                'status' => 'success',
                'message' => 'Task retrieved successfully',
                'code' => 200
            ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|integer|min:1|max:5',
            'status_id' => 'sometimes|exists:task_statuses,id',
            'completed_at' => 'sometimes|date|nullable',
        ]);

        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            ], 404);
        }

        $task->update($request->only([
            'title',
            'description',
            'priority',
            'status_id',
            'completed_at'
        ]));
        Cache::forget('tasks_user_' . Auth::id());

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
        $task = Task::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Task not found',
                'code'    => 404,
            ], 404);
        }
        $task->delete();
        Cache::forget('tasks_user_' . Auth::id());
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Task deleted successfully',
            'code'    => 200,
        ], 200);
    }
}
