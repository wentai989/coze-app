<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 商户名称管理
 */
class Mch extends Model
{

	protected $table = 'mchs';
	protected $guarded = ['id'];

	protected $casts = [
		'power' => 'array',
		'pay' => 'array',
		'mini' => 'array',
		'share' => 'array',
	];
}
