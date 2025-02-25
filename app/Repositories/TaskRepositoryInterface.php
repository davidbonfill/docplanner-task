<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TaskRepositoryInterface
{
    /**
     * @return Collection<Task>
     */
    public function getUserTasks(User $user): Collection;

    public function createTask(User $user, array $data): Task|Model;

    public function updateTask(Task $task, array $data): Task|Model;

    public function deleteTask(Task $task): void;
}
