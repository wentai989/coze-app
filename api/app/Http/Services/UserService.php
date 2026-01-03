<?php

namespace App\Http\Services;

use App\Models\Vip;

use App\Models\VipPay;
use App\Models\WechatApp;
use Illuminate\Support\Str;
use App\Models\UserExtend;
use App\Models\KontAgent;
use App\Http\Models\User as HttpUser;
use App\Models\User;


class UserService
{
    public function getUser($request)
    {
        $user = HttpUser::find($request->auth_user->id);
        return $user;
    }

    //签到
    public function handleSign($request)
    {
        $sign_power_value = $request->mch->power['sign_value'] ?? 0;

        $user = User::findOrFail($request->auth_user->id);


        if ($user->sign_at == date('Y-m-d')) {
            throw new \Exception('您今日已签到！');
        }
        $user->sign_at = date('Y-m-d');
        $user->power_value += $sign_power_value;
        $user->save();

        return [
            'title' => '恭喜您',
            'tag' => '签到奖励',
            'description' => '赠送' . $sign_power_value . '算力',
        ];
    }
}
