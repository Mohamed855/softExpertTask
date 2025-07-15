<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'assigned_by',
        'due_date',
        'status',
    ];

    protected $casts = [
      'due_date' => 'date',
    ];

    public const STATUSES = [
        0 => 'Pending',
        1 => 'In progress',
        2 => 'Completed',
        3 => 'Cancelled'
    ];

    public function getStatus()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    // relations

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to')->where(['role' => 'user']);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by')->where(['role' => 'manager']);
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_id');
    }

    public function allDependenciesCompleted()
    {
        return $this->dependencies()->where('status', '!=', '2')->count() === 0;
    }
}
