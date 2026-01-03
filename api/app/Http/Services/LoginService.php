<?php

namespace App\http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LoginService
{


    //短信发码

    /**
     * 发送验证码
     * @param string $mobile 手机号
     * @return array
     */
    public function smsCode($request)
    {
        try {
            // 生成6位随机验证码
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // 缓存验证码，设置60秒有效期
            Cache::put('sms_code_' . $request->phone, $code, 120);

            // 短信内容
            $content = sprintf(
                '【%s】您的验证码是%s。如非本人操作，请忽略本短信，120秒内有效!',
                $request->app->app_config->sms_sign,
                $code
            );

            // 构建请求参数
            $params = [
                'u' => $request->app->app_config->sms_account,
                'p' => $request->app->app_config->sms_api_key,
                'm' => $request->phone,
                'c' => $content
            ];

            // 发送HTTP请求，设置30秒超时
            $response = Http::timeout(30)
                ->get('https://api.smsbao.com/sms', $params)
                ->throw()
                ->body();

            // 短信宝状态码说明
            $statusStr = [
                '0' => '发送成功',
                '-1' => '参数不全',
                '-2' => '服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间',
                '30' => '密码错误',
                '40' => '账号不存在',
                '41' => '余额不足',
                '42' => '帐户已过期',
                '43' => 'IP地址限制',
                '50' => '内容含有敏感词'
            ];

            // 处理响应
            if ($response === '0') {
                return ['code' => 200, 'msg' => '验证码发送成功'];
            } else {
                throw new \Exception('验证码发送失败：' . ($statusStr[$response] ?? '未知错误'));
            }
        } catch (\Exception $e) {
            throw new \Exception('验证码发送异常：' . $e->getMessage());
        }
    }



    //统一登录
    public function login($request, $data)
    {

        $user = User::where('phone', $data['phone'])
            ->where('mch_id', $request->mch->id)
            ->first();

        if ($user) {
            $token = $user->createToken('remember_token')->plainTextToken;
            if (isset($data['openid'])) {
                $user->openid = $data['openid'];
                $user->save();
            }
            return [
                'token' => $token,
                'user_id' => $user->id,
            ];
        }
        try {
            DB::beginTransaction();

            $userModel = new User();




            if (empty($request->mch->power)) {
                throw new \Exception('请先完成系统配置');
            }




            if ($request->ask_id) {
                $askUser = User::find($request->ask_id);

                if ($askUser) {


                    if ($request->mch->power['ask_value'] > 0)
                        $askUser->power_value += $request->mch->power['add_value'];
                    //邀请数量
                    $askUser->invite_count += 1;
                    $askUser->save();
                    $userModel->ask_id = $askUser->id;
                }
            }


            //注册送算力

            if ($request->mch->power['add_value'] > 0)
                $addValue  =   $userModel->power_value = $request->mch->power['add_value'];







            $userModel->openid = isset($data['openid']) ? $data['openid'] : '';
            $userModel->name = '--';
            $userModel->phone = $data['phone'];
            $userModel->mch_id = $request->mch->id;
            $userModel->save();
            if (!$userModel) {
                throw new \Exception('登录失败1');
            }
            DB::commit();
            $token = $userModel->createToken('remember_token')->plainTextToken;

            return [
                'token' => $token,
                'user_id' => $userModel->id,
                'add_value' => $addValue ?? 0,
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception('登录失败' . $th->getMessage());
        }
    }
}
