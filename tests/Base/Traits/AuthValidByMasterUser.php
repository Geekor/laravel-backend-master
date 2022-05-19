<?php

namespace Geekor\BackendMaster\Tests\Base\Traits;

trait AuthValidByMasterUser
{
    use AuthUserHelper;

    /**
     * 使用管理员身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_master_user_token_with_right_permissions()
    {
        $this->callApiByMasterUserToken(true);
    }

}
