<?php

namespace App\Services;

use App\Models\PowerDeductionLog;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 算力使用记录
 *
 * @method PowerDeductionLog getModel()
 * @method PowerDeductionLog|\Illuminate\Database\Query\Builder query()
 */
class PowerDeductionLogService extends AdminService
{
	protected string $modelName = PowerDeductionLog::class;
}