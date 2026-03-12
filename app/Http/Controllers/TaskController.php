<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check policy: user can view their task list.
        $this->authorize('viewAny', Task::class);

        // Return tasks owned by the logged in user.
        $tasks = Task::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // Check policy: user can create a task.
        $this->authorize('create', Task::class);

        // Use validated input from StoreTaskRequest.
        $data = $request->validated();

        // Add user_id to the task data.
        $data['user_id'] = $request->user()->id;

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Policy check: user can view their own task only.
        $this->authorize('view', $task);

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // Policy check: user can update their own task only.
        $this->authorize('update', $task);

        // Use validated input from UpdateTaskRequest.
        $task->update($request->validated());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Policy check: user can delete their own task only.
        $this->authorize('delete', $task);

        $task->delete();

        // 204 success with no response body.
        return response()->noContent();
    }
}
