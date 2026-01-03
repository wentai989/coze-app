<?php

namespace App\Http\Services;

use App\Models\ComputePower;
use App\Models\Order;
use App\Http\Services\WechatPayService;
use Slowlyo\OwlAdmin\Services\AdminService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * 算力管理
 *
 * @method ComputePower getModel()
 * @method ComputePower|\Illuminate\Database\Query\Builder query()
 */
class ComputePowerService
{

    /**
     * 获取所有算力目录
     *
     * @return array
     */
    public function computePowers($request)
    {
        return ComputePower::where('is_status', 1)->where('mch_id', $request->mch->id)->orderBy('sort', 'desc')->get()->toArray();
    }

    public function createComputePowerPay($request)
    {

        return DB::transaction(function () use ($request) {
            $computePower = ComputePower::findOrFail($request->id);

            $order = new Order();
            $order->amount = $computePower->amount;
            $order->name = $computePower->name;
            $order->order_type = 1;
            $order->user_id = $request->auth_user->id;
            $order->power_value = $computePower->power_value;
            $order->mch_id = $request->mch->id;
            $order->order_no = date('YmdHis') . $request->auth_user->id . rand(1000, 9999);
            $order->save();



            $result =    (new WechatPayService())->miniProgramPay(
                $order,
                $request->auth_user->openid,
                $request->root() . "/api/{$order->order_no}/notify",
                [
                    'app_id'         => $request->mch->mini['appid'] ?? '',
                    'secret'         => $request->mch->mini['appsecret'] ?? '',
                    'mch_id'         => $request->mch->pay['mch_id'] ?? '',
                    'mch_secret_key' => $request->mch->pay['secret_key'] ?? '',
                    'private_key'    => $request->mch->pay['key'] ?? '',
                    'certificate'    => $request->mch->pay['cert'] ?? '',
                ]
            );

            $result['order_no'] = $order->order_no;
            return $result;
        });
    }



    /**
     * 支付回调
     *
     * @param Order $order
     * @return void
     */
    public function payNotify(Order $order)
    {
        if ($order->is_status == 1) {
            throw new \Exception('订单已处理');
        }
        $order->is_status = 1;
        User::where('id', $order->user_id)->increment('power_value', $order->power_value);
        return    $order->save();
    }
}
