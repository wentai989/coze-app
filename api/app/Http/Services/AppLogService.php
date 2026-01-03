<?php

namespace App\Http\Services;

use App\Models\AppLog;
use Illuminate\Http\Request;
use App\Models\Kont;
use App\Services\KontApiService;


class AppLogService
{
    /**
     * 获取应用日志
     *
     * @param int $id
     * @return array
     */
    public function updateAppLog(int $id, $request)
    {
        return AppLog::updateOrCreate(
            [
                'app_id'       => $id,
                'user_id'   => $request->auth_user->id,
                'log_type'  => $request->log_type,
                'mch_id'    => $request->mch->id,
            ],
            [
                'contents' => $request->messages,
            ]
        );
    }

    /**
     * 获取应用日志-异步工作流输出
     *
     * @param int $id
     * @return array
     */
    public function getWorkOupts(int $id, $request)
    {
        $appLog = AppLog::with('app.kont')->findOrFail($id);
        //请求结果
        if ($appLog->is_status == 0) {
            $kontApiService = new KontApiService();
            $kontApiService->app_id = $appLog->app->kont->app_id;
            $kontApiService->app_secret = $appLog->app->kont->app_secret;
            $kontApiService->app_key = $appLog->app->kont->app_key;
            $kontApiService->time = 60;
            $kontApiService->is_log = false;
            $appLog =  $kontApiService->asyncWorkOutput($appLog->app->bot_id, $appLog->execute_id, $appLog);
        }
        return [
            'is_status' => $appLog->is_status,
            'outputs' => $appLog->outputs,
            'output_params' => $appLog->app->output_params,
        ];
    }
    /**
     * 获取应用日志列表
     *
     * @param Request $request
     * @return array
     */
    public function getAppLogs(Request $request)
    {
        return AppLog::with('app')
            ->where('user_id', $request->auth_user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
