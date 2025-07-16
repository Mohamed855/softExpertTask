<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ResponseTrait;

    public function getTasks($user, $filters)
    {
        try {
            $query = $user->isUser() ? $user->tasks() : Task::query();
            $tasks = $this->filteredData($query, $filters);
            $message = $user->isUser() ? 'User tasks' : 'All tasks';
            return $this->success($message, TaskResource::collection($tasks));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getSingleTask(Task $task)
    {
        try {
            $task->load(['assignee', 'assignedBy', 'dependencies']);
            return $this->success('Task (' . $task->title . ')', new TaskResource($task));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function createNewTask($data)
    {
        try {
            $user = Auth::user();
            if (! $user->isManager()) {
                return $this->unauthorized('Manager only can create tasks');
            }
            $task = Task::create(array_merge($data, ['assigned_by' => $user->id]));
            return $this->success('Task has been created', new TaskResource($task));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updateTaskDetails(Task $task, $data)
    {
        try {
            $user = Auth::user();
            if (! $user->isManager()) {
                return $this->unauthorized('Only manager can update task details');
            }
            $task->update($data);
            return $this->success('Task has been updated', new TaskResource($task));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deleteTask(Task $task)
    {
        try {
            $user = Auth::user();
            if (! $user->isManager()) {
                return $this->unauthorized('Only managers can delete task');
            }
            $task->delete();
            return $this->success('Task has been deleted');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function assignTaskDependency(Task $task, $dependencies)
    {
        try {
            $user = Auth::user();
            if (! $user->isManager()) {
                return $this->unauthorized('Only managers can add dependencies for a task');
            }
            $task->dependencies()->sync($dependencies);
            return $this->success('Dependencies has been assigned to a task', new TaskResource($task));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updateTaskStatus(Task $task, $status)
    {
        try {
            $user = Auth::user();
            if ($user->isUser() && $task->assignee !== $user->id) {
                return $this->unauthorized("You can't update the status of a task not assigned to you");
            }
            if (! $task->allDependenciesCompleted() && $status === '2') {
                return $this->error('All dependencies must completed first');
            }
            $task->update(['status' => $status]);
            return $this->success('Task status has been updated', new TaskResource($task));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function filteredData($query, $filters = [])
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        if (! empty($filters['due_date_from']) && ! empty($filters['due_date_to'])) {
            $query->whereBetween('due_date', [$filters['due_date_from'], $filters['due_date_to']]);
        } elseif (! empty($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        } elseif (! empty($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        return $query->with(['assignee', 'assignedBy', 'dependencies'])->paginate(10);
    }
}
