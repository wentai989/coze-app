<?php

namespace App\Http\Services;

use App\Models\Order;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 订单记录
 *
 * @method Order getModel()
 * @method Order|\Illuminate\Database\Query\Builder query()
 */
class OrderService
{


	public function orders($request)
	{
		$orders = Order::where('user_id', $request->auth_user->id)->where('is_status', 1)->orderBy('id', 'desc')->get();
		return $orders;
	}
}
