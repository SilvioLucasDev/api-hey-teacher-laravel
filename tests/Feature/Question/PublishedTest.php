<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

test('should be able to publish a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->for($user)->create(['status' => 'draft']);

    Sanctum::actingAs($user);

    putJson(route('questions.publish', $question))
        ->assertNoContent();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'published']);
});

test('should allow that only the creator can publish', function () {
    $userOne  = User::factory()->create();
    $userTwo  = User::factory()->create();
    $question = Question::factory()->for($userOne)->create();

    Sanctum::actingAs($userTwo);

    putJson(route('questions.publish', $question))
        ->assertForbidden();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'draft']);
});

test('should only publish when the question is on status draft', function () {
    $userOne  = User::factory()->create();
    $userTwo  = User::factory()->create();
    $question = Question::factory()->for($userOne)->create(['status' => 'published']);

    Sanctum::actingAs($userTwo);

    putJson(route('questions.publish', $question))
        ->assertNotFound();

    assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'published']);
});
