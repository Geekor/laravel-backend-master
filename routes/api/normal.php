<?php

use Illuminate\Support\Facades\Route;

use Geekor\BackendMaster\Http\Controllers\Api\Auth\NormalAuthController;

/*
|--------------------------------------------------------------------------
| 关于 guard 的一点说明
|--------------------------------------------------------------------------
|
| 'middleware' => 'auth:sanctum'，所有使用 sanctum 认证的都可以请求
| 'middleware' => 'auth:master'，只有 guard 使用 master 的请求才能被允许
| 'middleware' => 'auth:user'，只有 guard 使用 user 的请求才能被允许
|
| 那么如何获取 master guard 的 token？ 通过 masters 表的 Model 生成 token;
| 那么如何获取 user guard 的 token？ 通过 users 表的 Model 生成 token;
*/

Route::prefix('/api/auth')->group(function() {

    //... 邮箱方式注册
    Route::post('/email-register', NormalAuthController::class.'@register');

    //... 邮箱方式登录
    Route::post('/email-login', NormalAuthController::class.'@login');

    // -------------------------------
    Route::middleware('auth:user')->group(function() {

        // 账户信息
        Route::get('/info', NormalAuthController::class.'@info');

        // 登出
        Route::delete('/me', NormalAuthController::class.'@logout');
    });
});
