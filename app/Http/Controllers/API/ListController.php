<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $method = $request->route()->getActionMethod();
            if (! $user->isManager() && in_array($method, ['users', 'managers', 'tasks'])) {
                return $this->unauthorized('Only managers allowed to access');
            }
            return $next($request);
        });
    }

    public function users()
    {
        $users = User::where('role', 'user')->get();
        return $this->success('Users list', UserResource::collection($users));
    }

    public function managers()
    {
        $managers = User::where('role', 'manager')->get();
        return $this->success('Managers list', UserResource::collection($managers));
    }

    public function tasks()
    {
        $tasks = Task::select('id', 'title')->get();
        return $this->success('Tasks list', $tasks);
    }

    public function taskStatuses()
    {
        return $this->success('Task statuses list', Task::STATUSES);
    }
}
