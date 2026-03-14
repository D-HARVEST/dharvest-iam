<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AuthTokenController extends Controller
{
    /**
     * Verify an access token and return user information.
     * This endpoint exposes the gRPC VerifyToken functionality over HTTP/REST.
     */
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'error' => 'No access token provided',
                'message' => 'Unauthenticated',
            ], 401);
        }

        try {
            // Create a mocked request for Passport authentication
            $mockRequest = \Illuminate\Http\Request::create('/', 'GET');
            $mockRequest->headers->set('Authorization', 'Bearer ' . $token);

            $guard = auth('api');
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($mockRequest);
            }

            // Bind the request to the app container
            app()->instance('request', $mockRequest);

            $user = $guard->user();

            if (!$user) {
                return response()->json([
                    'error' => 'Invalid token',
                    'message' => 'Unauthenticated or Invalid Token',
                ], 401);
            }

            // Return user data in the same format as gRPC
            return response()->json([
                'id' => $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'uid' => (string) $user->uid,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
                'updated_at' => $user->updated_at?->toIso8601String(),
                'fcm_token' => $user->fcm_token ?? null,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token verification failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        // Récupérer le token actuel de l'utilisateur et le révoquer
        $token = $user->token();

        if ($token) {
            $token->revoke();
        }

        // Effacer toutes les sessions de ce même utilisateur dans la base de données
        if ($user) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return response()->json(['message' => 'Successfully logged out']);
    }
}
