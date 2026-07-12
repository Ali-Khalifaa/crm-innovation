<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Helpers\ApiResponse;
use Modules\Tasks\Http\Requests\StoreTaskRequest;
use Modules\Tasks\Http\Requests\UpdateTaskRequest;
use Modules\Tasks\Http\Resources\TaskResource;
use Modules\Tasks\Models\Task;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Task::query();

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($priority = request('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignedTo = request('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        // Filter by related model (contact or deal)
        if (request('taskable_type') && request('taskable_id')) {
            $query->where('taskable_type', request('taskable_type'))
                  ->where('taskable_id', request('taskable_id'));
        }

        $tasks = $query->orderBy('due_date')->paginate(15);

        return ApiResponse::paginated($tasks, TaskResource::class);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return ApiResponse::success(new TaskResource($task), __('crm.created'), 201);
    }

    public function show(Task $task): JsonResponse
    {
        return ApiResponse::success(new TaskResource($task));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return ApiResponse::success(new TaskResource($task));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
