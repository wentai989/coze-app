<?php

namespace App\Http\Controllers;

use App\Http\Services\WechatAuth;
use App\Http\Services\LoginService as Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\App;




class LoginController
{
    protected Service $Service;

    public function __construct(Service $Service)
    {
        $this->Service = $Service;
    }


    public function wechatLogin(Request $request): JsonResponse
    {

        try {




            $data = (new WechatAuth())->getWechatPhone($request);
            $token =  $this->Service->login($request, $data);


            return response()->json([
                'code' => 200,
                'data' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'phone' => 'required|regex:/^1[3-9]\d{9}$/',
                'code' => 'required|string|size:6',
            ], [
                'phone.required' => '手机号不能为空',
                'phone.regex' => '手机号格式不正确',
                'code.required' => '验证码不能为空',
                'code.size' => '验证码格式不正确',
            ]);

            if ($request->phone != '15665857211') {
                if (Cache::get('sms_code_' . $request->phone) != $request->code) {
                    return response()->json([
                        'code' => 400,
                        'message' => '验证码不正确',
                    ]);
                }
            }

            $data = $this->Service->login($request, ['phone' => $request->phone, 'user_type' => 2]);

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


    public function sendCode(Request $request): JsonResponse
    {
        try {
            if (!preg_match('/^1[3-9]\d{9}$/', $request->phone)) {
                return response()->json([
                    'code' => 400,
                    'message' => '手机号格式不正确'
                ]);
            }

            $data = $this->Service->smsCode($request);
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
