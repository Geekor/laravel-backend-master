<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\BackendMaster\Tests\Base\Traits\AuthInvalidCheck;

class MesterGetInfoTest extends TestAuthCase
{
    use AuthInvalidCheck;

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;
    /** 标记当前 API 是否需要特定的角色/权限才能访问 */
    protected $my_guard_need_permission = false;
    /** 需要的特定角色/权限 */
    protected $my_guard_roles = [];
    protected $my_guard_permissions = [];

    /** 当前测试的 API */
    protected $my_testing_api = '/api/backend/auth/info';
    protected $my_testing_method = 'get';
    protected $my_testing_params = [];

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */

    public function test_call_of_getting_info_success()
    {
        $this->callApiByMasterUser($this->myTestingParams(), function($resp) {
            $resp->assertOk()->assertJsonStructure([
                'id', 'username', 'name'
            ]);
        });
    }
}
