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
        $access_permission_map = config('bm.app_access_permission', []);
        if (! Arr::has($access_permission_map, $app_mark)) {
            throw new InputException(GkApi::API_PARAM_ERROR,
                BM::tr('api.bad_app_mark_in_headers', ['app_mark' => $app_mark]
            ));
        }

        $auth = auth();
        if ($auth->guest()) {
            // ???? 不可能出现的阿 >??
            throw new PermissionException('??? have not setting token check ??');
        }

        $needed_permission = Arr::get($access_permission_map, $app_mark);
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
