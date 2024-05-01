<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\{Auth, Validator};

class MyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): ResourceCollection
    {
        $status = $request->route('status');

        Validator::validate(
            ['status' => $status],
            ['status' => ['required', 'in:draft,published,archived']]
        );

        $questions = Question::query()
            ->where('user_id', Auth::id())
            ->when(
                $status == 'archived',
                fn (Builder $query) => $query->onlyTrashed(),
                fn (Builder $query) => $query->where('status', $status)
            )
            ->paginate();

        return QuestionResource::collection($questions);
    }
}
