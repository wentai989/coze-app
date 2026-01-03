<?php

namespace App\Http\Controllers;

use App\Http\Services\PowerDeductionLogService;
use Illuminate\Http\Request;

class PowerDeductionLogController extends Controller
{
    protected $powerDeductionLogService;

    public function __construct(PowerDeductionLogService $powerDeductionLogService)
    {
        $this->powerDeductionLogService = $powerDeductionLogService;
    }



    /**
     * 创建算力消耗记录
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            $powerDeductionLog = $this->powerDeductionLogService->createPowerDeductionLog($request->id, $request->auth_user->id, $request->token);
            return response()->json([
                'code' => 200,
                'data' => $powerDeductionLog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //获取算力消耗记录
    public function index(Request $request)
    {
        try {
            $powerDeductionLog = $this->powerDeductionLogService->getPowerDeductionLogs($request);
            return response()->json([
                'code' => 200,
                'data' => $powerDeductionLog,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
