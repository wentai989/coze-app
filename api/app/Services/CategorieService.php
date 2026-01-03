<?php

namespace App\Services;

use App\Models\Categorie;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 分类管理
 *
 * @method Categorie getModel()
 * @method Categorie|\Illuminate\Database\Query\Builder query()
 */
class CategorieService extends AdminService
{
	protected string $modelName = Categorie::class;
}
