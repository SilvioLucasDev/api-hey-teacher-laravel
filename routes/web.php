<?php

use App\Http\Controllers\{Auth};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', Auth\RegisterController::class)->name('register');
Route::post('login', Auth\LoginController::class)->name('login');

Route::post('logout', Auth\LogoutController::class)->middleware('auth')->name('logout');
