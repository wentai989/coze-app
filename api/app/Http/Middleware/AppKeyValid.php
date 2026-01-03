<?php

namespace App\Http\Middleware;

use App\Http\Services\CommonService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\App;
use App\Models\Mch;
use Illuminate\Support\Facades\Auth;

class AppKeyValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $appType = $request->header('App-Type');
        $appKey = $request->header('App-Id');
        if (!$appType || !$appKey) {
            return response()->json([
                'code' => 500,
                'message' => '配置异常'
            ], 500);
        }


        $mch = Mch::where('app_key', $appKey)->first();
        if (!$mch) {
            return response()->json([
                'code' => 500,
                'message' => '配置异常',
            ], 500);
        }


        if ($mch->is_status == 0) {
            return response()->json([
                'code' => 500,
                'message' => '已禁用 请联系客服',
            ], 500);
        }

        if (date('Y-m-d') > $mch->service_at) {
            return response()->json([
                'code' => 500,
                'message' => '服务已到期 请联系客服',
            ], 500);
        }

        $request->merge([
            'mch' => $mch,
            'app_type' => $appType,
        ]);


        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $request->merge([
                'auth_user' => $user,
                'user_id' => $user->id,
            ]);
        }
        return $next($request);
    }
}
