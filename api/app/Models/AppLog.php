<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 对话记录
 */
class AppLog extends Model
{

	protected $table = 'app_logs';
	protected $guarded = ['id'];
	protected $casts = ['contents' => 'array', 'outputs' => 'array'];
	public function app()
	{
		return $this->belongsTo(App::class);
	}
}
