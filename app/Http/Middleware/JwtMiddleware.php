<?php

namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken();
        
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }
        try {
            $jwt         = JWTAuth::setToken($token);
            $credentials = JWTAuth::getPayload($jwt);
        } catch(TokenExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 401);

            // $jwt         = JWTAuth::setToken(JWTAuth::refresh($token));
            // $credentials = JWTAuth::getPayload($jwt);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
        $user = User::find($credentials['sub']);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}