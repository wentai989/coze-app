<?php

namespace App\Http\Controllers;

use App\Models\AppLog;
use App\Http\Services\AppLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppLogController extends Controller
{
    protected $appLogService;

    public function __construct(AppLogService $appLogService)
    {
        $this->appLogService = $appLogService;
    }



    /**
     * 保存应用日志
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(int $id, Request $request): JsonResponse
    {
        try {
            $result = $this->appLogService->updateAppLog($id, $request);
            return response()->json([
                'code' => 200,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function work(int $id, Request $request): JsonResponse
    {
        try {
            $result = $this->appLogService->getWorkOupts($id, $request);
            return response()->json([
                'code' => 200,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // /**
    //  * 获取应用日志
    //  *
    //  * @param int $id
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function show(int $id, Request $request): JsonResponse
    // {
    //     try {
    //         $result = $this->appLogService->getAppLog($id, $request);
    //         return response()->json([
    //             'code' => 200,
    //             'data' => $result,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    /**
     * 获取应用日志列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->appLogService->getAppLogs($request);
            return response()->json([
                'code' => 200,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
