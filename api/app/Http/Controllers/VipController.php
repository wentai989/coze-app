<?php

namespace App\Http\Controllers;

use App\Http\Services\VipService;
use Illuminate\Http\Request;

class VipController extends Controller
{
    protected $vipService;

    public function __construct(VipService $vipService)
    {
        $this->vipService = $vipService;
    }

    /**
     * 获取所有vip 套餐
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $vips = $this->vipService->vips($request);
            return response()->json([
                'code' => 200,
                'data' => $vips,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function pay(Request $request)
    {
        try {
            $result = $this->vipService->pay($request);
            return response()->json([
                'code' => 200,
                'message' => '正在唤起支付！',
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
