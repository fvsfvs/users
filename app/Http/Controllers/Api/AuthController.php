<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function register(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return ApiResponse::send(
            true,
            (new UserResource($user))->resolve(),
            'User created successfully.',
            201
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Wrong email or password.'],
            ]);
        }

        $existingToken = $user->tokens()->where('name', 'api-token')->first();

        if ($existingToken) {
            $token = $existingToken->plainTextToken ?? $existingToken->id . '|' . $existingToken->token;
        } else {
            $token = $user->createToken('api-token')->plainTextToken;
        }

        return ApiResponse::send(
            true,
            ['token' => $token],
            'You are logged in successfully.',
            200
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
    */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::send(
            true,
            null,
            'You have been logged out.',
            200
        );
    }
}
