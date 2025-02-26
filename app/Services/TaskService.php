<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaskService
{
    // TODO: use https://github.com/spatie/laravel-data in order to apply Data Transfer Objects (DTO)

    public function __construct(protected TaskRepositoryInterface $taskRepository)
    {
        //
    }

    /**
     * @return Collection<Task>
     */
    public function getUserTasks(User $user): Collection
    {
        return $this->taskRepository->getUserTasks($user);
    }

    public function createTask(User $user, array $data): Model|Task
    {
        return $this->taskRepository->createTask($user, $data);
    }

    public function updateTask(Task $task, array $data): Model|Task
    {
        return $this->taskRepository->updateTask($task, $data);
    }

    public function deleteTask(Task $task): void
    {
        $this->taskRepository->deleteTask($task);
    }
}
