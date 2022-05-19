<?php

namespace Geekor\BackendMaster\Tests\Base;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
// 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Tests\TestCase;

use Geekor\BackendMaster\Tests\Base\Contracts\ApiTestable;

class TestNormalCase extends TestCase implements ApiTestable
{
    use WithFaker;

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = false;

    /** 标记当前 API 是否需要特定的角色/权限才能访问 */
    protected $my_guard_need_permission = false;

    /** 需要的特定角色/权限 */
    protected $my_guard_roles = [];
    protected $my_guard_permissions = [];

    /** 当前测试的 API 信息 */
    protected $my_testing_api = ''; // /api/backend/auth/info
    protected $my_testing_method = ''; // get,post,put,delete
    protected $my_testing_params = [];

    //////////////////////////////////////////////////////////////////////
    // JUST HOOK
    public function createApplication()
    {
        // 每一个 `test_xxx` 用例都会调用这个基本函数
        // 来生成测试用的独立的 app 进程。
        //
        // 你可以在这里做 HOOK 处理

        $this->app = parent::createApplication();

        return $this->app;
    }

    //////////////////////////////////////////////////////////////////////

    /**
     * 用于判断当前这个 API 是否为管理员身份才能请求的
     */
    public function isMasterGuard(): bool
    {
        return $this->my_guard_is_master;
    }

    /**
     * 用于判断是否需要特定 角色/权限 才能访问
     */
    public function isPermissionRequired(): bool
    {
        return $this->my_guard_need_permission;
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
     * 当前 API 请求时要用到的参数
     * 例如：
     *
     * [
     *     'foo' => 'bar'
     * ]
     *
     */
    public function myTestingParams(): array
    {
        return $this->my_testing_params;
    }
}
