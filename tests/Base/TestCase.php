<?php

namespace Geekor\BackendMaster\Tests\Base;

// 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Tests\TestCase as AppTestCase;

use Geekor\BackendMaster\Tests\Base\ApiTestable;

class TestCase extends AppTestCase implements ApiTestable
{
    /**
     * 用于判断当前这个 API 是否为管理员身份才能请求的
     */
    public function isMasterGuard(): bool
    {
        return $this->my_guard_is_master;
    }

    /**
     * 当前帐号登录的设备名
     */
    public function myDeviceName(): string
    {
        return $this->my_device_name;
    }

    /**
     * 当前要请求的 API
     * 例如： /api/backend/test
     */
    public function myTestingApi(): string
    {
        return $this->my_testing_api;
    }

    /**
     * 当前 API 请求时使用的方法（小写）
     * 例如： get
     */
    public function myTestingMethod(): string
    {
        return $this->my_testing_method;
    }

    /**
     * 当前 API 请求时要用到的参数(可以写多个)
     * 例如：
     *
     * [
     *   'get' => [
     *     'foo' => 'bar'
     *   ]
     * ]
     *
     */
    public function myTestingParams(): array
    {
        return $this->my_testing_params;
    }
}
