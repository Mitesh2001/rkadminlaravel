<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'status' => 'TOKENEXPIRED',
                    'data' => '',
                    'message' => 'Token is Invalid'
                ]);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                try {
                    $token = $this->auth->parseToken()->refresh();
                } catch (Exception $e){
                    return response()->json([
                        'status' => 'TOKENEXPIRED',
                        'data' => '',
                        'message' => 'Token is Expired'
                    ]);
                }

                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    $user->token = $token;
                    $user->save();
                    return response()->json([
                        'status' => 'UPDATETOKEN',
                        'token' => $token
                    ]);
                }
                return response()->json([
                    'status' => 'TOKENEXPIRED',
                    'data' => '',
                    'message' => 'Token is Expired'
                ]);
            } else {
                return response()->json([
                    'status' => 'FAIL',
                    'data' => '',
                    'message' => 'Authorization Token not found'
                ]);
            }
        }
        return $next($request);
    }
}
