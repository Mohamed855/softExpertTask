<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date->format('D, d-m-Y'),
            'status' => Task::STATUSES[(int) $this->status],
            'assignee' => new UserResource($this->assignee),
            'assigned_by' => new UserResource($this->assignedBy),
            'dependencies' => self::collection($this->dependencies),
            'created_at' => $this->created_at->format('d-m-Y h:i A'),
        ];
    }
}
