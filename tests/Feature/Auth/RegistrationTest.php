<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

test('should be able to register in the application', function () {
    postJson(route('register', [
        'name'     => 'Any User',
        'email'    => 'any@email.com',
        'password' => 'password',
    ]))->assertSuccessful();

    assertDatabaseHas('users', [
        'name'  => 'Any User',
        'email' => 'any@email.com',
    ]);

    $userCreated = User::whereEmail('any@email.com')->first();

    assertTrue(Hash::check('password', $userCreated->password));
});

describe('Validation rules', function () {

    test('name', function ($rule, $value, $meta = []) {
        postJson(route('register', [
            'name' => $value,
        ]))->assertJsonValidationErrors([
            'name' => __(
                "validation.$rule",
                array_merge(['attribute' => 'name'], $meta)
            ),
        ]);

    })->with([
        'required' => ['required', ''],
        'min:3'    => ['min', 'AB', ['min' => 3]],
        'max:255'  => ['max', str_repeat('*', 256), ['max' => 255]],
    ]);
});

// test('after creating we should return a status 201 with the created question', function () {
//     $user = User::factory()->create();

//     Sanctum::actingAs($user);

//     $response = postJson(route('questions.store', [
//         'question' => 'Any Question ?',
//     ]))->assertCreated();

//     $question = Question::latest()->first();

//     $response->assertJson([
//         'data' => [
//             'id'         => $question->id,
//             'question'   => $question->question,
//             'status'     => $question->status,
//             'created_by' => [
//                 'id'   => $user->id,
//                 'name' => $user->name,
//             ],
//             'created_at' => $question->created_at->format('Y-m-d h:i:s'),
//             'updated_at' => $question->updated_at->format('Y-m-d h:i:s'),
//         ],
//     ]);
// });
