<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Kont;
use App\Services\KontApiService;




class KontService
{

    // 获取扣子临时token
    public function getToken($request)
    {

        try {

            if ($request->is_vip && $request->auth_user->vip_expire_time  < now()) {
                return response()->json([
                    'code' => 200,
                    'data' => [
                        'code' => 305,
                        'message' => '此为VIP专属应用，升级会员即可解锁！',
                    ],
                ]);
            }
            if ($request->auth_user->power_value  <= 0) {
                return response()->json([
                    'code' => 200,
                    'data' => [
                        'code' => 304,
                        'message' => '算力不足啦，请补充后继续创作。',
                    ],
                ]);
            }





            $kontSpace = Kont::findOrFail($request->id);

            $kontApiService = new KontApiService();
            $kontApiService->app_id = $kontSpace->app_id;
            $kontApiService->app_secret = $kontSpace->app_secret;
            $kontApiService->app_key = $kontSpace->app_key;
            // $kontApiService->time = 60;
            // $kontApiService->is_log = false;
            $token = $kontApiService->token();

            return response()->json([
                'code' => 200,
                'data' => $token,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }



    // 获取扣子异步结果

    // public function workHistory(Request $request): JsonResponse
    // {

    //     try {
    //         $kontApiService = new KontApiService();

    //         $kontAgent = KontAgent::whereHas('kontSpace')->findOrFail($request->id);

    //         $kontApiService->app_id = $kontAgent->kontSpace->app_id;
    //         $kontApiService->app_secret = $kontAgent->kontSpace->app_secret;
    //         $kontApiService->app_key = $kontAgent->kontSpace->app_key;




    //         $history = $kontApiService->workHistory($request->workflow_id, $request->execute_id, $request->log_id);
    //         return response()->json([
    //             'code' => 200,
    //             'data' => $history,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
