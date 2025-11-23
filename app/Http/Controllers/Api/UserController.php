<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userService->getUsersList($request);

            return UserResource::collection($users)->additional(['success' => true, 'message' => 'Users retrieved']);
        } catch (\Exception $e) {
            Log::error('Internal server error while get the list of users', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $userData = $request->validated();

            $user = $this->userService->store($userData);

            return response()->json([
                'success' => true,
                'message' => 'User created',
                'data' => new UserResource($user)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Internal server error while storing user', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userService->getUserById($id);

            return response()->json([
                'success' => true,
                'message' => 'User retrieved',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while get user by id', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            $userData = $request->validated();

            $user = $this->userService->update($id, $userData);

            return response()->json([
                'success' => true,
                'message' => 'User updated',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while update user', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->userService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted',
                'data' => []
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            Log::error('Internal server error while delete user', ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }
}
