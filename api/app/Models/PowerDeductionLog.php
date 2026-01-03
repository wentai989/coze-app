<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 算力使用记录
 */
class PowerDeductionLog extends Model
{

	protected $table = 'power_deduction_logs';

	protected $guarded = ['id'];

	/**
	 * 会员
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * 商户
	 */
	public function mch()
	{
		return $this->belongsTo(Mch::class);
	}
}
