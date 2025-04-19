<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateWithApiToken
{
    public function handle(Request $request, Closure $next)
    {
        Log::debug('API→Web Auth middleware', [
            'bearer' => $request->bearerToken(),
            'query'  => $request->query('token'),
            'session_user' => Auth::id(),
          ]);

        if (Auth::check()) {
            return $next($request);
        }

        $token = $request->bearerToken()
            ?: $request->query('token')
            ?: $request->cookie('api_token');   // <— new

        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                Auth::login($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
