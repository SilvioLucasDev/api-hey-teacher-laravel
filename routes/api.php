<?php

use App\Http\Controllers\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('user', function (Request $request) {
    return $request->user();
});

/** Question */
Route::get('mt-questions/{status}', Question\MyController::class)->name('my-questions');
Route::get('questions', Question\IndexController::class)->name('questions.index');
Route::post('questions', Question\StoreController::class)->name('questions.store');
Route::put('questions/{question}', Question\UpdateController::class)->name('questions.update');
Route::delete('questions/{question}', Question\DeleteController::class)->name('questions.delete');
Route::delete('questions/{question}/archive', Question\ArchiveController::class)->name('questions.archive');
Route::put('questions/{question}/restore', Question\RestoreController::class)->name('questions.restore');
Route::put('questions/{question}/publish', Question\PublishController::class)->name('questions.publish');
