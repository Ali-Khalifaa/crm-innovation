<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Helpers\ApiResponse;
use Modules\Core\Services\NotificationService;
use Modules\Tasks\Http\Requests\StoreTaskRequest;
use Modules\Tasks\Http\Requests\UpdateTaskRequest;
use Modules\Tasks\Http\Resources\TaskResource;
use Modules\Tasks\Models\Task;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with('taskable');

        if ($search = request('search')) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        if ($priority = request('priority')) {
            $query->where('priority', $priority);
        }
        if ($assignedTo = request('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }
        if ($from = request('due_from')) {
            $query->whereDate('due_date', '>=', $from);
        }
        if ($to = request('due_to')) {
            $query->whereDate('due_date', '<=', $to);
        }
        if (request()->boolean('overdue')) {
            $query->where('due_date', '<', now()->toDateString())
                  ->where('status', '!=', 'completed');
        }
        if (request('taskable_type') && request('taskable_id')) {
            $query->where('taskable_type', request('taskable_type'))
                  ->where('taskable_id', request('taskable_id'));
        }

        $tasks = $query->orderBy('due_date')->paginate(15);

        return ApiResponse::paginated($tasks, TaskResource::class);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $task = Task::create($request->validated());
        $task->load('taskable');

        $actor = auth('tenant_api')->user();
        if ($task->assigned_to && $task->assigned_to !== $actor->id) {
            NotificationService::notify(
                $actor->tenant_id,
                $task->assigned_to,
                'task_assigned',
                __('crm.notif_task_assigned_title', ['title' => $task->title]),
                __('crm.notif_task_assigned_body', ['name' => $actor->name, 'due' => $task->due_date]),
                ['url' => '/crm/tasks', 'id' => $task->id]
            );
        }

        return ApiResponse::success(new TaskResource($task), __('crm.created'), 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return ApiResponse::success(new TaskResource($task));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());
        $task->load('taskable');

        return ApiResponse::success(new TaskResource($task));
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
