<?php

namespace Geekor\BackendMaster\Http\Middleware;

use Closure;
use Geekor\BackendMaster\Consts as BM;
use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\User;
use Geekor\Core\Exceptions\InputException;
use Geekor\Core\Exceptions\PermissionException;
use Geekor\Core\Support\GkApi;
use Illuminate\Support\Arr;

class AppAccessCheck
{
    /**
     * app-mark => 权限
     * -------------
     * w-x   web app
     * a-x   android app
     * i-x   ios app
     */
    const APP_PERMISSION_MAP = [
        'w-sti' => 'master:wapp-master-login',

        'w-pets' => '',
        'a-pets' => '',
    ];

    /**
     * 用于 APP 登录权限管理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!config('bm.use_app_access_check', false)) {
            return $next($request);
        }

        $app_mark = strtolower( $request->header('app-mark', '') );
        if (empty($app_mark)) {
            throw new InputException(GkApi::API_PARAM_MISS, BM::tr('api.no_app_mark_in_headers'));
        }
        //
        if (! Arr::has(self::APP_PERMISSION_MAP, $app_mark)) {
            throw new InputException(GkApi::API_PARAM_ERROR,
                BM::tr('api.bad_app_mark_in_headers', ['app_mark' => $app_mark]
            ));
        }

        $auth = auth();
        if ($auth->guest()) {
            // ???? 不可能出现的阿 >??
            throw new PermissionException('??? have not setting token check ??');
        }

        $needed_permission = Arr::get(self::APP_PERMISSION_MAP, $app_mark);
        if (empty($needed_permission)) {
            return $next($request);
        }

        //--------------------
        $user = $auth->user();

        if ($user instanceof User) {
            // 下面的方法可以自动抛出异常，不用担心
            $user->hasPermissionTo($needed_permission);

        } else if ($user instanceof Master) {
            // 下面的方法可以自动抛出异常，不用担心
            $user->hasPermissionTo($needed_permission);

        } else {
            throw new PermissionException('??? 未知类型的用户，开发者需要来处理以下 ??');
        }

        return $next($request);
    }

    // public function terminate($request, $response)
    // {
    // }
}
