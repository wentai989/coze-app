<?php

namespace App\Http\Services;

use App\Models\Code;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Spread;
use App\Models\Order;
use App\Models\Mch;



class SpreadService
{


    //推广员数据
    public function getSpread($request)
    {
        $user = User::where('id', $request->auth_user->id)->firstOrFail();

        //推广总金额
        $totalSpread = Spread::where('user_id', $user->id)
            ->where('spread_type', 1)
            ->sum('amount');
        //近30天推广金额
        $spread30 = Spread::where('user_id', $user->id)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->where('spread_type', 1)
            ->sum('amount');
        //邀请用户总数量


        return [
            'amount' => $user->amount,
            'total_spread' => number_format($totalSpread, 2),
            'spread_30_days' => $spread30,
            'invite_count' => $user->invite_count,
            'amount_img' => $user->amount_img,
        ];
    }

    //推广员提现
    public function handleWithdraw($request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::where('id', $request->auth_user->id)->firstOrFail();
            if ($user->amount < $request->amount) {
                throw new \Exception('佣金余额不足');
            }
            $user->amount -= $request->amount;
            $user->amount_img = $request->qr_code;
            $user->save();
            Spread::create([
                'user_id' => $request->auth_user->id,
                'amount_img' => $request->qr_code,
                'amount' => $request->amount,
                'spread_type' => 2,
                'mch_id' => $request->mch->id,
                'is_status' => 0,
                'remark' => '推广员申请提现',
            ]);
        });
    }

    //获取推广员提现记录
    public function getSpreads($request)
    {
        $data = Spread::where('user_id', $request->auth_user->id)
            ->get();
        return $data;
    }
    /**
     * 处理订单支付成功后的返佣逻辑
     *
     * @param Order $order
     * @return void
     */
    public function processCommission(Order $order): void
    {
        // 使用 Eloquent 关联加载商户和用户信息，可以提高性能
        // 建议在 Order 模型中定义好 mch() 和 user() 的关联关系
        $mch = Mch::find($order->mch_id);
        $user = User::find($order->user_id);



        // 检查商户是否开启返佣，以及下单用户是否存在
        if (!$mch || !$user || !$mch->is_spread || $mch->spread_value <= 0) {
            return;
        }

        // 检查用户是否有推荐人
        if (!$user->ask_id) {
            return;
        }

        // 找到推荐人
        $referrer = User::find($user->ask_id);


        if (!$referrer) {
            return;
        }

        // 计算返佣金额
        $commissionAmount = round($order->amount * $mch->spread_value / 100, 2);
        if ($commissionAmount > 0) {

            // 增加推荐人余额 (使用 increment 保证原子性)
            $referrer->increment('amount', $commissionAmount);
            // 创建返佣记录
            Spread::create([
                'user_id'    => $referrer->id,
                'tg_user_id' => $user->id,
                'amount'     => $commissionAmount,
                'mch_id'     => $order->mch_id,
                'spread_type' => 1,
                'is_status' => 1,
                'remark'     => sprintf(
                    '用户[%s]消费%s元，返佣%s元',
                    $user->phone,
                    $order->amount,
                    $commissionAmount
                ),
            ]);
        }
    }
}
