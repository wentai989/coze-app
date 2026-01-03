<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 算力管理
 */
class ComputePower extends Model
{

	protected $table = 'compute_powers';
	public function mch()
	{
		return $this->belongsTo(Mch::class);
	}
}
