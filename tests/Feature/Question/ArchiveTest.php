<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertNotSoftDeleted, assertSoftDeleted, deleteJson};

it('should be able to archive a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user)->create();

    Sanctum::actingAs($user);

    deleteJson(route('questions.archive', $question))
        ->assertNoContent();

    assertSoftDeleted('questions', ['id' => $question->id]);
});

it('should allow that only the creator can archive', function () {
    $userOne  = User::factory()->create();
    $userTwo  = User::factory()->create();
    $question = Question::factory()->for($userOne)->create();

    Sanctum::actingAs($userTwo);

    deleteJson(route('questions.archive', $question))
        ->assertForbidden();

    assertNotSoftDeleted('questions', ['id' => $question->id]);
});
