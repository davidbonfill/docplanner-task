<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;

it('retrieves user tasks', function () {
    $user = new User;
    $expectedCollection = new Collection([new Task, new Task]);

    $mockRepo = mock(TaskRepositoryInterface::class, function (MockInterface $mock) use ($user, $expectedCollection) {
        $mock->shouldReceive('getUserTasks')
            ->once()
            ->with($user)
            ->andReturn($expectedCollection);
    });

    $service = new TaskService($mockRepo);
    $tasks = $service->getUserTasks($user);

    expect($tasks)->toBe($expectedCollection);
});

it('creates a task for a user', function () {
    $user = new User;
    $data = [
        'status' => TaskStatus::PENDING->value,
        'description' => 'Test task',
    ];
    $expectedTask = new Task;
    $expectedTask->status = TaskStatus::PENDING->value;
    $expectedTask->description = 'Test task';

    $mockRepo = mock(TaskRepositoryInterface::class, function (MockInterface $mock) use ($user, $data, $expectedTask) {
        $mock->shouldReceive('createTask')
            ->once()
            ->with($user, $data)
            ->andReturn($expectedTask);
    });

    $service = new TaskService($mockRepo);
    $task = $service->createTask($user, $data);

    expect($task)->toBe($expectedTask)
        ->and($task->status->value)->toEqual(TaskStatus::PENDING->value)
        ->and($task->description)->toEqual('Test task');
});

it('updates a task', function () {
    $task = new Task;
    $task->status = TaskStatus::PENDING->value;
    $task->description = 'Old description';

    $data = [
        'status' => TaskStatus::COMPLETED->value,
        'description' => 'New description',
    ];

    $expectedTask = $task;
    $expectedTask->status = TaskStatus::COMPLETED->value;
    $expectedTask->description = 'New description';

    $mockRepo = mock(TaskRepositoryInterface::class, function (MockInterface $mock) use ($task, $data, $expectedTask) {
        $mock->shouldReceive('updateTask')
            ->once()
            ->with($task, $data)
            ->andReturn($expectedTask);
    });

    $service = new TaskService($mockRepo);
    $updatedTask = $service->updateTask($task, $data);

    expect($updatedTask->status->value)->toEqual(TaskStatus::COMPLETED->value)
        ->and($updatedTask->description)->toEqual('New description');
});

it('deletes a task', function () {
    $task = new Task;

    $mockRepo = mock(TaskRepositoryInterface::class, function (MockInterface $mock) use ($task) {
        $mock->shouldReceive('deleteTask')
            ->once()
            ->with($task)
            ->andReturnNull();
    });

    $service = new TaskService($mockRepo);
    $result = $service->deleteTask($task);

    expect($result)->toBeNull();
});
