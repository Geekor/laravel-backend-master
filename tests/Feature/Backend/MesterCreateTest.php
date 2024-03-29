<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Models\Master;
use Illuminate\Support\Str;

use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\BackendMaster\Tests\Base\Traits\AuthInvalidCheck;
use Geekor\Core\Support\GkTestUtil;

class MesterCreateTest extends TestAuthCase
{
    use AuthInvalidCheck;

    // 下面属性的更多说明可查看 /tests/Base/TestCase.php

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;
    /** 标记当前 API 是否需要特定的角色/权限才能访问 */
    protected $my_guard_need_permission = true;
    /** 需要的特定角色/权限 */
    protected $my_guard_roles = [];
    protected $my_guard_permissions = ['master:user-a'];

    /** 当前测试的 API */
    protected $my_testing_api = '/api/backend/masters';
    protected $my_testing_method = 'post';
    protected $my_testing_params = [];

    public function __construct()
    {
        parent::__construct();

        // 因为使用到函数 只能在构造函数时配置 post 参数
        //
        // 这些参数用于生成新账号
        $this->my_testing_params = [
            'username' => Str::random(12),
            'password' => Str::random(8),
            'role' => 'data_master'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */

    public function test_call_api_of_creating_new_master_success()
    {
        $this->callApiByMasterUserToken(true, null, function($resp) {
            $this->assertTrue(GkTestUtil::isAcceptableStatus($resp, [
                200, 201
            ]));

            Master::where('username', $this->my_testing_params['username'])->delete();
        });
    }
}
