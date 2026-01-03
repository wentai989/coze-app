<?php

namespace App\Services;

use App\Models\Order;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 订单记录
 *
 * @method Order getModel()
 * @method Order|\Illuminate\Database\Query\Builder query()
 */
class OrderService extends AdminService
{
	protected string $modelName = Order::class;
}
