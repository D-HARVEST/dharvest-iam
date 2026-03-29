<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->routeIs('passport.authorizations.authorize')) {
                // Sauvegarder les paramètres OAuth en session (sans `screen` qui est interne à l'IAM)
                $oauthParams = collect($request->query())->except('screen')->all();
                session(['oauth_params' => $oauthParams]);

                $screen = $request->query('screen', 'login');

                return match ($screen) {
                    'register' => route('register'),
                    'forgot-password', 'forgot' => route('password.request'),
                    'google' => route('login.google', $oauthParams),
                    default => route('login'),
                };
            }

            return $request->expectsJson() ? null : route('login');
        });

        $middleware->web(append: [
            \App\Http\Middleware\RequireOAuthSessionConfirmation::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            "client.credentials" => \Laravel\Passport\Http\Middleware\CheckToken::class,
        ]);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
