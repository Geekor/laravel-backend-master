<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Models\Role;
use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\BackendMaster\Tests\Base\Traits\AuthInvalidCheck;
use Geekor\BackendMaster\Tests\Base\Traits\AuthValidByMasterUser;
use Geekor\Core\Support\GkApi;

class RoleUpdateTest extends TestAuthCase
{
    use AuthInvalidCheck;
    use AuthValidByMasterUser;

    // 下面属性的更多说明可查看 /tests/Base/TestCase.php

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;
    /** 标记当前 API 是否需要特定的角色/权限才能访问 */
    protected $my_guard_need_permission = true;
    /** 需要的特定角色/权限 */
    protected $my_guard_roles = ['super_master'];
    protected $my_guard_permissions = [];

    /** 当前测试的 API */
    protected $my_testing_api = '/api/backend/roles/1';
    protected $my_testing_method = 'put';
    protected $my_testing_params = [
        'title' => '普通用户',
        'level' => 10,
        'description' => '测试修改'
    ];

    /*
    |--------------------------------------------------------------------------
    | 角色信息可以被修改的选项： 'title', 'level', 'description', 'permissions'
    |--------------------------------------------------------------------------
    */

    public function test_call_api_without_level_param()
    {
        $this->callApiByMasterUser([
            'title' => '普通用户',
            'description' => '测试修改'
        ], function($resp) {
            $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
        });
    }

    public function test_call_api_without_title_param()
    {
        $this->callApiByMasterUser([
            'level' => 1,
            'description' => '测试修改'
        ], function($resp) {
            $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
        });
    }

    public function test_call_api_without_description_param()
    {
        $this->callApiByMasterUser([
            'title' => '普通用户',
            'level' => 1,
        ], function($resp) {
            $resp->assertOk();
        });
    }

    public function test_do_illegal_action_of_setting_master_permission_on_normal_user_role()
    {
        //在【普通用户】角色上非法配置【管理员】才可以拥有的权限
        $this->callApiByMasterUser([
            'title' => '普通用户',
            'level' => 1,
            'permissions' => [ 'master:role-a' ]
        ], function($resp) {
            $resp->assertStatus(403)->assertJson(['code' => GkApi::FORBIDDEN]);
        });
    }

    public function test_call_api_of_changing_permissions()
    {
        // 调整权限配置
        $this->callApiByMasterUser([
            'title' => '普通用户',
            'level' => 1,
            'permissions' => [ 'user:article-b', 'user:article-r', 'user:article-a' ]
        ], function($resp) {
            $resp->assertOk();
        });
    }

    public function test_call_api_with_bad_method_of_delete()
    {
        // 调整权限配置
        $this->my_testing_method = 'delete';

        $this->callApiByMasterUser([], function($resp) {
            $resp->assertStatus(405)->assertJson(['code' => GkApi::METHOD_NOT_ALLOWED]);
        });
    }
}
