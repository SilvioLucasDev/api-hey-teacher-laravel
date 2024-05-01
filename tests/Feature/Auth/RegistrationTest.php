<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertAuthenticatedAs, assertDatabaseHas, postJson};
use function PHPUnit\Framework\assertTrue;

test('should be able to register in the application', function () {
    postJson(route('register', [
        'name'               => 'Any User',
        'email'              => 'any@email.com',
        'email_confirmation' => 'any@email.com',
        'password'           => 'password',
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

    test('email', function ($rule, $value, $meta = []) {
        if ($rule == 'unique') {
            User::factory()->create(['email' => $value]);
        }

        postJson(route('register', [
            'email' => $value,
        ]))->assertJsonValidationErrors([
            'email' => __(
                "validation.$rule",
                array_merge(['attribute' => 'email'], $meta)
            ),
        ]);

    })->with([
        'required'  => ['required', ''],
        'min:3'     => ['min', 'AB', ['min' => 3]],
        'max:255'   => ['max', str_repeat('*', 256), ['max' => 255]],
        'email'     => ['email', 'not-email'],
        'unique'    => ['unique', 'any@email.com'],
        'confirmed' => ['confirmed', 'any@email.com'],
    ]);

    test('password', function ($rule, $value, $meta = []) {
        postJson(route('register', [
            'password' => $value,
        ]))->assertJsonValidationErrors([
            'password' => __(
                "validation.$rule",
                array_merge(['attribute' => 'password'], $meta)
            ),
        ]);

    })->with([
        'required' => ['required', ''],
        'min:8'    => ['min', 'AB', ['min' => 8]],
        'max:40'   => ['max', str_repeat('*', 41), ['max' => 40]],
    ]);
});

test('should log the new user in the system', function () {
    postJson(route('register', [
        'name'               => 'Any User',
        'email'              => 'any@email.com',
        'email_confirmation' => 'any@email.com',
        'password'           => 'password',
    ]))->assertSuccessful();

    $user = User::first();
    assertAuthenticatedAs($user);
});
