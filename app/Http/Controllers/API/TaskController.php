<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->taskService->getTasks(Auth::user(), $request->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        return $this->taskService->createNewTask($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->taskService->getSingleTask($task);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        return $this->taskService->updateTaskDetails($task, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        return $this->taskService->deleteTask($task);
    }

    /**
     * Add dependencies for specified resource in storage.
     */
    public function assignDependency(Request $request, Task $task)
    {
        $request->validate([
            'dependencies' => 'required|array',
            'dependencies.*' => 'required|exists:tasks,id|not_in:' . $task->id,
        ]);

        return $this->taskService->assignTaskDependency($task, $request->dependencies);
    }

    /**
     * Update the specified resource status in storage.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
        ]);

        return $this->taskService->updateTaskStatus($task, $request->status);
    }
}
