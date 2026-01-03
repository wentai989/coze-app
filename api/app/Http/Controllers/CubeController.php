<?php

namespace App\Http\Controllers;

use App\Services\CubeService;
use Illuminate\Http\Request;

class CubeController extends Controller
{
    protected $cubeService;

    public function __construct(CubeService $cubeService)
    {
        $this->cubeService = $cubeService;
    }

    /**
     * 获取所有魔方
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $cubes = $this->cubeService->cubes();
            return response()->json([
                'code' => 200,
                'data' => $cubes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
