<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue API bearer token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Issue new Sanctum bearer token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated successfully.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Log out authenticated user by revoking current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = $request->user()->currentAccessToken();
        
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * Get profile of currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($request->user()),
        ], Response::HTTP_OK);
    }
}
