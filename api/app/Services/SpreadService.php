<?php

namespace App\Services;

use App\Models\Spread;
use App\Models\User;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 推广记录
 *
 * @method Spread getModel()
 * @method Spread|\Illuminate\Database\Query\Builder query()
 */
class SpreadService extends AdminService
{
	protected string $modelName = Spread::class;


	public function saved($model, $isEdit = false)
	{
		//返款
		if ($model->is_status == 2 && $model->spread_type == 2) {
			//拒绝返款，减少用户余额
			$user = User::find($model->user_id);
			if ($user) {
				$user->increment('amount', $model->amount);
				$user->save();
			}
		}
	}
}
