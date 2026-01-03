<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 订单记录
 */
class Order extends Model
{

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
