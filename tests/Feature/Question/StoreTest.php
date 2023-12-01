<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{assertDatabaseHas, postJson};

test('should be able to store a new question', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Any Question ?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'question' => 'Any Question ?',
    ]);
});

test('with the creation of the question, we need to make sure that it creates with status _draft_', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson(route('questions.store', [
        'question' => 'Any Question ?',
    ]))->assertSuccessful();

    assertDatabaseHas('questions', [
        'user_id'  => $user->id,
        'status'   => 'draft',
        'question' => 'Any Question ?',
    ]);
});

describe('Validation rules', function () {
    test('question::required', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', []))
            ->assertJsonValidationErrors([
                'question' => __('validation.required', ['attribute' => 'question']),
            ]);
    });

    test('question::ending with question mark (?)', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Question without a question mark',
        ]))
        ->assertJsonValidationErrors([
            'question' => 'The question should end with question mark (?).',
        ]);
    });

    test('question::min:10', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Any ?',
        ]))
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

        Sanctum::actingAs($user);

        postJson(route('questions.store', [
            'question' => 'Any Question ?',
        ]))
        ->assertJsonValidationErrors([
            'question' => __('validation.unique', ['attribute' => 'question']),
        ]);
    });
});

test('after creating we should return a status 201 with the created question', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = postJson(route('questions.store', [
        'question' => 'Any Question ?',
    ]))->assertCreated();

    $question = Question::latest()->first();

    $response->assertJson([
        'data' => [
            'id'         => $question->id,
            'question'   => $question->question,
            'status'     => $question->status,
            'created_by' => [
                'id'   => $user->id,
                'name' => $user->name,
            ],
            'created_at' => $question->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $question->updated_at->format('Y-m-d h:i:s'),
        ],
    ]);

});
