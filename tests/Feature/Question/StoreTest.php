<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, postJson};

test('should be able to store a new question', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum ?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'question' => 'Lorem ipsum ?',
    ]);
});

test('after creating a new question, I need to make sure that it creates on _draft_ status', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Lorem ipsum ?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'status'   => 'draft',
        'question' => 'Lorem ipsum ?',
    ]);
});