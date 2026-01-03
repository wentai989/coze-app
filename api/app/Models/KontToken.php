<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 扣子token日志
 */
class KontToken extends Model
{
	// use SoftDeletes;

	protected $table = 'kont_tokens';
	protected $fillable = ['app_id', 'token', 'expires_in'];
}
