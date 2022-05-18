<?php

use Illuminate\Support\Facades\Route;

use Geekor\BackendMaster\Http\Controllers\Api\Auth\BackendAuthController;
use Geekor\BackendMaster\Http\Controllers\Api\Member\MasterController;
use Geekor\BackendMaster\Http\Controllers\Api\Member\RoleController;
use Geekor\BackendMaster\Http\Controllers\Api\Member\PermissionController;
use Geekor\BackendMaster\Http\Controllers\Api\Member\UserController;

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

/*
|--------------------------------------------------------------------------
| 用户认证  /api/backend/auth
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => vsprintf('/api/%s/auth',[$API_PREFIX])], function() {

    // 登入
    Route::post('/login', BackendAuthController::class.'@login');

    // [认证后访问] ------------------------------------------
    Route::middleware('auth:master')->group(function(){

        // 登出
        Route::delete('/me', BackendAuthController::class.'@logout');

        // 用户账户信息（不含详细资料）
        Route::get('/info', BackendAuthController::class.'@info');
    });
});

/*
|--------------------------------------------------------------------------
| 角色 /api/backend/roles
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => vsprintf('/api/%s',[$API_PREFIX]),
    'middleware' => 'auth:master'
],function() {

    Route::get('role-options', RoleController::class.'@roleOptions');
    Route::get('roles', RoleController::class.'@index');
    Route::get('roles/{id}', RoleController::class.'@show');
    Route::put('roles/{id}', RoleController::class.'@update');
    Route::post('roles', RoleController::class.'@store');
});

/*
|--------------------------------------------------------------------------
| 权限 /api/backend/permissions
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => vsprintf('api/%s',[$API_PREFIX]),
    'middleware' => 'auth:master'
], function() {

    Route::get('permissions', PermissionController::class.'@index');
});

/*
|--------------------------------------------------------------------------
| 管理员 /api/backend/masters
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => vsprintf('api/%s',[$API_PREFIX]),
    'middleware' => 'auth:master'
], function() {

    Route::get('masters', MasterController::class.'@index');
    Route::post('masters', MasterController::class.'@store');
    Route::get('masters/{id}/permissions', MasterController::class.'@userHasPermissions');

    //...获取管理员资料
    // Route::get('masters/{id}/profile', 'ProfileController@showAdmin');
});

/*
|--------------------------------------------------------------------------
| 普通用户 /api/backend/users
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => vsprintf('api/%s',[$API_PREFIX]),
    'middleware' => 'auth:master'
], function(){

    Route::get('users', UserController::class.'@index');
    Route::get('users/filter', UserController::class.'@filters');
    Route::post('users', UserController::class.'@store');
    Route::put('users/{id}/banned', UserController::class.'@banUser');
    Route::put('users/{id}/roles', UserController::class.'@updateRoles');
    Route::get('users/{id}/permissions', UserController::class.'@userHasPermissions');
    // Route::post('users/{id}/remove_data', UserController::class.'@removeData');
    // Route::delete('users/{id}', UserController::class.'@destroy');
});
