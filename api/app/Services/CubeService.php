<?php

namespace App\Services;

use App\Models\Cube;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 魔方管理
 *
 * @method Cube getModel()
 * @method Cube|\Illuminate\Database\Query\Builder query()
 */
class CubeService extends AdminService
{
	protected string $modelName = Cube::class;

	public function cubes()
	{
		return $this->query()
			->where('is_status', 1)
			->orderBy('sort', 'desc')
			->get();
	}
}
