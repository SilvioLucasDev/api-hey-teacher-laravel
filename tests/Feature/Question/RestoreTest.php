<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertNotSoftDeleted, assertSoftDeleted, putJson};

it('should be able to restore a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user)->create();

    $question->delete();
    assertSoftDeleted('questions', ['id' => $question->id]);

    Sanctum::actingAs($user);

    putJson(route('questions.restore', $question))
        ->assertNoContent();

    assertNotSoftDeleted('questions', ['id' => $question->id]);
});

it('should allow that only the creator can restore', function () {
    $userOne  = User::factory()->create();
    $userTwo  = User::factory()->create();
    $question = Question::factory()->for($userOne)->create();
    $question->delete();

    Sanctum::actingAs($userTwo);

    putJson(route('questions.restore', $question))
        ->assertForbidden();

    assertSoftDeleted('questions', ['id' => $question->id]);
});

it('should only restore when the question is deleted', function () {
    $userOne  = User::factory()->create();
    $userTwo  = User::factory()->create();
    $question = Question::factory()->for($userOne)->create();

    Sanctum::actingAs($userTwo);

    putJson(route('questions.restore', $question))
        ->assertNotFound();

    assertNotSoftDeleted('questions', ['id' => $question->id]);
});
