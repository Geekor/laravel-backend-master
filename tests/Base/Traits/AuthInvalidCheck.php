<?php

namespace Geekor\BackendMaster\Tests\Base\Traits;

use Illuminate\Support\Str;

/**
 * 本模块只用于需要 token 验证的模块
 */
trait AuthInvalidCheck
{
    use AuthUserHelper;

    /**
     * headers 不带 TOKEN
     */
    public function test_call_api_without_token()
    {
        $token = null;
        if ($resp = $this->accessWithToken($token)) {
            $resp->assertUnauthorized();

        } else {
            var_dump($this->myTestingApi());
            $this->assertTrue(false);
        }
    }

    /**
     * 使用错误的 TOKEN
     */
    public function test_call_api_by_bad_token()
    {
        $token = Str::random(10);
        if ($resp = $this->accessWithToken($token)) {
            $resp->assertUnauthorized();

        } else {
            var_dump($this->myTestingApi());
            $this->assertTrue(false);
        }
    }

    /**
     * 使用普通用户身份的 TOKEN, 但不带角色/权限
     */
    public function test_call_api_by_normal_user_token_without_permissions()
    {
        $this->callApiByNormalUserToken(false);
    }

    /**
     * 使用管理员身份的 TOKEN, 但不带角色/权限
     */
    public function test_call_api_by_master_user_token_without_permissions()
    {
        $this->callApiByMasterUserToken(false);
    }

    // =====================

    private function accessWithToken($token)
    {
        $resp = null;

        (! empty($token)) && $resp = $this->withToken($token);

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = ($resp ?? $this)->getJson($this->myTestingApi(), $this->myTestingParams());
                break;

            case 'post':
                $resp = ($resp ?? $this)->postJson($this->myTestingApi(), $this->myTestingParams());
                break;

            case 'put':
                $resp = ($resp ?? $this)->putJson($this->myTestingApi(), $this->myTestingParams());
                break;

            case 'delete':
                $resp = ($resp ?? $this)->deleteJson($this->myTestingApi());
                break;
        }

        return $resp;
    }

}
