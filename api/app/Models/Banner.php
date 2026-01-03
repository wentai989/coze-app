<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 轮播图管理
 */
class Banner extends Model
{


    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }
}
