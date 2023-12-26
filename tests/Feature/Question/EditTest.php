<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, putJson};

test('should be able to update a question', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    putJson(route('questions.update', $question), [
        'question' => 'Updating Question ?',
    ])->assertOk();

    assertDatabaseHas('questions', [
        'id'       => $question->id,
        'user_id'  => $user->id,
        'question' => 'Updating Question ?',
    ]);
});

describe('Validation rules', function () {
    test('question::required', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => '',
        ])->assertJsonValidationErrors([
            'question' => __('validation.required', ['attribute' => 'question']),
        ]);
    });

    test('question::ending with question mark (?)', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Question without a question mark',
        ])
        ->assertJsonValidationErrors([
            'question' => 'The question should end with question mark (?).',
        ]);
    });

    test('question::min:10', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Any ?',
        ])
        ->assertJsonValidationErrors([
            'question' => __('validation.min.string', ['min' => 10, 'attribute' => 'question']),
        ]);
    });

    test('question::unique', function () {
        $user = User::factory()->create();
        Question::factory()->create([
            'user_id'  => $user->id,
            'question' => 'Any Question ?',
            'status'   => 'draft',
        ]);
        $question = Question::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Any Question ?',
        ])
        ->assertJsonValidationErrors([
            'question' => __('validation.unique', ['attribute' => 'question']),
        ]);
    });

    test('question::unique - should be unique only if id is different', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id'  => $user->id,
            'question' => 'Any Question ?',
            'status'   => 'draft',
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Any Question ?',
        ])
        ->assertOk();
    });

    test('question::should be able to edit question only if the status is in _draft_', function () {
        $user     = User::factory()->create();
        $question = Question::factory()->create([
            'user_id'  => $user->id,
            'question' => 'Any Question ?',
            'status'   => 'published',
        ]);

        Sanctum::actingAs($user);

        putJson(route('questions.update', $question), [
            'question' => 'Updating Question ?',
        ])
        ->assertJsonValidationErrors([
            'question' => 'The question should be a draft to be able to edit',
        ]);

        assertDatabaseHas('questions', [
            'user_id'  => $user->id,
            'question' => 'Any Question ?',
            'status'   => 'published',
        ]);
    });

});

describe('security', function () {
    it('only the person who create the question can update the same question', function () {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $question = Question::factory()->create(['user_id' => $userOne->id]);

        Sanctum::actingAs($userTwo);

        putJson(route('questions.update', $question), [
            'question' => 'Updating Question ?',
        ])->assertForbidden();

        assertDatabaseHas('questions', [
            'id'       => $question->id,
            'question' => $question->question,
        ]);
    });
});
