<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $perPage = $request->get('per_page', 10);
        $users   = $query->paginate($perPage);

        if ($users->isEmpty() && $users->currentPage() > 1) {
            return ApiResponse::send(false, null, 'No users found on this page', 404);
        }

        return ApiResponse::send(
            true,
            [
                'items'      => UserResource::collection($users),
                'pagination' => [
                    'total'         => $users->total(),
                    'per_page'      => $users->perPage(),
                    'current_page'  => $users->currentPage(),
                    'last_page'     => $users->lastPage(),
                ],
            ],
            'Users fetched successfully',
            200
        );
    }

    /**
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return ApiResponse::send(
            true,
            (new UserResource($user))->resolve(),
            'User created successfully',
            201
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return ApiResponse::send(
            true,
            (new UserResource($user))->resolve(),
            'User fetched successfully',
            200
        );
    }

    /**
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {

        $user = User::findOrFail($id);
        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return ApiResponse::send(
            true,
            (new UserResource($user))->resolve(),
            'User updated successfully',
            200
        );
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return ApiResponse::send(
            true,
            null,
            'User deleted successfully',
            200
        );
    }
}
