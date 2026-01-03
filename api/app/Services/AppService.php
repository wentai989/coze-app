<?php

namespace App\Services;

use App\Models\App;
use App\Models\AppLog;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 应用设置
 *
 * @method App getModel()
 * @method App|\Illuminate\Database\Query\Builder query()
 */
class AppService extends AdminService
{
	protected string $modelName = App::class;

	/**
	 * deleted 钩子 (执行于删除后)
	 *
	 * @param $ids
	 *
	 * @return void
	 */
	public function deleted($ids)
	{

		appLog::whereIn('app_id', explode(',', $ids))->delete();
	}
}
