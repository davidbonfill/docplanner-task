<?php

use App\Enums\TaskStatus;
use App\Models\Task;

it('has correct fillable fields defined', function () {
    $task = new Task;
    expect($task->getFillable())->toEqual([
        'user_id',
        'description',
        'status',
    ]);
});

it('applies cast to the status attribute', function () {
    $task = new Task;
    $task->setRawAttributes(['status' => TaskStatus::PENDING->value], true);

    $status = $task->status;
    expect($status)->toBeInstanceOf(TaskStatus::class);
});
