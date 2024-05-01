<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $data = request()->validate([
            'name'     => ['required', 'min:3', 'max:255'],
            'email'    => ['required', 'min:3', 'max:255', 'email', 'unique:users,email', 'confirmed'],
            'password' => ['required', 'min:8', 'max:40'],
        ]);
        $user = User::create($data);
        Auth::login($user);

        return response()->make(status: Response::HTTP_CREATED);
    }
}
