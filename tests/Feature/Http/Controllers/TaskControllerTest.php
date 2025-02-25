<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('authorized request tests', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    });

    /*************************************
     * LIST ENDPOINT TESTS
     ************************************/

    it('can list tasks without deleted ones', function () {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->softDeleted()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    });

    /*************************************
     * SHOW ENDPOINT TESTS
     ************************************/

    it('can show a task', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'user_id' => $this->user->id,
                    'status' => $task->status->value,
                    'description' => $task->description,
                ],
            ]);
    });

    it('cannot show a non-existing task', function () {
        $response = $this->getJson('/api/tasks/999');

        $response->assertNotFound();
    });

    /*************************************
     * CREATE ENDPOINT TESTS
     ************************************/

    it('can create a task', function () {
        $taskData = [
            'status' => TaskStatus::PENDING->value,
            'description' => 'New task description',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'status' => $taskData['status'],
                    'description' => $taskData['description'],
                ],
            ]);

        $this->assertDatabaseHas('tasks', array_merge($taskData, ['user_id' => $this->user->id]));
    });

    it('cannot create a task without a description', function () {
        $response = $this->postJson('/api/tasks', ['status' => TaskStatus::PENDING->value]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    });

    it('cannot create a task with an invalid status', function () {
        $response = $this->postJson('/api/tasks', [
            'status' => 'invalid_status',
            'description' => 'Valid description',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    });

    /*************************************
     * UPDATE ENDPOINT TESTS
     ************************************/

    it('can update a task', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'status' => 'in_progress',
            'description' => 'Updated task description',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => $updateData['status'],
                    'description' => $updateData['description'],
                ],
            ]);

        $this->assertDatabaseHas('tasks', array_merge($updateData, ['id' => $task->id]));
    });

    it('cannot update a task with an empty description', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", ['description' => '']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    });

    it('cannot update a task with an invalid status', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'not_a_valid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    });

    it('fails to update a task with PUT if a required field is missing', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = ['status' => TaskStatus::PENDING->value];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    });

    it('updates a task with PUT when all required fields are provided', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'description' => 'Updated description',
            'status' => TaskStatus::IN_PROGRESS->value,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'description' => 'Updated description',
                    'status' => TaskStatus::IN_PROGRESS->value,
                ],
            ]);

        $this->assertDatabaseHas('tasks', array_merge($updateData, ['id' => $task->id]));
    });

    it('updates a task with PATCH when only some fields are provided', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = ['status' => TaskStatus::COMPLETED->value];

        $response = $this->patchJson("/api/tasks/{$task->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => TaskStatus::COMPLETED->value,
                ],
            ]);

        $this->assertDatabaseHas('tasks', array_merge($updateData, ['id' => $task->id]));
    });

    it('does not remove existing fields when using PATCH with partial data', function () {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'Initial description',
            'status' => TaskStatus::PENDING->value,
        ]);

        $updateData = ['status' => TaskStatus::IN_PROGRESS->value];

        $response = $this->patchJson("/api/tasks/{$task->id}", $updateData);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'status' => TaskStatus::IN_PROGRESS->value,
                    'description' => 'Initial description',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => TaskStatus::IN_PROGRESS->value,
            'description' => 'Initial description',
        ]);
    });

    it('cannot update a non-existing task', function () {
        $response = $this->putJson('/api/tasks/999', [
            'status' => 'completed',
            'description' => 'Updated description',
        ]);

        $response->assertNotFound();
    });

    /*************************************
     * DELETE ENDPOINT TESTS
     ************************************/

    it('can delete a task', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => now(),
        ]);
    });

    it('cannot delete a non-existing task', function () {
        $response = $this->deleteJson('/api/tasks/999');

        $response->assertNotFound();
    });

});

describe('unauthorized request tests', function () {

    beforeEach(function () {
        $user = User::factory()->create();
        $this->task = Task::factory()->create(['user_id' => $user->id]);
    });

    it('fails to list tasks without authentication', function () {
        $response = $this->getJson('/api/tasks');

        $response->assertUnauthorized();
    });

    it('fails to show a task without authentication', function () {
        $response = $this->getJson("/api/tasks/{$this->task->id}");

        $response->assertUnauthorized();
    });

    it('fails to create a task without authentication', function () {
        $taskData = [
            'description' => 'New Task',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertUnauthorized();
    });

    it('fails to update a task with PUT without authentication', function () {
        $updateData = ['description' => 'Updated Task', 'status' => 'completed'];

        $response = $this->putJson("/api/tasks/{$this->task->id}", $updateData);

        $response->assertUnauthorized();
    });

    it('fails to update a task with PATCH without authentication', function () {
        $updateData = ['status' => 'in_progress']; // Partial update

        $response = $this->patchJson("/api/tasks/{$this->task->id}", $updateData);

        $response->assertUnauthorized();
    });

    it('fails to delete a task without authentication', function () {
        $response = $this->deleteJson("/api/tasks/{$this->task->id}");

        $response->assertUnauthorized();
    });

});
