<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class UserTokensValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (empty($request->auth_user)) {
            return response()->json([
                'code' => 401,
                'message' => '请先登录',
                'data' => null,
            ], 401);
        }


        return $next($request);
    }
}
