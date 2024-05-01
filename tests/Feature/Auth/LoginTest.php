<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{assertAuthenticatedAs, postJson};

it('should be able to login', function () {
    $user = User::factory()->create(['email' => 'any@email.com', 'password' => Hash::make('password')]);

    postJson(route('login', [
        'email'    => 'any@email.com',
        'password' => 'password',
    ]))->assertNoContent();

    assertAuthenticatedAs($user);
});

it('should check if the email and password is valid', function ($email, $password) {
    User::factory()->create(['email' => 'any@email.com', 'password' => Hash::make('password')]);

    postJson(route('login', [
        'email'    => $email,
        'password' => $password,
    ]))->assertJsonValidationErrors([
        'email' => __("auth.failed"),
    ]);

})->with([
    'wrong email'    => ['wrong-email', 'password'],
    'wrong password' => ['any@email.com', 'wrong-password'],
    'invalid email'  => ['not-email', 'password'],
]);

it('should check if the email and password is not empty', function () {
    User::factory()->create(['email' => 'any@email.com', 'password' => Hash::make('password')]);

    postJson(route('login', [
        'email'    => '',
        'password' => '',
    ]))->assertJsonValidationErrors([
        'email'    => __("validation.required", ['attribute' => 'email']),
        'password' => __("validation.required", ['attribute' => 'password']),
    ]);
});

it('should log the new user in the system', function () {
    $user = User::factory()->create(['email' => 'any@email.com', 'password' => Hash::make('password')]);

    postJson(route('login', [
        'email'    => 'any@email.com',
        'password' => 'password',
    ]))->assertNoContent();

    assertAuthenticatedAs($user);
});
