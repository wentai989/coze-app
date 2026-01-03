<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Slowlyo\OwlAdmin\Models\BaseModel as Model;


/**
 * 扣子空间
 */
class Kont extends Model
{




    // 添加全局作用域
    // protected static function booted()
    // {
    //     parent::booted();
    // }




    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }
}
