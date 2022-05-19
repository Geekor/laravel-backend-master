<?php

namespace Geekor\BackendMaster\Tests\Base;

use Exception;
use Illuminate\Foundation\Testing\WithFaker;

// 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Tests\TestCase;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\User;
use Geekor\BackendMaster\Tests\Base\Contracts\ApiTestable;
use Geekor\BackendMaster\Tests\Base\Contracts\ApiUserMakable;

class TestAuthCase extends TestCase implements ApiTestable, ApiUserMakable
{
    use WithFaker;

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;

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

    /**
     * 创建管理员并生成一个登录后的 token
     *
     * @param usePermission 是否分配定义中的权限配置
     */
    public function makeMasterUserAndToken($usePermission = null): array
    {
        $user = Master::factory()->create();

        if (is_null($usePermission)) {
            $usePermission = $this->isPermissionRequired();
        }

        if ($usePermission) {
            if (count($this->my_guard_roles) > 0) {
                $user->assignRole($this->my_guard_roles);
            }

            if (count($this->my_guard_permissions) > 0) {
                $user->givePermissionTo($this->my_guard_permissions);
            }
        }

        $token = $user->createPlainTextToken($this->myDeviceName());

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * 创建普通用户并生成一个登录后的 token
     *
     * @param usePermission 是否分配定义中的权限配置
     */
    public function makeNormalUserAndToken($usePermission = null): array
    {
        $user = User::factory()->create();

        if (is_null($usePermission)) {
            $usePermission = $this->isPermissionRequired();
        }

        if ($usePermission) {
            if (count($this->my_guard_roles) > 0) {
                $user->assignRole($this->my_guard_roles);
            }

            if (count($this->my_guard_permissions) > 0) {
                $user->givePermissionTo($this->my_guard_permissions);
            }
        }
        $token = $user->createPlainTextToken($this->myDeviceName());

        return [
            'user' => $user,
            'token' => $token
        ];
    }

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
