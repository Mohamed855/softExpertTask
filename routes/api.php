<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ListController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::any('logout', [AuthController::class, 'logout']);
    // tasks
    Route::resource('tasks', TaskController::class)->except(['create', 'edit']);
    Route::post('tasks/{task}/assign-dependencies', [TaskController::class, 'assignDependency']);
    Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus']);
    // lists
    Route::get('users', [ListController::class, 'users']);
    Route::get('managers', [ListController::class, 'managers']);
    Route::get('tasks-list', [ListController::class, 'tasks']);
    Route::get('task-statuses', [ListController::class, 'taskStatuses']);
});
