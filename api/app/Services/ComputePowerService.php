<?php

namespace App\Services;

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
class ComputePowerService extends AdminService
{
	protected string $modelName = ComputePower::class;
}
