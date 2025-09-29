<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());
        $token = $user->generateApiToken();

        return $this->authResponse('User registered successfully', $user, $token, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->attemptLogin($request->validated());
        $token = $user->generateApiToken();

        return $this->authResponse('Login successful', $user, $token);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logged out successfully');
    }

    public function profile(): JsonResponse
    {
        return $this->successResponse('Profile retrieved successfully', auth()->user());
    }
}
