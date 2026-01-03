<?php

namespace App\Services;

use App\Models\UploadImg;
use Slowlyo\OwlAdmin\Services\AdminService;

/**
 * 文件上传日志
 *
 * @method UploadImg getModel()
 * @method UploadImg|\Illuminate\Database\Query\Builder query()
 */
class UploadImgService extends AdminService
{
	protected string $modelName = UploadImg::class;
}