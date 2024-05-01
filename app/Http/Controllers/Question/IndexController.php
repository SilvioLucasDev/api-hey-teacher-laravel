<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): ResourceCollection
    {
        $search = $request->query('search');

        $questions = Question::query()
            ->published()
            ->search($search)
            ->withSum('votes', 'like')
            ->withSum('votes', 'unlike')
            ->paginate();

        return QuestionResource::collection($questions);
    }
}
