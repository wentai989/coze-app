<?php

namespace App\Services;

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
class VipService extends AdminService
{
	protected string $modelName = Vip::class;
}
