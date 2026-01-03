<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Leto\MiniProgramAES\WXBizDataCrypt;
use Illuminate\Support\Facades\Log;

class WechatAuth
{
    /**
     * 获取微信手机号 解密
   
     */
    public function getWechatPhone($request)
    {

        //临时解决方案
        $appId = $request->mch->mini['appid'] ?? null;
        $appSecret = $request->mch->mini['appsecret'] ?? null;

        if (!$appId || !$appSecret) {
            throw new \Exception('请先配置小程序appid和appsecret');
        }

        $sessionResult = $this->getWechatSession($request->js_code, [
            'app_id' => $appId,
            'app_secret' => $appSecret
        ]);



        //获取手机号解密
        $pc = new WXBizDataCrypt($appId, $sessionResult['session_key']);

        $errCode = $pc->decryptData($request->encryptedData, $request->iv, $data);

        if ($errCode != 0) {
            Log::error('微信手机号解密失败', [
                'errCode' => $errCode,
                'appId' => $appId,
                'session_key_sample' => substr($sessionResult['session_key'] ?? '', 0, 5) . '...', // 只记一部分防泄露
                'iv' => $request->iv
            ]);
            throw new \Exception('验证失败，请重新点击登录');
        }

        $data = json_decode($data, true);

        if ($data['phoneNumber'] == '') {
            throw new \Exception('验证失败，请重新点击登录');
        }
        return [
            'openid' => $sessionResult['openid'],
            'phone' => $data['phoneNumber'],
            'user_type' => 1,
            //  'ask_id' => $request->ask_id,
        ];
    }

    /**
     * 获取微信用户信息
     * @param string $code 登录凭证
     * @param array $config 小程序配置
     * @return array
     */
    public function getWechatSession(string $code, array $config): array
    {

        $response = Http::get('https://api.weixin.qq.com/sns/jscode2session', [
            'appid' => $config['app_id'],
            'secret' => $config['app_secret'],
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ]);

        $result = $response->json();

        if (isset($result['errcode'])) {
            throw new \Exception($result['errmsg'] ?? '获取身份信息失败,请检查小程序配置');
        }

        return $result;
    }
}
