<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireOAuthSessionConfirmation
{
    public function handle(Request $request, Closure $next)
    {
        if (
            $request->routeIs('passport.authorizations.authorize') &&
            auth()->check() &&
            !session()->pull('oauth_session_confirmed')
        ) {
            session(['oauth_confirm_params' => $request->query()]);
            return redirect()->route('oauth.confirm-session');
        }

        return $next($request);
    }
}
