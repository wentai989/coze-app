<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 会员管理
 */
class Vip extends Model
{
    protected $table = 'vips';

    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }
}
