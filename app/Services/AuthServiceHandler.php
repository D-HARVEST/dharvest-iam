<?php

namespace App\Services;

use App\Grpc\Auth\AuthServiceInterface;
use App\Grpc\Auth\TokenRequest;
use App\Grpc\Auth\UserResponse;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Configuration;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\Exception\InvokeException;

class AuthServiceHandler implements AuthServiceInterface
{
    public function VerifyToken(ContextInterface $ctx, TokenRequest $in): UserResponse
    {
        $accessToken = $in->getAccessToken();

        if (empty($accessToken)) {
            throw new InvokeException('No access token provided', \Spiral\RoadRunner\GRPC\StatusCode::INVALID_ARGUMENT);
        }

        try {
            // Création d'une requête HTTP "mockée" pour Passport
            $request = \Illuminate\Http\Request::create('/', 'GET');
            $request->headers->set('Authorization', 'Bearer ' . $accessToken);

            $guard = auth('api');
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($request);
            }
            // Certains guards nécessitent que 'request' soit binder correctement
            app()->instance('request', $request);

            $user = $guard->user();

            if (!$user) {
                throw new InvokeException('Unauthenticated or Invalid Token', \Spiral\RoadRunner\GRPC\StatusCode::UNAUTHENTICATED);
            }

            $response = new UserResponse();
            $response->setId($user->id);
            $response->setName((string) $user->name);
            $response->setEmail((string) $user->email);
            $response->setUid((string) $user->uid);

            if ($user->email_verified_at) {
                $response->setEmailVerifiedAt($user->email_verified_at->toIso8601String());
            }

            if ($user->created_at) {
                $response->setCreatedAt($user->created_at->toIso8601String());
            }

            if ($user->updated_at) {
                $response->setUpdatedAt($user->updated_at->toIso8601String());
            }

            // check if fcm_token exists on user
            if (isset($user->fcm_token) && $user->fcm_token) {
                $response->setFcmToken($user->fcm_token);
            }

            return $response;

        } catch (\Exception $e) {
            throw new InvokeException($e->getMessage(), \Spiral\RoadRunner\GRPC\StatusCode::INTERNAL);
        }
    }
}
