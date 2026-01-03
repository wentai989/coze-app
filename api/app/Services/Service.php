<?php

namespace App\Services;

use App\Models\;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 智能体设置
 *
 * @method  getModel()
 * @method |\Illuminate\Database\Query\Builder query()
 */
class Service extends AdminService
{
	protected string $modelName = ::class;
}