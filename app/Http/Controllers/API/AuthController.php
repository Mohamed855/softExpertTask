<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseTrait;

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();

            // delete old tokens & regenerate another
            $user->tokens()->delete();
            $token = $user->createToken('api-token', [$user->role])->plainTextToken;

            return $this->success('Welcome back ' . $user->name, [
                'token' => $token,
                'user'  => new UserResource($user),
            ]);
        }

        return $this->unauthorized('Invalid credentials');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success('Logged out successfully');
    }

    public function profile()
    {
        $user = Auth::user();
        return $this->success('Hello ' . $user->name, new UserResource($user));
    }
}
