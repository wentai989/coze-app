<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 推广记录
 */
class Spread extends Model
{
    protected $guarded = [];
    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function tg_user()
    // {
    //     return $this->belongsTo(User::class, 'tg_user_id');
    // }
}
