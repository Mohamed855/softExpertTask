<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ResponseTrait;

    public function getTasks($user)
    {
        $tasks = $user->isUser() ? $user->tasks()->get() : Task::get();
        $message = $user->isUser() ? 'User tasks' : 'All tasks';
        $tasks->load(['assignee', 'assignedBy', 'dependencies']);
        return $this->success($message, TaskResource::collection($tasks));
    }

    public function getSingleTask(Task $task)
    {
        $task->load(['assignee', 'assignedBy', 'dependencies']);
        return $this->success('Task (' . $task->title . ')', new TaskResource($task));
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
        $user = Auth::user();
        if ($user->isUser() && $task->assignee !== $user->id) {
            return $this->unauthorized("You can't update the status of a task not assigned to you");
        }
        if (! $task->allDependenciesCompleted() && $status === '2') {
            return $this->error('All dependencies must completed first');
        }
        $task->update(['status' => $status]);
        return $this->success('Task status has been updated', new TaskResource($task));
    }
}
