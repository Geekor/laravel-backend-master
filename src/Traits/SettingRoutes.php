<?php

namespace Geekor\BackendMaster\Traits;

use Exception;
use Illuminate\Support\Arr;

trait SettingRoutes
{
    /**
     * 添加到 路由中间件列表
     */
    public function appendToRouteMiddleware($key, $middle)
    {
        if (Arr::has($this->routeMiddleware, $key)) {
            throw new Exception("route middleware key [$key] already exists!");
        }

        $this->routeMiddleware[$key] = $middle;

        $this->syncMiddlewareToRouter();

        return $this;
    }

    /**
     * 添加到 路由组，没有则添加新组名
     */
    public function appendMiddlewareToGroupAuto($group, $middleware)
    {
        if (! isset($this->middlewareGroups[$group])) {
            $this->middlewareGroups[$group] = [];
        }

        if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
            $this->middlewareGroups[$group][] = $middleware;
        }

        $this->syncMiddlewareToRouter();

        return $this;
    }
}