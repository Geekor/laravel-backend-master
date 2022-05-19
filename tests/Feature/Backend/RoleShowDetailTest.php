<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\BackendMaster\Tests\Base\Traits\AuthInvalidCheck;

class RoleShowDetailTest extends TestAuthCase
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
    protected $my_guard_roles = ['super_master'];
    protected $my_guard_permissions = [];

    /** 当前测试的 API */
    protected $my_testing_api = '/api/backend/roles/1';
    protected $my_testing_method = 'get';
    protected $my_testing_params = [];

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */

    public function test_call_api_of_show_a_role_detail()
    {
        /*
        "role": {#3044
            +"id": 1
            +"name": "user"
            +"guard_name": "user"
            +"level": 1
            +"title": "用户"
            +"description": "普通（仅可浏览）用户"
            +"removable": 0
            +"created_at": "2022-05-18T12:40:49.000000Z"
            +"updated_at": "2022-05-18T12:40:49.000000Z"
            +"permissions": array:2 [
            0 => {#4544
                +"id": 13
                +"name": "user:article-b"
                +"guard_name": "user"
                +"title": "浏览文章"
                +"description": "浏览文章列表"
                +"removable": 0
                +"created_at": "2022-05-18T12:40:49.000000Z"
                +"updated_at": "2022-05-18T12:40:49.000000Z"
                +"pivot": {#1440
                +"role_id": 1
                +"permission_id": 13
                }
            }
            ...
            ]
        }
        +"permissions": array:5 [
            0 => {#1434
            +"id": 15
            +"name": "user:article-a"
            +"guard_name": "user"
            +"title": "添加文章"
            +"description": "创建文章"
            +"removable": 0
            +"created_at": "2022-05-18T12:40:49.000000Z"
            +"updated_at": "2022-05-18T12:40:49.000000Z"
            }
            ...
        ]
        }
        */
        $this->callApiByMasterUser($this->myTestingParams(), function($resp) {
            $resp->assertOk()->assertJsonStructure([
                'role', 'permissions'
            ]);
        });
    }
}
