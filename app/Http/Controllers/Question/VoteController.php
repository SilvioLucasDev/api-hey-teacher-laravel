<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Question $question, string $vote): Response
    {
        Validator::validate(
            ['vote' => $vote],
            ['vote' => ['required', 'in:like,unlike']]
        );

        $dynamicVote = $vote == 'like' ? 'unlike' : 'like';

        $question->votes()->updateOrCreate(
            ['user_id' => user()->id],
            [
                $vote        => 1,
                $dynamicVote => 0,
            ]
        );

        return response()->noContent();
    }
}
