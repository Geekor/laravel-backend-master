<?php

namespace Geekor\BackendMaster\Http\Traits;

use Illuminate\Http\Request;

use Geekor\Core\Exceptions\PermissionException;

trait PermissionCheck
{
    /**
     * 获取当前认证的用户实例
     * -------------------
     * 如果为 null，则直接报 401 错误
     *
     * @return 当前登录的用户
     */
    protected function user()
    {
        // 其实在路由配置中已经通过 middleware 拦截过一次，
        // 这里再验证一次做保险而已。
        if (! $user = auth()->user()) {
            throw new PermissionException();
        }

        return $user;
    }

    /**
     * 获取当前用户，并根据角色拦截
     * -------------------------
     *
     * @param mixed $roles 可以是单个角色，也可以是一个数组
     * @return 当前登录的用户
     */
    protected function getUserAndCheckHasRole($roles, $message = null)
    {
        if ($user = $this->user()) {
            if (!$user->hasAnyRole($roles)) {
                throw new PermissionException($message);
            }
        } else {
            throw new PermissionException($message);
        }

        return $user;
    }

    /**
     * 获取当前用户，并根据权限拦截
     * ------------------------
     */
    protected function getUserAndCheckHasPermission($permission, $message = null)
    {
        if ($user = $this->user()) {
            if (!$user->hasPermissionTo($permission)) {
                throw new PermissionException($message);
            }
        } else {
            throw new PermissionException($message);
        }

        return $user;
    }
}
