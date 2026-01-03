<?php

namespace App\Services;

use App\Models\Mch;
use Slowlyo\OwlAdmin\Services\AdminService;
use Illuminate\Support\Str;

/**
 * 商户名称管理
 *
 * @method Mch getModel()
 * @method Mch|\Illuminate\Database\Query\Builder query()
 */
class MchService extends AdminService
{
	protected string $modelName = Mch::class;





	public function saved($model, $isEdit = false)
	{
		if (!$model->app_key) {
			//生成唯一的app_key
			$model->app_key = Str::random(32);
		}


		$model->save();
	}
}
