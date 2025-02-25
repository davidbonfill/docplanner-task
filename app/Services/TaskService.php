<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaskService
{
    // TODO: use https://github.com/spatie/laravel-data in order to apply Data Transfer Objects (DTO)
    // Other improvements: use of repositories

    /**
     * @return Collection<Task>
     */
    public function getUserTasks(User $user): Collection
    {
        return $user->tasks;
    }

    public function createTask(User $user, array $data): Task|Model
    {
        return $user->tasks()->create($data);
    }

    public function updateTask(Task $task, array $data): Task|Model
    {
        $task->update($data);

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }
}
