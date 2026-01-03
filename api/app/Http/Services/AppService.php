<?php

namespace App\Http\Services;

use App\Models\App;
use App\Services\KontApiService;
use App\Models\AppLog;
use Carbon\Carbon;

class AppService
{



    public function apps($request)
    {
        $query = App::query()
            ->with('categorie')
            ->where('is_status', 1)
            ->where('mch_id', $request->mch->id);

        if ($request->is_home) {
            $query->where('is_home', 1);
        }

        if ($request->categorie_id) {
            $query->where('categorie_id', $request->categorie_id);
        }
        if ($request->search_key) {
            $query->where('name', 'like', "%{$request->search_key}%");
        }

        return $query->orderBy('sort', 'desc')
            ->offset(($request->page - 1) * $request->page_size)
            ->limit($request->page_size)
            ->get();
    }

    /**
     * 获取单个应用
     *
     * @param int $id
     * @return App
     */
    public function app(int $id, $request): App
    {
        $with = ['categorie'];
        if ($request->auth_user) {
            $with['agentLog'] = function ($query) use ($request) {
                $query->where('user_id', $request->auth_user->id);
            };
        }
        return App::query()
            ->with($with)
            ->where('id', $id)
            ->where('is_status', 1)
            ->where('mch_id', $request->mch->id)
            ->firstOrFail();
    }

    /**
     * 开始异步工作流
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function workRun($request, $id)
    {
        try {
            if ($request->is_vip && Carbon::parse($request->auth_user->vip_expire_time)->isPast()) {
                return response()->json([
                    'code' => 200,
                    'data' => [
                        'code' => 305,
                        'message' => '此为VIP专属应用，升级会员即可解锁！',
                    ],
                ]);
            }

            $app = App::with('kont')->findOrFail($id);
            if (!$app->kont) {
                throw new \Exception('应用未绑定扣子空间');
            }

            if ($app->power_type == 1) {
                throw new \Exception('扣点配置错误，请联系管理员');
            }

            if ($request->auth_user->power_value < $app->use_power) {
                return response()->json([
                    'code' => 200,
                    'data' => [
                        'code' => 304,
                        'message' => '算力不足啦，请补充后继续创作。',
                    ],
                ]);
            }



            $kontApiService = new KontApiService();
            $kontApiService->app_id = $app->kont->app_id;
            $kontApiService->app_secret = $app->kont->app_secret;
            $kontApiService->app_key = $app->kont->app_key;
            $kontApiService->time = 60;
            $kontApiService->is_log = false;
            $appLog = $kontApiService->workRun($request, $app);
            //扣点算力
            (new PowerDeductionLogService())->createPowerDeductionLog($app->id, $request->auth_user->id, $app->use_power);


            return response()->json([
                'code' => 200,
                'data' => $appLog,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
