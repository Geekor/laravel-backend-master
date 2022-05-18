<?php

namespace Geekor\BackendMaster\Tests\Feature\Traits;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\User;

trait AuthTokenCheck
{
    /**
     * headers 不带 TOKEN
     */
    public function test_call_api_without_token()
    {
        if ($this->my_testing_method === 'get') {
            $resp = $this->getJson($this->my_testing_api);
            $resp->assertUnauthorized();
        }
    }

    /**
     * 使用错误的 TOKEN
     */
    public function test_call_api_by_bad_token()
    {
        if ($this->my_testing_method === 'get') {
            $resp = $this->withToken('frowqjfemo023ur')->getJson($this->my_testing_api);
            $resp->assertUnauthorized();
        }
    }

    /**
     * 使用普通用户身份的 TOKEN
     */
    public function test_call_api_by_normal_user_token()
    {
        if ($this->my_testing_method === 'get') {
            $user = User::factory()->create();
            $token = $user->createPlainTextToken($this->my_device_name);

            $resp = $this->withToken($token)->getJson($this->my_testing_api);
            $resp->assertUnauthorized();
        }
    }

    /**
     * 使用管理员身份的 TOKEN
     */
    public function test_call_api_by_master_user_token()
    {
        if ($this->my_testing_method === 'get') {
            $user = Master::factory()->create();
            $token = $user->createPlainTextToken($this->my_device_name);

            $resp = $this->withToken($token)->getJson($this->my_testing_api);

            $resp->assertOk()->assertJsonStructure(['id', 'username', 'name']);
        }
    }
}
