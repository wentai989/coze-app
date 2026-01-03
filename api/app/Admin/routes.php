<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::get('/admin', fn() => \Slowlyo\OwlAdmin\Admin::view());

Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->resource('dashboard', \App\Admin\Controllers\HomeController::class);
    $router->resource('system/settings', \App\Admin\Controllers\SettingController::class);
    //获取智能体
    $router->post('knot-agent/get-agent-by-space-id', [\App\Admin\Controllers\KontController::class, 'getAgentBySpaceId']);
});
