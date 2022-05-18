<?php

namespace Geekor\BackendMaster\Tests\Feature\Traits;

use Illuminate\Support\Str;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\User;

trait AuthTokenCheck
{
    /**
     * headers 不带 TOKEN
     */
    public function test_call_api_without_token()
    {
        if ($this->myTestingMethod() === 'get') {
            $resp = $this->getJson($this->myTestingApi());
            $resp->assertUnauthorized();
        }
    }

    /**
     * 使用错误的 TOKEN
     */
    public function test_call_api_by_bad_token()
    {
        if ($this->myTestingMethod() === 'get') {
            $token = Str::random(10);

            $resp = $this->withToken($token)->getJson($this->myTestingApi());
            $resp->assertUnauthorized();
        }
    }

    /**
     * 使用普通用户身份的 TOKEN
     */
    public function test_call_api_by_normal_user_token()
    {
        if ($this->myTestingMethod() === 'get') {
            $arr = $this->makeNormalUserAndToken();

            $resp = $this->withToken($arr['token'])->getJson($this->myTestingApi());

            if ($this->isMasterGuard()) {
                $resp->assertUnauthorized();
            } else {
                $resp->assertOk();
            }
        }
    }

    /**
     * 使用管理员身份的 TOKEN
     */
    public function test_call_api_by_master_user_token()
    {
        if ($this->myTestingMethod() === 'get') {
            $arr = $this->makeMasterUserAndToken();

            $resp = $this->withToken($arr['token'])->getJson($this->myTestingApi());

            if ($this->isMasterGuard()) {
                $resp->assertOk();
            } else {
                $resp->assertUnauthorized();
            }
        }
    }
}
