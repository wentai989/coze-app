<?php

namespace App\Http\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserExtend;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;
    // use HasApiTokens;



    protected $appends = ['is_sign', 'is_vip'];

    public function getPhoneAttribute($value)
    {
        return substr_replace($value, '****', 3, 4);
    }


    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getIsSignAttribute()
    {
        return $this->sign_at === Carbon::today()->toDateString();
    }

    public function getIsVipAttribute()
    {
        return $this->vip_expire_time >= Carbon::today()->toDateString();
    }


    protected $hidden = [
        // 'created_at',
        'updated_at',
        'openid',
        'remember_token',
    ];
}
