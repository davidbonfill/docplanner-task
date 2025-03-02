<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    /**
     * @return Collection<Task>
     */
    public function getUserTasks(User $user): Collection;

    public function createTask(User $user, array $data): Task;

    public function updateTask(Task $task, array $data): Task;

    public function deleteTask(Task $task): void;
}
