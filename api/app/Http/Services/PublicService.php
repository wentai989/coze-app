<?php

namespace App\Http\Services;

use App\Models\Vip;

use App\Models\VipPay;
use App\Models\WechatApp;
use Illuminate\Support\Str;
use App\Models\UserExtend;
use App\Models\KontAgent;
use App\Http\Models\User as HttpUser;
use App\Models\User;
use App\Models\Banner;
use App\Models\Categorie;



class PublicService
{
    public function getBanners($request)
    {
        $banner = Banner::where('is_status', 1)->where('mch_id', $request->mch->id)->orderBy('sort', 'desc')->get();
        return $banner;
    }

    public function getCategories($request)
    {
        return Categorie::where('is_status', 1)->where('mch_id', $request->mch->id)->orderBy('sort', 'desc')->get();
    }
    //获取分享设置
    public function getShareSetting($request)
    {

        return [
            'title' => $request->mch->share['title'] ?? '未设置',
            'image' => $request->mch->share['poster'] ?? '',
            'path' => '/pages/app/index',
        ];
    }
}
