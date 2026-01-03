<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Http\Services\AppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppController extends Controller
{
    protected $appService;

    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }

    /**
     * 获取所有应用
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->appService->apps($request);

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

    /**
     * 获取单个应用
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $result = $this->appService->app($id, $request);
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

    // 开始异步工作流
    public function work(Request $request, int $id): JsonResponse
    {

        try {
            return $this->appService->workRun($request, $id);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
