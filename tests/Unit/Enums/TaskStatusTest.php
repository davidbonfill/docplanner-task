<?php

use App\Enums\TaskStatus;

it('contains the expected enum values', function () {
    expect(TaskStatus::cases())
        ->toHaveCount(3)
        ->and(TaskStatus::PENDING->value)->toBe('pending')
        ->and(TaskStatus::IN_PROGRESS->value)->toBe('in_progress')
        ->and(TaskStatus::COMPLETED->value)->toBe('completed');
});

it('returns an array of values correctly', function () {
    $values = array_column(TaskStatus::cases(), 'value');

    expect($values)->toBe([
        'pending',
        'in_progress',
        'completed',
    ]);
});
