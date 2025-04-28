<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * Handle user login and return a token.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
{


    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Incorrect email or password. Please try again.',
        ], 401);
    }


    $user = Auth::user();
    $token = $user->createToken('MyAuthApp')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'User logged in successfully.',
        'data' => [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
        ],
        ],
    ],200);
}

    /**
     * Handle user registration.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Validation is now handled by the RegisterRequest class
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token = $user->createToken('MyAuthApp')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
            ],
            ],
        ],200);
    }

    /**
     * Logout the user and revoke their tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        auth('api')->user()->tokens->each(function ($token) {
            $token->delete();
        });
    
        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully.',
            'data' => []
        ], 200);
    }
}
