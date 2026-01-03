<?php

namespace App\Http\Controllers;

use App\Http\Services\CodeService;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    protected CodeService $CodeService;

    public function __construct(CodeService $CodeService)
    {
        $this->CodeService = $CodeService;
    }

    //获取用户所有卡密
    public function history(Request $request)
    {
        return response()->json([
            'code' => 200,
            'data' => $this->CodeService->getCodes($request),
        ]);
    }



    //激活卡密
    public function activate(Request $request, $code)
    {
        try {
            $result = $this->CodeService->updateCodeStatus($code, $request);
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
