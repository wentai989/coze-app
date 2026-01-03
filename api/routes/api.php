<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\CubeController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\UserTokensValid;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ComputePowerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\SpreadController;
use App\Http\Controllers\KontController;
use App\Http\Controllers\AppLogController;
use App\Http\Controllers\PowerDeductionLogController;

use App\Http\Middleware\AppKeyValid;


//图标转换
Route::get('/icons', [PublicController::class, 'iconChange']);



//订单回调
Route::any('/order/{order_no}/notify', [OrderController::class, 'notify']);



Route::middleware(AppKeyValid::class)->group(function () {

    //获取商户名称配置
    Route::get('/mch', [PublicController::class, 'mch']);

    //获取分享标题
    Route::get('/share', [PublicController::class, 'share']);

    //获取所有banner
    Route::get('/banners', [PublicController::class, 'banners']);

    //用户登录
    Route::post('/wechat-login', [LoginController::class, 'wechatLogin']);
    //获取所有应用立方体
    Route::get('/cubes', [CubeController::class, 'index']);
    //获取所有应用
    Route::get('/apps', [AppController::class, 'index']);
    //获取单个应用
    Route::get('/app/{id}', [AppController::class, 'show']);
    //获取分类
    Route::get('/categories', [PublicController::class, 'categories']);
    //获取所有vip 套餐
    Route::get('/vips', [VipController::class, 'index']);
    //获取所有算力目录
    Route::get('/compute-powers', [ComputePowerController::class, 'index']);


    Route::middleware(UserTokensValid::class)->group(function () {

        //图片上传
        Route::post('/upload', [PublicController::class, 'upload']);

        //获取扣子token
        Route::post('/kont/{id}/token', [KontController::class, 'token']);
        //开始异步工作流
        Route::post('/app/{id}/work', [AppController::class, 'work']);
        //保存应用对话记录
        Route::post('/app/{id}/log', [AppLogController::class, 'store']);

        // //获取应用日志
        // Route::get('/app/{id}/log', [AppLogController::class, 'show']);


        //激活卡密
        Route::get('/code/{code}/activate', [CodeController::class, 'activate']);

        //激活历史
        Route::get('/code/history', [CodeController::class, 'history']);

        //推广员数据
        Route::get('/spread', [SpreadController::class, 'show']);

        //推广员提现
        Route::post('/spread/withdraw', [SpreadController::class, 'withdraw']);
        //获取推广员提现记录
        Route::get('/spread/logs', [SpreadController::class, 'index']);

        Route::get('/user', [UserController::class, 'show']);
        //签到
        Route::get('/user/sign', [UserController::class, 'sign']);

        //充值vip
        Route::post('/vip/{id}/pay', [VipController::class, 'pay']);

        //充值算力
        Route::post('/compute-power/{id}/pay', [ComputePowerController::class, 'pay']);

        //算力扣点
        Route::post('/power-deduction-log', [PowerDeductionLogController::class, 'store']);

        //获取算力消耗记录
        Route::get('/power-deduction-logs', [PowerDeductionLogController::class, 'index']);

        //获取订单列表
        Route::get('/orders', [OrderController::class, 'index']);

        //获取工作流异步结果
        Route::get('/app-log/{id}/work', [AppLogController::class, 'work']);

        //获取 对话记录
        Route::get('/app-logs', [AppLogController::class, 'index']);
    });
});
