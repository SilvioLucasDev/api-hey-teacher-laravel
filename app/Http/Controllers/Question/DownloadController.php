<?php

namespace App\Http\Controllers\Question;

use App\Exports\QuestionsExport;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return (new QuestionsExport())->download('questions.xlsx');
    }
}
