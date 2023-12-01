<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreRequest $request): JsonResource
    {
        $question = Question::create([
            'user_id'  => user()->id,
            'status'   => 'draft',
            'question' => $request->question,
        ]);

        return QuestionResource::make($question);
    }
}
