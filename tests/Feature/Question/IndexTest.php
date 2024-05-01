<?php

use App\Models\{Question, User};
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

it('should be able to list only published questions', function () {
    $user              = User::factory()->create();
    $questionPublished = Question::factory()->for($user)->published()->create();
    $questionDraft     = Question::factory()->for($user)->draft()->create();

    Sanctum::actingAs($user);

    $response = getJson(route('questions.index'))
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
        'id'       => $questionDraft->id,
        'question' => $questionDraft->question,
    ]);
});
