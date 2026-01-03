<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 分类管理
 */
class Categorie extends Model
{

    protected $table = 'categories';
    protected $guarded = ['id'];
    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }
}
