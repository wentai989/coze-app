<?php

namespace App\Services;

use App\Models\Agent;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 智能体设置
 *
 * @method Agent getModel()
 * @method Agent|\Illuminate\Database\Query\Builder query()
 */
class AgentService extends AdminService
{
	protected string $modelName = Agent::class;
}