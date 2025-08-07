<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;





use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;





/**
 * Class AuthController
 * Handles user authentication including registration, login, token refresh, and logout.
 */



class AuthController extends Controller
{
    protected AuthService $auth;
    protected UserRepository $userRepo;

    public function __construct(AuthService $auth, UserRepository $userRepo)
    {
        $this->auth = $auth;
        $this->userRepo = $userRepo;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->validated());

        return response()->json([
            'user' => $result['user'],
            'token_type' => 'Bearer',
            'expires_in' => $result['expires_in'],
        ], 201)->withHeaders([
            'Authorization' => 'Bearer ' . $result['access_token'],
            'X-Refresh-Token' => $result['refresh_token'],
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->validated());

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user' => $result['user'],
            'token_type' => 'Bearer',
            'expires_in' => $result['expires_in'],
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $result['access_token'],
            'X-Refresh-Token' => $result['refresh_token'],
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->header('X-Refresh-Token');

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token missing'], 400);
        }

        $result = $this->auth->refresh($refreshToken);

        if (!$result) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        return response()->json([
            'token_type' => 'Bearer',
            'expires_in' => $result['expires_in'],
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $result['access_token'],
            'X-Refresh-Token' => $result['refresh_token'],
        ]);
    }

    public function logout(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->auth->logout(Auth::user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(): JsonResponse
    {
        return response()->json(Auth::user());
    }
    public function allUsers(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $users = $this->userRepo->allUsers($request->all());

        return response()->json($users);
    }
}