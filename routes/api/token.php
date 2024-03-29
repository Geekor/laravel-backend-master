<?php

use Illuminate\Support\Facades\Route;

use Geekor\BackendMaster\Http\Controllers\Api\Auth\TokenController;

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

$API_PREFIX = config('bm.prefix', 'backend');

Route::group([
    'prefix' => '/api/' . $API_PREFIX,
    'middleware' => ['auth:master', 'bm.login']
], function() {

    //... 生成新的 token （旧的依旧会有用）
    Route::post('/tokens', [TokenController::class, 'createToken']);

    //... 获取所有 有效的 token
    Route::get('/tokens', [TokenController::class, 'tokens']);

    //... 删除指定 token
    Route::delete('/tokens/{id}', [TokenController::class, 'removeToken']);

    //... 删除所有 token
    Route::delete('/tokens', [TokenController::class, 'removeAllTokens']);

});

Route::group([
    'prefix' => '/api',
    'middleware' => ['auth:user', 'bm.login']
], function() {

    //... 生成新的 token （旧的依旧会有用）
    Route::post('/tokens', [TokenController::class, 'createToken']);

    //... 获取所有 有效的 token
    Route::get('/tokens', [TokenController::class, 'tokens']);

    //... 删除指定 token
    Route::delete('/tokens/{id}', [TokenController::class, 'removeToken']);

    //... 删除所有 token
    Route::delete('/tokens', [TokenController::class, 'removeAllTokens']);

});
