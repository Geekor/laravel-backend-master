<?php

namespace Geekor\BackendMaster\Tests\Base\Traits;

trait AuthValidByNormalUser
{
    use AuthUserHelper;

    /**
     * 使用普通用户身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_normal_user_token_with_right_permissions()
    {
        $this->callApiByNormalUserToken(true);
    }

}
