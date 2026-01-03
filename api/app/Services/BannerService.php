<?php

namespace App\Services;

use App\Models\Banner;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 轮播图管理
 *
 * @method Banner getModel()
 * @method Banner|\Illuminate\Database\Query\Builder query()
 */
class BannerService extends AdminService
{
	protected string $modelName = Banner::class;
}
