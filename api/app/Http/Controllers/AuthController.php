<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;





use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;





class AuthController extends Controller
{
    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $token = $this->auth->register($request->validated());
            return response()->json([
                'token' => $token,
                'user' => Auth::user()
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Registration failed.'], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->auth->login($request->validated());
            if (!$token) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'token' => $token,
                'user' => Auth::user()
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Login failed.'], 500);
        }
    }

    public function user(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function logout(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}