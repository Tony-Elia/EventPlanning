<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($request->has('avatar'))
            $this->handleUserAvatar($request);

        // Assign default role (customer) or requested role if valid
        $role = $request->input('role', 'customer');
        $user->assignRole($role);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->createdResponse([
            'user' => $user->toResource(),
            'access_token' => $token
        ], 'User registered successfully');
    }

    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all previous tokens (optional - remove if you want multiple sessions)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user->toResource(),
            'access_token' => $token,
        ], 'Login successful');
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get authenticated user.
     */
    public function getAuthUser(Request $request): JsonResponse
    {
        return $this->successResponse(auth()->user()->toResource());
    }

    public function updateUser(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth()->user();
        if($user->update($request->validated())) {
            if($request->has('avatar'))
                $this->handleUserAvatar($request);
            return $this->successResponse($user->toResource(), 'Profile updated successfully');
        }
        return $this->errorResponse('Unable to update user profile');
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully');
    }

    private function handleUserAvatar(Request $request)
    {
        try {
            $request->user()->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
