<?php

namespace App\Http\Middleware;


use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;






use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoRefreshToken
{
    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['message' => 'Access token missing'], 401);
        }

        $token = PersonalAccessToken::findToken($bearerToken);

        if (!$token) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $expiration = config('sanctum.expiration');
        if ($expiration && $token->created_at->addMinutes($expiration)->isPast()) {
            $refreshToken = $request->header('X-Refresh-Token');

            if (!$refreshToken) {
                return response()->json(['message' => 'Refresh token missing'], 401);
            }

            $result = $this->auth->refresh($refreshToken);
            if (!$result) {
                return response()->json(['message' => 'Refresh failed'], 401);
            }

            $request->headers->set('Authorization', 'Bearer ' . $result['access_token']);
            Auth::setUser($result['user']);

            $response = $next($request);
            return $response->withHeaders([
                'Authorization' => 'Bearer ' . $result['access_token'],
                'X-Refresh-Token' => $result['refresh_token'],
            ]);
        }

        Auth::setUser($token->tokenable);
        return $next($request);
    }
}
