<?php

namespace App\Http\Controllers;

use App\Http\Services\KontService;
use Illuminate\Http\Request;

class KontController extends Controller
{
    protected $kontService;

    public function __construct(KontService $kontService)
    {
        $this->kontService = $kontService;
    }

    /**
     * 获取所有扣子
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request)
    {
        try {
            return $this->kontService->getToken($request);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
