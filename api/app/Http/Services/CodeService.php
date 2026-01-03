<?php

namespace App\Http\Services;

use App\Models\Code;
use Illuminate\Support\Facades\DB;
use App\Models\User;



class CodeService
{

    //获取用户所有卡密
    public function getCodes($request)
    {
        return Code::query()
            ->where('user_id', $request->auth_user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
    }


    //激活卡密
    public function updateCodeStatus($code, $request)
    {


        $codeModel = Code::query()
            ->where('code', $code)
            ->where('mch_id', $request->mch->id)
            ->where('is_status', 0)
            ->first();

        if (!$codeModel) {
            throw new \Exception('卡密不存在');
        }


        return DB::transaction(function () use ($codeModel, $request) {


            $codeModel->update([
                'is_status' => 1,
                'user_id' => $request->auth_user->id,
                'invoke_at' => now(),
            ]);


            $user = User::where('id', $request->auth_user->id)->firstOrFail();

            //算力订单
            if ($codeModel->code_type == 1) {
                // 假设算力值存储在 value 字段
                return $user->increment('power_value', $codeModel->value);
            }


            //会员订单 增加会员天数
            if ($codeModel->code_type == 2) {
                $vipExpireTime = $user->vip_expire_time;
                $daysToAdd = $codeModel->value; // 假设VIP天数存储在 value 字段

                // 正确的逻辑：如果未过期，在原基础上叠加；如果已过期，从现在开始计算
                if ($vipExpireTime && $vipExpireTime->isFuture()) {
                    $newVipExpireTime = $vipExpireTime->addDays($daysToAdd);
                } else {
                    $newVipExpireTime = now()->addDays($daysToAdd);
                }

                return $user->update(['vip_expire_time' => $newVipExpireTime]);
            }

            throw new \Exception('未知订单类型');
        });



        return $codeModel;
    }
}
