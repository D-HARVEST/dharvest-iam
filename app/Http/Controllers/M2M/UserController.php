<?php

namespace App\Http\Controllers\M2M;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                'id'               => $user->id,
                'name'             => (string) $user->name,
                'email'            => (string) $user->email,
                'uid'              => (string) $user->uid,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at'       => $user->created_at?->toIso8601String(),
                'updated_at'       => $user->updated_at?->toIso8601String(),
                'fcm_token'        => $user->fcm_token ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Token verification failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
