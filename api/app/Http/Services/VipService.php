<?php

namespace App\Http\Services;

use App\Models\Vip;
use Slowlyo\OwlAdmin\Services\AdminService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Services\WechatPayService;
use App\Models\User;
use Illuminate\Support\Facades\DB;



/**
 * 会员管理
 *
 * @method Vip getModel()
 * @method Vip|\Illuminate\Database\Query\Builder query()
 */
class VipService
{


	/**
	 * 获取所有vip 套餐
	 *
	 * @return array
	 */
	public function vips($request)
	{
		return Vip::where('is_status', 1)->where('mch_id', $request->mch->id)->get()->toArray();
	}

	/**
	 * 充值vip
	 *
	 * @param Request $request
	 * @return array
	 */
	public function pay($request)
	{


		return DB::transaction(function () use ($request) {
			$vip = Vip::findOrFail($request->id);

			$order = new Order();
			$order->amount = $vip->amount;
			$order->name = $vip->name;
			$order->order_type = 2;
			$order->user_id = $request->auth_user->id;
			$order->power_value = $vip->power_value;
			$order->day_number = $vip->day_number;
			$order->mch_id = $request->mch->id;
			$order->order_no = date('YmdHis') . $request->auth_user->id . rand(1000, 9999);
			$order->save();

			$result =	(new WechatPayService())->miniProgramPay(
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

		$user = User::where('id', $order->user_id)->first();



		if ($user->vip_expire_time < date('Y-m-d H:i:s')) {
			$user->vip_expire_time = date('Y-m-d H:i:s', strtotime('+' . $order->day_number . ' days'));
		} else {
			$user->vip_expire_time = date('Y-m-d H:i:s', strtotime($order->vip_expire_time . ' +' . $order->day_number . ' days'));
		}

		if ($order->power_value > 0) {
			$user->power_value += $order->power_value;
		}
		$user->save();

		return	$order->save();
	}
}
