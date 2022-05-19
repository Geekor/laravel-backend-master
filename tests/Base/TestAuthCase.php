<?php

namespace Geekor\BackendMaster\Tests\Base;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\User;
use Geekor\BackendMaster\Tests\Base\Contracts\ApiTestable;
use Geekor\BackendMaster\Tests\Base\Contracts\ApiUserMakable;
use Geekor\BackendMaster\Tests\Base\Traits\ApiPrefixUtil;

class TestAuthCase extends TestNormalCase implements ApiUserMakable
{
    use WithFaker;
    use ApiPrefixUtil;

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;

    ////////////////////////////////////////

    // JUST HOOK & FIX BACKEND API PREFIX
    public function createApplication()
    {
        // 每一个 `test_xxx` 用例都会调用这个基本函数
        // 来生成测试用的独立的 app 进程。
        //
        // 你可以在这里做 HOOK 处理

        $this->app = parent::createApplication();

        $this->fixApiBackendPrefix();

        return $this->app;
    }

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
}
