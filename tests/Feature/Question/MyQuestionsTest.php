<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

test('should list only questions that the logged user has ben created :: published', function () {
    $user              = User::factory()->create();
    $questionPublished = Question::factory()->for($user)->published()->create();
    $questionDraft     = Question::factory()->for($user)->draft()->create();
    $questionArchive   = Question::factory()->for($user)->archive()->create();

    $anotherUser              = User::factory()->create();
    $anotherQuestionPublished = Question::factory()->for($anotherUser)->published()->create();

    Sanctum::actingAs($user);

    $response = getJson(route('my-questions', ['status' => 'published']))
        ->assertOk();

    $response->assertJsonFragment([
        'id'         => $questionPublished->id,
        'question'   => $questionPublished->question,
        'status'     => $questionPublished->status,
        'created_by' => [
            'id'   => $user->id,
            'name' => $user->name,
        ],
        'created_at' => $questionPublished->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $questionPublished->updated_at->format('Y-m-d h:i:s'),
    ])->assertJsonMissing([
        'question' => $questionDraft->question,
    ])->assertJsonMissing([
        'question' => $questionArchive->question,
    ])->assertJsonMissing([
        'question' => $anotherQuestionPublished->question,
    ]);
});

test('should list only questions that the logged user has ben created :: draft', function () {
    $user              = User::factory()->create();
    $questionPublished = Question::factory()->for($user)->published()->create();
    $questionDraft     = Question::factory()->for($user)->draft()->create();
    $questionArchive   = Question::factory()->for($user)->archive()->create();

    $anotherUser          = User::factory()->create();
    $anotherQuestionDraft = Question::factory()->for($anotherUser)->draft()->create();

    Sanctum::actingAs($user);

    $response = getJson(route('my-questions', ['status' => 'draft']))
        ->assertOk();

    $response->assertJsonFragment([
        'id'         => $questionDraft->id,
        'question'   => $questionDraft->question,
        'status'     => $questionDraft->status,
        'created_by' => [
            'id'   => $user->id,
            'name' => $user->name,
        ],
        'created_at' => $questionDraft->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $questionDraft->updated_at->format('Y-m-d h:i:s'),
    ])->assertJsonMissing([
        'question' => $questionPublished->question,
    ])->assertJsonMissing([
        'question' => $questionArchive->question,
    ])->assertJsonMissing([
        'question' => $anotherQuestionDraft->question,
    ]);
});

test('should list only questions that the logged user has ben created :: archive', function () {
    $user              = User::factory()->create();
    $questionPublished = Question::factory()->for($user)->published()->create();
    $questionDraft     = Question::factory()->for($user)->draft()->create();
    $questionArchive   = Question::factory()->for($user)->archive()->create();

    $anotherUser          = User::factory()->create();
    $anotherQuestionDraft = Question::factory()->for($anotherUser)->draft()->create();

    Sanctum::actingAs($user);

    $response = getJson(route('my-questions', ['status' => 'archived']))
        ->assertOk();

    $response->assertJsonFragment([
        'id'         => $questionArchive->id,
        'question'   => $questionArchive->question,
        'status'     => $questionArchive->status,
        'created_by' => [
            'id'   => $user->id,
            'name' => $user->name,
        ],
        'created_at' => $questionArchive->created_at->format('Y-m-d h:i:s'),
        'updated_at' => $questionArchive->updated_at->format('Y-m-d h:i:s'),
    ])->assertJsonMissing([
        'question' => $questionPublished->question,
    ])->assertJsonMissing([
        'question' => $questionDraft->question,
    ])->assertJsonMissing([
        'question' => $anotherQuestionDraft->question,
    ]);
});

test('making sure that only draft, published, and archived statuses can be passed to the route', function (string $status, int $code) {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    getJson(route('my-questions', ['status' => $status]))
    ->assertStatus($code);

})->with([
    'draft'     => ['draft', 200],
    'published' => ['published', 200],
    'archived'  => ['archived', 200],
    'thing'     => ['thing', 422],
]);
