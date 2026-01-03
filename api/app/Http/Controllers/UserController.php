<?php

namespace App\Http\Controllers;

use App\Http\Services\WechatAuth;
use App\Http\Services\UserService as Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



class UserController
{
    protected Service $Service;

    public function __construct(Service $Service)
    {
        $this->Service = $Service;
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $data = $this->Service->getUser($request);

            return response()->json([
                'code' => 200,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    //签到
    public function sign(Request $request): JsonResponse
    {
        try {


            $data = $this->Service->handleSign($request);

            return response()->json([
                'code' => 200,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
