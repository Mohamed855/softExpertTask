<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ResponseTrait;

    public function getUserTasks($user)
    {
        $userTasks = $user->isUser() ? $user->tasks()->get() : $user->createdTasks()->get();
        $message = $user->isUser() ? 'User tasks' : 'Manager created tasks';
        $userTasks->load(['assignee', 'assignedBy', 'dependencies']);
        return $this->success($message, TaskResource::collection($userTasks));
    }

    public function getSingleTask(Task $task)
    {
        $task->load(['assignee', 'assignedBy', 'dependencies']);
        return $this->success('Task (' . $task->name . ')', new TaskResource($task));
    }

    public function createNewTask($data)
    {
        $user = Auth::user();
        if (! $user->isManager()) {
            return $this->unauthorized('Manager only can create tasks');
        }
        $task = Task::create(array_merge($data, ['assigned_by' => $user->id]));
        return $this->success('Task has been created', new TaskResource($task));
    }

    public function updateTaskDetails(Task $task, $data)
    {
        $user = Auth::user();
        if (! $user->isManager()) {
            return $this->unauthorized('Manager only can update task details');
        }
        $task->update($data);
        return $this->success('Task has been updated', new TaskResource($task));
    }

    public function deleteTask(Task $task)
    {
        $user = Auth::user();
        if (! $user->isManager()) {
            return $this->unauthorized('Manager only can delete task');
        }
        $task->delete();
        return $this->success('Task has been deleted');
    }

    public function updateTaskStatus(Task $task, $status)
    {
        if (! $task->allDependenciesCompleted() && $status === '2') {
            return $this->error('All dependencies must completed first', $task->dependencies());
        }
        $task->update(['status' => $status]);
        return $this->success('Task status has been updated', new TaskResource($task));
    }
}
