<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('cannot view tasks belonging to other users', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertForbidden();
});

it('can update their own tasks', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $updateData = [
        'description' => 'Updated description',
        'status' => 'completed',
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'description' => 'Updated description',
            ],
        ]);

    $this->assertDatabaseHas('tasks', $updateData);
});

it('cannot update tasks belonging to other users', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'description' => 'Updated description',
        'status' => TaskStatus::COMPLETED->value,
    ]);

    $response->assertForbidden();
});

it('can delete their own tasks', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'deleted_at' => now(),
    ]);
});

it('cannot delete tasks belonging to other users', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertForbidden();
});
