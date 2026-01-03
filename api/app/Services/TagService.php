<?php

namespace App\Services;

use App\Models\Tag;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 标签管理
 *
 * @method Tag getModel()
 * @method Tag|\Illuminate\Database\Query\Builder query()
 */
class TagService extends AdminService
{
	protected string $modelName = Tag::class;
}