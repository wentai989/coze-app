<?php

namespace App\Models;

use Slowlyo\OwlAdmin\Models\BaseModel as Model;

/**
 * 应用设置
 */
class App extends Model
{

    protected $casts = [
        'launch_params' => 'array',
        'previews' => 'array',
        'configs' => 'array',
        'output_params' => 'array',
    ];

    protected $guarded = [];
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function mch()
    {
        return $this->belongsTo(Mch::class);
    }

    public function agentLog()
    {
        return $this->hasOne(AppLog::class, 'app_id', 'id')->where('log_type', 'agent');
    }
    public function kont()
    {
        return $this->belongsTo(Kont::class);
    }
}
