<?php

namespace App\Http\Controllers\M2M;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function getUserByJwt(Request $request): JsonResponse
    {
        $token = $request->input('token');

        if (empty($token)) {
            return response()->json([
                'error' => 'No access token provided',
                'message' => 'Unauthenticated',
            ], 401);
        }

        try {
            $mockRequest = Request::create('/', 'GET');
            $mockRequest->headers->set('Authorization', 'Bearer ' . $token);

            $guard = auth('api');
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($mockRequest);
            }

            app()->instance('request', $mockRequest);

            $user = $guard->user();

            if (!$user) {
                return response()->json([
                    'error' => 'Invalid token',
                    'message' => 'Unauthenticated or Invalid Token',
                ], 401);
            }

            return response()->json([
                'id' => $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'uid' => (string) $user->uid,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
                'updated_at' => $user->updated_at?->toIso8601String(),
                'fcm_token' => $user->fcm_token ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token verification failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function generatePassword(User $user): void
    {
        $user->tokens()->delete();
        $plainPassword = str()->random(8);
        $user->password = Hash::make($plainPassword);
        $user->notify(new \App\Notifications\NewMemberCredentials($user, $plainPassword));
        $user->notify(new \App\Notifications\PasswordChangeNotification);
        $user->save();

    }
    public function regeneratePassword(string $uid): JsonResponse
    {
        $user = User::where('uid', $uid)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
                'message' => "No user with uid: {$uid}",
            ], 404);
        }

        $this->generatePassword($user);

        return response()->json([
            'message' => 'Password regenerated successfully',
            'uid' => $uid,

        ]);
    }
    public function addNewUser(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                "uid" => ['required', 'string', 'max:255', 'unique:users,uid'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'fcm_token' => ['nullable', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        //existing user with same uid or email should not be created, but we can update the existing one if needed
        $existing = (User::where('uid', $validated['uid'])->orWhere('email', $validated['email'])->first());
        if ($existing) {
            $user = $existing;
            return (new UserResource($user))
                ->additional([
                    'message' => 'User with same uid or email already exists, returning existing user',
                ])
                ->response()
                ->setStatusCode(201);
        }
        $plainPassword = str()->random(12);

        $user = User::create([
            'uid' => $validated['uid'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'fcm_token' => $validated['fcm_token'] ?? null,
        ]);
        $this->generatePassword($user);

        return (new UserResource($user))

            ->response()
            ->setStatusCode(201);
    }

    public function updateUser(Request $request, string $uid): JsonResponse|UserResource
    {
        $user = User::where('uid', $uid)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
                'message' => "No user with uid: {$uid}",
            ], 404);
        }

        try {
            $validated = $request->validate([
                'fcm_token' => ['sometimes', 'nullable', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (empty($validated)) {
            return response()->json([
                'error' => 'No fields to update',
                'message' => 'Provide at least one of: name, email, password, fcm_token',
            ], 422);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return new UserResource($user);
    }

    public function deleteUser(string $uid): JsonResponse
    {
        $user = User::where('uid', $uid)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found',
                'message' => "No user with uid: {$uid}",
            ], 404);
        }

        // Révoquer tous les tokens Passport avant suppression
        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'uid' => $uid,
        ]);
    }
}
