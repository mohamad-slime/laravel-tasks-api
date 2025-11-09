<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');
        $priority = $request->input('priority');

        $key = 'tasks_user_' . Auth::id() . "_page_{$request->page}_status_{$status}_priority_{$priority}";

        $tasks = Cache::remember($key, 60, function () use ($status, $priority, $perPage) {
            $query = Task::where('user_id', Auth::id());

            if ($status) {
                $query->where('status', $status);
            }

            if ($priority) {
                $query->where('priority', $priority);
            }

            return $query->latest()->paginate($perPage);
        });

        return TaskResource::collection($tasks)
            ->additional([
                'success' => true,
                'status' => 'success',
            'message' => 'Tasks retrieved successfully',
                'code' => 200
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'due_date' => 'nullable|date|after:now',
            ]);

            $task = Task::create([
                ...$validated,
                'user_id' => Auth::id()
            ]);

            if ($validated['status'] === 'completed') {
                $task->markAsCompleted();
            }

            Cache::forget('tasks_user_' . Auth::id());

            return (new TaskResource($task))
                ->additional([
                    'success' => true,
                'status' => 'success',
                'message' => 'Task created successfully',
                    'code' => 201
                ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        };
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
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'due_date' => 'sometimes|nullable|date|after:now',
        ]);

        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $task->markAsCompleted();
            unset($validated['status']); // Remove status from validated data as it's already handled
        }

        $task->update($validated);
        Cache::forget('tasks_user_' . Auth::id());

        return (new TaskResource($task))
            ->additional([
                'success' => true,
            'status' => 'success',
            'message' => 'Task updated successfully',
                'code' => 200
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        $task->delete();
        Cache::forget('tasks_user_' . Auth::id());

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Task deleted successfully',
            'code' => 200
        ]);
    }

    /**
     * Get overdue tasks.
     */
    public function overdue()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->overdue()
            ->get();

        return TaskResource::collection($tasks)
            ->additional([
                'success' => true,
                'status' => 'success',
                'message' => 'Overdue tasks retrieved successfully',
                'code' => 200
            ]);
    }

    /**
     * Get upcoming tasks.
     */
    public function upcoming()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->upcoming()
            ->get();

        return TaskResource::collection($tasks)
            ->additional([
                'success' => true,
                'status' => 'success',
                'message' => 'Upcoming tasks retrieved successfully',
                'code' => 200
            ]);
    }
}
