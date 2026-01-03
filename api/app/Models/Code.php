<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 卡密管理
 */
class Code extends Model
{
    protected $table = 'codes';

    protected $guarded = ['id'];
    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
