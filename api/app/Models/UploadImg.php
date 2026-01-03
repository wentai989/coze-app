<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 文件上传日志
 */
class UploadImg extends Model
{

	protected $table = 'upload_imgs';
	protected $guarded = ['id'];
}
