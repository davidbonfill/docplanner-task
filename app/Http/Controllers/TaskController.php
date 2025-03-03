<?php

namespace App\Http\Controllers;

use App\DataTables\TasksDataTable;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * @group Tasks
 */
class TaskController extends Controller
{
    public function __construct(public TaskService $taskService)
    {
        //
    }

    /**
     * GET tasks (Datatable)
     *
     * Display a listing of authenticated user's tasks for datatable serverside option.
     *
     * @authenticated
     */
    public function datatable(Request $request): JsonResponse
    {
        return (new TasksDataTable($request->user()))->ajax();
    }

    /**
     * GET tasks
     *
     * Display a listing of authenticated user's tasks.
     *
     * @authenticated
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TaskResource::collection($this->taskService->getUserTasks($request->user()));
    }

    /**
     * POST task
     *
     * Store a newly created task in storage.
     *
     * @authenticated
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        Gate::authorize('create', Task::class);

        $task = $this->taskService->createTask($request->user(), $request->validated());

        return new TaskResource($task);
    }

    /**
     * GET task
     *
     * Display the specified task.
     *
     * @authenticated
     */
    public function show(Task $task): TaskResource
    {
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * PUT task
     *
     * Update the specified task in storage.
     *
     * @authenticated
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        Gate::authorize('update', $task);

        $this->taskService->updateTask($task, $request->validated());

        return new TaskResource($task);
    }

    /**
     * DELETE task
     *
     * Remove the specified resource from storage.
     *
     * @authenticated
     */
    public function destroy(Task $task): Response
    {
        Gate::authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->noContent();
    }
}
